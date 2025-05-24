@extends('layouts.app')

@section('title', 'Dashboard Binwasnaker & K3')
@section('page_title', 'Binwasnaker & K3')

@section('header_filters')
    <form method="GET" action="{{ route('binwasnaker.dashboard') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full">
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
            <a href="{{ route('binwasnaker.dashboard') }}" class="w-full sm:w-auto text-center text-sm font-medium text-filter-btn-clear-text bg-filter-btn-clear-bg border border-filter-btn-clear-border hover:bg-red-200 focus:ring-4 focus:outline-none focus:ring-red-100 rounded-md px-4 py-2 transition-colors duration-200">
                Bersihkan
            </a>
        </div>
    </form>
@endsection

@section('content')
<div class="space-y-8">


    <!-- <h2 class="text-xl font-semibold text-gray-800 -mb-4">Kinerja Umum Binwasnaker & K3</h2> -->
    @php
        $yearToDisplay = $selectedYear ?: date('Y');
        $monthValue = null;
        if ($selectedMonth && is_numeric($selectedMonth)) {
            $monthValue = (int)$selectedMonth;
        }

        if ($monthValue && $monthValue >= 1 && $monthValue <= 12) {
            $endMonthName = \Carbon\Carbon::create()->month($monthValue)->isoFormat('MMMM');
            $periodText = "Periode: Januari - " . $endMonthName . " " . $yearToDisplay;
        } else {
            $periodText = "Sepanjang Tahun " . $yearToDisplay;
        }
    @endphp

    {{-- Kartu Statistik Binwasnaker & K3 --}}
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="{{ route('binwasnaker.pelaporan-wlkp-online.index') }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">Laporan WLKP Online</p>
                    <p class="stat-card-value">{{ number_format($totalWlkpReported ?? 0) }}</p>
                </div>
                <div class="stat-card-icon-wrapper bg-blue-100">
                    <i class="ri-computer-line text-blue-500 text-2xl"></i>
                </div>
            </div>
            <div class="stat-card-footer">{{ $periodText }}</div>
        </a>

        <a href="{{ route('binwasnaker.pengaduan-pelanggaran-norma.index') }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">Pengaduan Pelanggaran Norma (TL)</p>
                    <p class="stat-card-value">{{ number_format($totalPengaduanNorma ?? 0) }}</p>
                </div>
                <div class="stat-card-icon-wrapper bg-red-100">
                    <i class="ri-alert-line text-red-500 text-2xl"></i>
                </div>
            </div>
            <div class="stat-card-footer">{{ $periodText }}</div>
        </a>

        <a href="{{ route('binwasnaker.penerapan-smk3.index') }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">Penerapan SMK3</p>
                    <p class="stat-card-value">{{ number_format($totalPenerapanSmk3 ?? 0) }}</p>
                </div>
                <div class="stat-card-icon-wrapper bg-green-100">
                    <i class="ri-shield-keyhole-line text-green-500 text-2xl"></i>
                </div>
            </div>
            <div class="stat-card-footer">{{ $periodText }}</div>
        </a>
        
        <a href="{{ route('binwasnaker.self-assessment-norma100.index') }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">Self-Assessment Norma 100</p>
                    <p class="stat-card-value">{{ number_format($totalSelfAssessment ?? 0) }}</p>
                </div>
                <div class="stat-card-icon-wrapper bg-yellow-100">
                    <i class="ri-check-double-line text-yellow-500 text-2xl"></i>
                </div>
            </div>
            <div class="stat-card-footer">{{ $periodText }}</div>
        </a>
    </section>

    {{-- Bagian Grafik --}}
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Laporan WLKP Online ({{ $yearToDisplay }})</h3>
            <div id="echart-binwasnaker-wlkp-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Pengaduan Pelanggaran Norma (TL) ({{ $yearToDisplay }})</h3>
            <div id="echart-binwasnaker-pengaduan-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Penerapan SMK3 ({{ $yearToDisplay }})</h3>
            <div id="echart-binwasnaker-smk3-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Self-Assessment Norma 100 ({{ $yearToDisplay }})</h3>
            <div id="echart-binwasnaker-norma100-trend" style="width: 100%; height: 300px;"></div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
{{-- ECharts sudah di-include di layouts.app.blade.php --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const textColor = '#374151'; 
        const axisLineColor = '#D1D5DB';
        const legendTextColor = '#4B5563';

        // Fungsi umum untuk membuat chart
        function createChart(chartId, legendDataName, chartDataLabels, chartDataValues, itemColor, areaColorStops) {
            var chartDom = document.getElementById(chartId);
            if (chartDom) {
                var myChart = echarts.init(chartDom, null);
                var option = {
                    tooltip: { trigger: 'axis', formatter: function (params) { return params[0].name + '<br/>' + params[0].seriesName + ' : ' + params[0].value.toLocaleString('id-ID'); } },
                    legend: { data: [legendDataName], textStyle: { color: legendTextColor }, bottom: 0 },
                    grid: { left: '3%', right: '4%', bottom: '10%', containLabel: true },
                    xAxis: { type: 'category', boundaryGap: false, data: chartDataLabels, axisLine: { lineStyle: { color: axisLineColor } }, axisLabel: { color: textColor } },
                    yAxis: { type: 'value', name: 'Jumlah', min: 0, axisLine: { lineStyle: { color: axisLineColor } }, axisLabel: { color: textColor, formatter: function (value) { return value.toLocaleString('id-ID'); } }, nameTextStyle: { color: textColor } },
                    series: [{
                        name: legendDataName, type: 'line', smooth: true,
                        data: chartDataValues,
                        itemStyle: { color: itemColor },
                        areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, areaColorStops)}
                    }]
                };
                myChart.setOption(option);
                window.addEventListener('resize', () => myChart.resize());
            }
        }

        // Chart untuk Tren Laporan WLKP Online
        createChart(
            'echart-binwasnaker-wlkp-trend',
            'Laporan WLKP Online',
            @json($wlkpChartLabels ?? []),
            @json($wlkpChartDataValues ?? []),
            '#3b82f6', // Biru
            [{offset: 0, color: 'rgba(59, 130, 246, 0.5)'}, {offset: 1, color: 'rgba(59, 130, 246, 0.1)'}]
        );

        // Chart untuk Tren Pengaduan Pelanggaran Norma (TL)
        createChart(
            'echart-binwasnaker-pengaduan-trend',
            'Pengaduan Norma (TL)',
            @json($pengaduanChartData ?? []),
            @json($pengaduanChartData ?? []),
            '#ef4444', // Merah
            [{offset: 0, color: 'rgba(239, 68, 68, 0.5)'}, {offset: 1, color: 'rgba(239, 68, 68, 0.1)'}]
        );

        // Chart untuk Tren Penerapan SMK3
        createChart(
            'echart-binwasnaker-smk3-trend',
            'Penerapan SMK3',
            @json($smk3TrendChartLabels ?? []),
            @json($smk3TrendChartDataValues ?? []),
            '#10b981', // Hijau
            [{offset: 0, color: 'rgba(16, 185, 129, 0.5)'}, {offset: 1, color: 'rgba(16, 185, 129, 0.1)'}]
        );
        
        // Chart untuk Tren Self-Assessment Norma 100
        createChart(
            'echart-binwasnaker-norma100-trend',
            'Self-Assessment Norma 100',
            @json($saTrendChartLabels ?? []),
            @json($saTrendChartDataValues ?? []),
            '#f59e0b', // Kuning/Amber
            [{offset: 0, color: 'rgba(245, 158, 11, 0.5)'}, {offset: 1, color: 'rgba(245, 158, 11, 0.1)'}]
        );
    });
</script>
@endpush