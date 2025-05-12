@extends('layouts.app')

@section('title', 'Dashboard Inspektorat Jenderal')
@section('page_title', 'Inspektorat Jenderal')

@section('header_filters')
    <form method="GET" action="{{ route('inspektorat.dashboard') }}" class="w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 items-end">
            <div class="flex-grow">
                <label for="year_filter_itjen" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                <select name="year_filter" id="year_filter_itjen" class="form-input mt-1 w-full bg-white">
                    {{-- <option value="">Semua Tahun</option> --}}
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow">
                <label for="month_filter_itjen" class="text-sm text-gray-600 whitespace-nowrap">Bulan:</label>
                <select name="month_filter" id="month_filter_itjen" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua Bulan</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $selectedMonth == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex items-center space-x-2 pt-5">
                <button type="submit" class="w-full sm:w-auto px-4 py-1.5 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
                    <i class="ri-filter-3-line mr-1"></i> Terapkan
                </button>
                 <a href="{{ route('inspektorat.dashboard') }}" class="w-full sm:w-auto px-4 py-1.5 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
                    Reset
                </a>
            </div>
        </div>
    </form>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Baris untuk Kartu Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Kartu 1: Progres Temuan BPK --}}
        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Progres Tindak Lanjut Temuan BPK</h3>
                <a href="{{ route('inspektorat.progress-temuan-bpk.index') }}" class="text-xs text-primary hover:text-primary/80">Lihat Detail &rarr;</a>
            </div>
            <div class="text-2xl font-semibold text-gray-800">
                {{ number_format($summaryBpk->total_temuan_admin_kasus ?? 0) }} Kasus Adm. / 
                Rp {{ number_format($summaryBpk->total_temuan_kerugian_rp ?? 0, 0, ',', '.') }}
            </div>
            <div class="mt-1">
                <p class="text-sm {{ $persentaseSelesaiBpkAdmin >= 75 ? 'text-green-500' : ($persentaseSelesaiBpkAdmin >= 50 ? 'text-yellow-500' : 'text-red-500') }}">
                    Adm: {{ number_format($persentaseSelesaiBpkAdmin, 2) }}% Selesai
                     ({{ number_format($summaryBpk->total_tl_admin_kasus ?? 0) }}/{{ number_format($summaryBpk->total_temuan_admin_kasus ?? 0) }} Kasus)
                </p>
                <p class="text-sm {{ $persentaseSelesaiBpkKerugian >= 75 ? 'text-green-500' : ($persentaseSelesaiBpkKerugian >= 50 ? 'text-yellow-500' : 'text-red-500') }}">
                    Kerugian: {{ number_format($persentaseSelesaiBpkKerugian, 2) }}% Selesai
                    (Rp {{ number_format($summaryBpk->total_tl_kerugian_rp ?? 0, 0, ',', '.') }}/{{ number_format($summaryBpk->total_temuan_kerugian_rp ?? 0, 0, ',', '.') }})
                </p>
            </div>
            {{-- Chart Tren Temuan BPK --}}
            <div class="mt-4 h-64" id="echart-bpk-trend"></div>
        </div>

        {{-- Kartu 2: Progres Temuan Internal --}}
        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Progres Tindak Lanjut Temuan Internal</h3>
                 <a href="{{ route('inspektorat.progress-temuan-internal.index') }}" class="text-xs text-primary hover:text-primary/80">Lihat Detail &rarr;</a>
            </div>
             <div class="text-2xl font-semibold text-gray-800">
                {{ number_format($summaryInternal->total_temuan_admin_kasus ?? 0) }} Kasus Adm. / 
                Rp {{ number_format($summaryInternal->total_temuan_kerugian_rp ?? 0, 0, ',', '.') }}
            </div>
             <div class="mt-1">
                <p class="text-sm {{ $persentaseSelesaiInternalAdmin >= 75 ? 'text-green-500' : ($persentaseSelesaiInternalAdmin >= 50 ? 'text-yellow-500' : 'text-red-500') }}">
                    Adm: {{ number_format($persentaseSelesaiInternalAdmin, 2) }}% Selesai
                    ({{ number_format($summaryInternal->total_tl_admin_kasus ?? 0) }}/{{ number_format($summaryInternal->total_temuan_admin_kasus ?? 0) }} Kasus)
                </p>
                <p class="text-sm {{ $persentaseSelesaiInternalKerugian >= 75 ? 'text-green-500' : ($persentaseSelesaiInternalKerugian >= 50 ? 'text-yellow-500' : 'text-red-500') }}">
                    Kerugian: {{ number_format($persentaseSelesaiInternalKerugian, 2) }}% Selesai
                     (Rp {{ number_format($summaryInternal->total_tl_kerugian_rp ?? 0, 0, ',', '.') }}/{{ number_format($summaryInternal->total_temuan_kerugian_rp ?? 0, 0, ',', '.') }})
                </p>
            </div>
            {{-- Chart Tren Temuan Internal --}}
            <div class="mt-4 h-64" id="echart-internal-trend"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Chart untuk Tren Temuan BPK
        var bpkChartDom = document.getElementById('echart-bpk-trend');
        if (bpkChartDom) {
            var bpkChart = echarts.init(bpkChartDom);
            var bpkOption = {
                tooltip: { 
                    trigger: 'axis', 
                    axisPointer: { type: 'cross', crossStyle: { color: '#999' } }
                },
                legend: { 
                    data: ['Temuan Admin (Kasus)', 'TL Admin (Kasus)', 'Temuan Kerugian (Rp)', 'TL Kerugian (Rp)'],
                    textStyle: { fontSize: 10 }
                },
                grid: { left: '3%', right: '4%', bottom: '10%', containLabel: true },
                xAxis: [{ 
                    type: 'category', 
                    boundaryGap: false, 
                    data: @json($bpkChartLabels),
                    axisLabel: { fontSize: 10 }
                }],
                yAxis: [
                    { 
                        type: 'value', name: 'Jumlah Kasus', min: 0,
                        axisLabel: { formatter: '{value}', fontSize: 10 },
                        nameTextStyle: { fontSize: 10, padding: [0, 0, 0, 25] }
                    },
                    { 
                        type: 'value', name: 'Nilai (Rp)', min: 0,
                        axisLabel: { formatter: function (value) { return (value/1000000).toFixed(0) + ' Jt'; }, fontSize: 10 }, // Format ke Juta
                        nameTextStyle: { fontSize: 10, padding: [0, 25, 0, 0] }
                    }
                ],
                series: [
                    {
                        name: 'Temuan Admin (Kasus)', type: 'bar', yAxisIndex: 0, barGap: 0, barMaxWidth: 20,
                        data: @json($bpkChartData['temuan_admin']), itemStyle: { color: '#3b82f6' } // Biru
                    },
                    {
                        name: 'TL Admin (Kasus)', type: 'bar', yAxisIndex: 0, barMaxWidth: 20,
                        data: @json($bpkChartData['tl_admin']), itemStyle: { color: '#10b981' } // Hijau
                    },
                    {
                        name: 'Temuan Kerugian (Rp)', type: 'line', smooth: true, yAxisIndex: 1,
                        data: @json($bpkChartData['temuan_kerugian']), itemStyle: { color: '#f59e0b' } // Oranye
                    },
                    {
                        name: 'TL Kerugian (Rp)', type: 'line', smooth: true, yAxisIndex: 1,
                        data: @json($bpkChartData['tl_kerugian']), itemStyle: { color: '#ef4444' } // Merah
                    }
                ]
            };
            bpkChart.setOption(bpkOption);
            window.addEventListener('resize', () => bpkChart.resize());
        }

        // Chart untuk Tren Temuan Internal
        var internalChartDom = document.getElementById('echart-internal-trend');
        if (internalChartDom) {
            var internalChart = echarts.init(internalChartDom);
            var internalOption = {
                tooltip: { 
                    trigger: 'axis', 
                    axisPointer: { type: 'cross', crossStyle: { color: '#999' } }
                },
                legend: { 
                    data: ['Temuan Admin (Kasus)', 'TL Admin (Kasus)', 'Temuan Kerugian (Rp)', 'TL Kerugian (Rp)'],
                    textStyle: { fontSize: 10 }
                },
                grid: { left: '3%', right: '4%', bottom: '10%', containLabel: true },
                xAxis: [{ 
                    type: 'category', 
                    boundaryGap: false, 
                    data: @json($internalChartLabels),
                    axisLabel: { fontSize: 10 }
                }],
                 yAxis: [
                    { 
                        type: 'value', name: 'Jumlah Kasus', min: 0,
                        axisLabel: { formatter: '{value}', fontSize: 10 },
                        nameTextStyle: { fontSize: 10, padding: [0, 0, 0, 25] }
                    },
                    { 
                        type: 'value', name: 'Nilai (Rp)', min: 0,
                        axisLabel: { formatter: function (value) { return (value/1000000).toFixed(0) + ' Jt'; }, fontSize: 10 },
                        nameTextStyle: { fontSize: 10, padding: [0, 25, 0, 0] }
                    }
                ],
                series: [
                    {
                        name: 'Temuan Admin (Kasus)', type: 'bar', yAxisIndex: 0, barGap:0, barMaxWidth: 20,
                        data: @json($internalChartData['temuan_admin']), itemStyle: { color: '#8b5cf6' } // Ungu
                    },
                    {
                        name: 'TL Admin (Kasus)', type: 'bar', yAxisIndex: 0, barMaxWidth: 20,
                        data: @json($internalChartData['tl_admin']), itemStyle: { color: '#6366f1' } // Indigo
                    },
                     {
                        name: 'Temuan Kerugian (Rp)', type: 'line', smooth: true, yAxisIndex: 1,
                        data: @json($internalChartData['temuan_kerugian']), itemStyle: { color: '#d946ef' } // Fuchsia
                    },
                    {
                        name: 'TL Kerugian (Rp)', type: 'line', smooth: true, yAxisIndex: 1,
                        data: @json($internalChartData['tl_kerugian']), itemStyle: { color: '#ec4899' } // Pink
                    }
                ]
            };
            internalChart.setOption(internalOption);
            window.addEventListener('resize', () => internalChart.resize());
        }
    });
</script>
@endpush
