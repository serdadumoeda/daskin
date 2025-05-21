@extends('layouts.app')

@section('title', 'Dashboard Inspektorat Jenderal')
@section('page_title', 'Inspektorat Jenderal') {{-- Sudah menggunakan nama lengkap --}}

@section('header_filters')
    <form method="GET" action="{{ route('inspektorat.dashboard') }}" class="w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 items-end">
            <div class="flex-grow">
                <label for="year_filter_itjen" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                <select name="year_filter" id="year_filter_itjen" class="form-input mt-1 w-full bg-white">
                    @if ($availableYears->isEmpty())
                        <option value="{{ $selectedYear ?: date('Y') }}" selected>{{ $selectedYear ?: date('Y') }}</option>
                    @else
                        @foreach ($availableYears as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                {{ $year }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="flex-grow">
                <label for="month_filter_itjen" class="text-sm text-gray-600 whitespace-nowrap">Bulan:</label>
                <select name="month_filter" id="month_filter_itjen" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua Bulan</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ (int) $selectedMonth == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month((int) $i)->isoFormat('MMMM') }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex items-center space-x-2 pt-5">
                <button type="submit"
                    class="w-full sm:w-auto px-4 py-1.5 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
                    <i class="ri-filter-3-line mr-1"></i> Terapkan
                </button>
                <a href="{{ route('inspektorat.dashboard') }}"
                    class="w-full sm:w-auto px-4 py-1.5 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
                    Reset
                </a>
            </div>
        </div>
    </form>
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Baris untuk Kartu Ringkasan Statistik --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Kartu 1: Progres Tindak Lanjut Temuan BPK (Administratif) --}}
            <div class="bg-white p-5 rounded-lg shadow">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-sm font-medium text-gray-600">Tindak Lanjut Temuan BPK (Administratif)</h3>
                    <a href="{{ route('inspektorat.progress-temuan-bpk.index', ['jenis_temuan_filter' => 'administratif']) }}"
                        class="text-xs text-primary hover:text-primary/80">Lihat Detail &rarr;</a>
                </div>
                <div class="text-2xl font-semibold text-gray-800">
                    {{ number_format($persentaseSelesaiBpkAdmin, 2) }} % Selesai Ditindaklanjuti
                </div>
                {{-- <div class="mt-1">
                    <p
                        class="text-sm {{ $persentaseSelesaiBpkAdmin >= 75 ? 'text-green-500' : ($persentaseSelesaiBpkAdmin >= 50 ? 'text-yellow-500' : 'text-red-500') }}">
                        {{ number_format($persentaseSelesaiBpkAdmin, 2) }}% Selesai Ditindaklanjuti
                    </p>
                    <p class="text-xs text-gray-500">
                        ({{ number_format($summaryBpk->total_tl_admin_kasus ?? 0) }} dari
                        {{ number_format($summaryBpk->total_temuan_admin_kasus ?? 0) }} Kasus)
                    </p>
                </div> --}}
            </div>

            {{-- Kartu 3: Progres Tindak Lanjut Temuan Internal (Administratif) --}}
            <div class="bg-white p-5 rounded-lg shadow">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-sm font-medium text-gray-600">Tindak Lanjut Temuan Internal (Administratif)</h3>
                    <a href="{{ route('inspektorat.progress-temuan-internal.index', ['jenis_temuan_filter' => 'administratif']) }}"
                        class="text-xs text-primary hover:text-primary/80">Lihat Detail &rarr;</a>
                </div>
                <div class="text-2xl font-semibold text-gray-800">
                    {{ number_format($persentaseSelesaiInternalAdmin, 2) }}% Selesai Ditindaklanjuti
                </div>
                {{-- <div class="mt-1">
                    <p
                        class="text-sm {{ $persentaseSelesaiInternalAdmin >= 75 ? 'text-green-500' : ($persentaseSelesaiInternalAdmin >= 50 ? 'text-yellow-500' : 'text-red-500') }}">
                        {{ number_format($persentaseSelesaiInternalAdmin, 2) }}% Selesai Ditindaklanjuti
                    </p>
                    <p class="text-xs text-gray-500">
                        ({{ number_format($summaryInternal->total_tl_admin_kasus ?? 0) }}/{{ number_format($summaryInternal->total_temuan_admin_kasus ?? 0) }}
                        Kasus)
                    </p>
                </div> --}}
            </div>
        </div>

        {{-- Chart Temuan BPK --}}
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-sm font-medium text-gray-600 mb-3">Grafik Tren Progres Temuan BPK</h3>
            <div class="h-64" id="echart-bpk-trend"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart untuk Tren Temuan BPK
            var bpkChartDom = document.getElementById('echart-bpk-trend');
            if (bpkChartDom) {
                var bpkChart = echarts.init(bpkChartDom);
                var bpkOption = {
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {
                            type: 'cross',
                            crossStyle: {
                                color: '#999'
                            }
                        }
                    },
                    legend: {
                        data: ['Temuan BPK', 'Temuan Internal', 'Akumulasi'],
                        textStyle: {
                            fontSize: 10
                        },
                        bottom: 0
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '15%',
                        containLabel: true
                    }, // Adjusted bottom for legend
                    xAxis: [{
                        type: 'category',
                        boundaryGap: true, // bar charts usually have gap
                        data: @json($bpkChartLabels),
                        axisLabel: {
                            fontSize: 10
                        }
                    }],
                    yAxis: [{
                            type: 'value',
                            name: 'Jumlah Kasus',
                            min: 0,
                            axisLabel: {
                                formatter: '{value}',
                                fontSize: 10
                            },
                            nameTextStyle: {
                                fontSize: 10,
                                padding: [0, 0, 0, 30]
                            } // Adjusted padding
                        },
                        // {
                        //     type: 'value',
                        //     name: 'Nilai (Rp)',
                        //     min: 0,
                        //     axisLabel: {
                        //         formatter: function(value) {
                        //             return (value / 1000000).toFixed(1) + ' Jt';
                        //         },
                        //         fontSize: 10
                        //     },
                        //     nameTextStyle: {
                        //         fontSize: 10,
                        //         padding: [0, 30, 0, 0]
                        //     } // Adjusted padding
                        // }
                    ],
                    series: [{
                            name: 'Temuan BPK',
                            type: 'bar',
                            yAxisIndex: 0,
                            barGap: '20%',
                            barMaxWidth: 20,
                            data: @json($bpkChartData['temuan_admin']),
                            itemStyle: {
                                color: '#3b82f6'
                            }
                        },
                        {
                            name: 'Temuan Internal',
                            type: 'bar',
                            yAxisIndex: 0,
                            barMaxWidth: 20,
                            data: @json($internalChartData['temuan_admin']),
                            itemStyle: {
                                color: '#10b981'
                            }
                        },
                        {
                            name: 'Akumulasi',
                            type: 'line',
                            smooth: true,
                            yAxisIndex: 0,
                            data: @json($arrAkumulasi),
                            itemStyle: {
                                color: '#f59e0b'
                            },
                            lineStyle: {
                                width: 2
                            },
                            symbol: 'circle',
                            symbolSize: 6
                        },
                    ]
                };
                bpkChart.setOption(bpkOption);
                window.addEventListener('resize', () => bpkChart.resize());
            }
        });
    </script>
@endpush
