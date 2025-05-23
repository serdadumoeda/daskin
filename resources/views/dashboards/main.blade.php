@extends('layouts.app')

@section('page_title', 'Dashboard Utama Kinerja Kementerian')

@section('header_filters')
    <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full">
        {{-- Tahun --}}
        <div class="flex-1 w-full sm:w-auto">
            <label for="tahun" class="sr-only">Tahun</label>
            <select name="tahun" id="tahun" class="form-input w-full px-3 py-2 border border-gray-300 rounded-md text-sm shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                @php
                    $currentLoopYear = date('Y');
                @endphp
                @for ($yearOption = $currentLoopYear + 1; $yearOption >= $currentLoopYear - 4; $yearOption--)
                    <option value="{{ $yearOption }}" {{ $selectedYear == $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>
                @endfor
            </select>
        </div>

        {{-- Bulan --}}
        <div class="flex-1 w-full sm:w-auto">
            <label for="bulan" class="sr-only">Bulan</label>
            <select name="bulan" id="bulan" class="form-input w-full px-3 py-2 border border-gray-300 rounded-md text-sm shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                <option value="">Semua Bulan (Tahunan)</option>
                {{-- Pastikan array ini ditulis dengan benar --}}
                @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $monthKey => $monthName)
                    <option value="{{ $monthKey + 1 }}" {{ $selectedMonth == ($monthKey + 1) ? 'selected' : '' }}>{{ $monthName }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button type="submit" class="w-full sm:w-auto text-sm font-medium text-filter-btn-apply-text bg-filter-btn-apply-bg border border-filter-btn-apply-border hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-md px-4 py-2 transition-colors duration-200">
                Terapkan
            </button>
            <a href="{{ route('dashboard') }}" class="w-full sm:w-auto text-center text-sm font-medium text-filter-btn-clear-text bg-filter-btn-clear-bg border border-filter-btn-clear-border hover:bg-red-200 focus:ring-4 focus:outline-none focus:ring-red-100 rounded-md px-4 py-2 transition-colors duration-200">
                Bersihkan
            </a>
        </div>
    </form>
@endsection

@section('content')

    @if (!empty($dashboardSummaryCards))
    <section class="mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach ($dashboardSummaryCards as $card)
            <div class="bg-white p-5 rounded-lg shadow-md flex items-center space-x-4 hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300">
                <div class="flex-shrink-0 p-3.5 rounded-full {{ $card['icon_bg_color'] }} w-16 h-16 flex items-center justify-center">
                    <i class="{{ $card['icon'] }} text-3xl {{ $card['icon_text_color'] }}"></i>
                </div>
                <div class="flex-1">
                    <p class="text-[13px] text-gray-600 mb-0.5">{{ $card['title'] }}</p>
                    <h3 class="text-2xl font-semibold text-gray-800">
                        {{ $card['value'] }}
                        @if(isset($card['unit']) && !empty($card['unit']))<span class="text-sm font-normal text-gray-500 ml-1">{{ $card['unit'] }}</span>@endif
                    </h3>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <section class="mb-4">
        <h2 class="text-2xl font-semibold text-gray-800">Grafik Indikator Utama Kementerian</h2>
        <p class="text-sm text-gray-500">Menampilkan tren bulanan dan akumulasi untuk tahun {{ $selectedYear }}
            @if($selectedMonth)
                (Data akumulasi total hingga {{ \Carbon\Carbon::create()->month($selectedMonth)->isoFormat('MMMM') }})
            @else
                (Data akumulasi total tahunan)
            @endif
        </p>
    </section>

    <section class="mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @if(!empty($chartData))
                @foreach ($chartData as $key => $data)
                    <div class="bg-white p-5 rounded-lg shadow-md hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-start space-x-3 mb-3">
                            <div class="flex-shrink-0 p-2.5 rounded-full {{ $data['icon_bg_color'] }} w-12 h-12 flex items-center justify-center">
                                <i class="{{ $data['icon'] }} text-2xl {{ $data['icon_text_color'] }}"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-md font-semibold text-gray-700">{{ $data['title'] }}</h3>
                                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $data['akumulasi_total'] }}</p>
                                <p class="text-xs text-gray-500">
                                    Akumulasi 
                                    @if($selectedMonth)
                                        Jan - {{ \Carbon\Carbon::create()->month($selectedMonth)->isoFormat('MMM') }} {{ $selectedYear }}
                                    @else
                                        Tahun {{ $selectedYear }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div id="chart{{ ucfirst(Str::camel($key)) }}" style="height: 280px; width:100%;"></div>
                    </div>
                @endforeach
            @else
                <p class="lg:col-span-2 text-center text-gray-500 py-8">Data chart tidak tersedia.</p>
            @endif
        </div>
    </section>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const monthLabels = @json($monthLabels); // Mengambil dari variabel $monthLabels yang dikirim controller
        const chartData = @json($chartData);

        for (const key in chartData) {
            const data = chartData[key];
            var chartDomId = 'chart' + key.charAt(0).toUpperCase() + key.slice(1);
            var chartDom = document.getElementById(chartDomId);

            if (chartDom && data && data.tren_bulanan && data.tren_akumulasi_bulanan) {
                var myChart = echarts.init(chartDom);
                var option = {
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {
                            type: 'cross',
                            crossStyle: { color: '#999' }
                        }
                    },
                    legend: {
                        data: ['Jumlah Bulanan', 'Akumulasi Bulanan'],
                        bottom: 0,
                        itemGap: 10,
                        textStyle: { fontSize: 10 }
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '18%', 
                        containLabel: true
                    },
                    xAxis: [
                        {
                            type: 'category',
                            data: monthLabels,
                            axisPointer: { type: 'shadow' }
                        }
                    ],
                    yAxis: [
                        { 
                            type: 'value',
                            name: 'Jumlah',
                            nameTextStyle: { fontSize: 10, padding: [0,0,0,30] }, // Disesuaikan padding
                            axisLabel: { fontSize: 10, formatter: '{value}' },
                            splitLine: { lineStyle: { type: 'dashed', color: '#e0e0e0' } }
                        },
                        { 
                            type: 'value',
                            name: 'Akumulasi',
                            nameTextStyle: { fontSize: 10, padding: [0,30,0,0] }, // Disesuaikan padding
                            axisLabel: { fontSize: 10, formatter: '{value}' },
                            splitLine: { show: false } 
                        }
                    ],
                    series: [
                        {
                            name: 'Jumlah Bulanan',
                            type: 'bar',
                            yAxisIndex: 0, 
                            tooltip: { valueFormatter: function (value) { return value; } },
                            data: data.tren_bulanan,
                            itemStyle: { color: data.bar_color || '#5470C6' }, 
                            barMaxWidth: '40%'
                        },
                        {
                            name: 'Akumulasi Bulanan',
                            type: 'line',
                            yAxisIndex: 1, 
                            smooth: true,
                            tooltip: { valueFormatter: function (value) { return value; } },
                            data: data.tren_akumulasi_bulanan,
                            itemStyle: { color: data.line_color || '#91CC75' }, 
                            lineStyle: { width: 3 },
                            symbolSize: 6
                        }
                    ]
                };
                myChart.setOption(option);

                const resizeChart = () => myChart.resize();
                window.addEventListener('resize', resizeChart);

                const sidebar = document.getElementById('sidebar');
                if (sidebar) {
                     new MutationObserver(mutations => {
                        for (const mutation of mutations) {
                            if (mutation.attributeName === 'class' || mutation.attributeName === 'style') {
                                setTimeout(resizeChart, 350); 
                                break;
                            }
                        }
                    }).observe(sidebar, { attributes: true });
                }
            } else {
                // console.warn('Chart DOM atau data tidak ditemukan untuk:', chartDomId, data);
            }
        }
    });
</script>
@endpush