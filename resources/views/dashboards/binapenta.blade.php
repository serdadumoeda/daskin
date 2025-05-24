@extends('layouts.app')

@section('title', 'Dashboard Binapenta')
@section('page_title', 'Binapenta')

@section('header_filters')
<form method="GET" action="{{ route('binapenta.dashboard') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full">
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
            <a href="{{ route('binapenta.dashboard') }}" class="w-full sm:w-auto text-center text-sm font-medium text-filter-btn-clear-text bg-filter-btn-clear-bg border border-filter-btn-clear-border hover:bg-red-200 focus:ring-4 focus:outline-none focus:ring-red-100 rounded-md px-4 py-2 transition-colors duration-200">
                Bersihkan
            </a>
        </div>
    </form>
@endsection

@section('content')
<div class="space-y-8">



    <!-- <h2 class="text-xl font-semibold text-gray-800 -mb-4">Kinerja Umum Binapenta</h2> -->
    @php
        $currentSelectedYear = $selectedYear ?? date('Y');
        $currentSelectedMonth = $selectedMonth ?? null;

        $yearToDisplay = $currentSelectedYear;
        $monthValue = null;
        if ($currentSelectedMonth && is_numeric($currentSelectedMonth)) {
            $monthValue = (int)$currentSelectedMonth;
        }

        if ($monthValue && $monthValue >= 1 && $monthValue <= 12) {
            $endMonthName = \Carbon\Carbon::create()->month($monthValue)->isoFormat('MMMM');
            $periodText = "Periode: Januari - " . $endMonthName . " " . $yearToDisplay;
        } else {
            $periodText = "Sepanjang Tahun " . $yearToDisplay;
        }
    @endphp

    {{-- Kartu Statistik Binapenta --}}
    {{-- Pastikan variabel total dan rute sesuai dengan yang ada di BinapentaDashboardController --}}
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Kartu Jml Penempatan oleh Kemnaker --}}
        <a href="{{ route('binapenta.jumlah-penempatan-kemnaker.index') }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">Jml Penempatan oleh Kemnaker</p>
                    <p class="stat-card-value">{{ number_format($totalPenempatanKemnaker ?? 0) }} <span class="text-sm font-normal">Orang</span></p> {{-- Sesuaikan nama variabel --}}
                </div>
                <div class="stat-card-icon-wrapper bg-blue-100">
                    <i class="ri-user-add-line text-blue-500 text-2xl"></i>
                </div>
            </div>
            <div class="stat-card-footer">{{ $periodText }}</div>
        </a>

        {{-- Kartu Jml Lowongan Kerja Baru (Pasker) --}}
        <a href="{{ route('binapenta.jumlah-lowongan-pasker.index') }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">Jml Lowongan Kerja Baru (Pasker)</p>
                    <p class="stat-card-value">{{ number_format($totalLowonganPasker ?? 0) }} <span class="text-sm font-normal">Lowongan</span></p> {{-- Sesuaikan nama variabel --}}
                </div>
                <div class="stat-card-icon-wrapper bg-green-100">
                    <i class="ri-briefcase-4-line text-green-500 text-2xl"></i>
                </div>
            </div>
            <div class="stat-card-footer">{{ $periodText }}</div>
        </a>
        
        {{-- Kartu Persetujuan RPTKA --}}
        <a href="{{ route('binapenta.persetujuan-rptka.index') }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">Persetujuan RPTKA</p>
                    <p class="stat-card-value">{{ number_format($totalRptkaDiterima ?? 0) }} <span class="text-sm font-normal">TKA</span></p> {{-- Sesuaikan nama variabel --}}
                </div>
                <div class="stat-card-icon-wrapper bg-purple-100">
                    <i class="ri-user-shared-line text-purple-500 text-2xl"></i>
                </div>
            </div>
            <div class="stat-card-footer">{{ $periodText }}</div>
        </a>
    </section>

    {{-- Bagian Grafik --}}
    {{-- Pastikan ID chart dan variabel data chart sesuai dengan yang ada di BinapentaDashboardController --}}
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Penempatan oleh Kemnaker ({{ $yearToDisplay }})</h3>
            <div id="echart-binapenta-penempatan-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Lowongan Kerja Baru (Pasker) ({{ $yearToDisplay }})</h3>
            <div id="echart-binapenta-lowongan-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Persetujuan RPTKA ({{ $yearToDisplay }})</h3>
            <div id="echart-binapenta-rptka-trend" style="width: 100%; height: 300px;"></div>
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

        // Chart untuk Tren Penempatan oleh Kemnaker
        createChart(
            'echart-binapenta-penempatan-trend',
            'Jml Penempatan Kemnaker',
            @json($penempatanChartLabels ?? []), // Sesuaikan nama variabel
            @json($penempatanChartDataValues ?? []), // Sesuaikan nama variabel
            '#3b82f6', // Biru
            [{offset: 0, color: 'rgba(59, 130, 246, 0.5)'}, {offset: 1, color: 'rgba(59, 130, 246, 0.1)'}]
        );

        // Chart untuk Tren Lowongan Kerja Baru (Pasker)
        createChart(
            'echart-binapenta-lowongan-trend',
            'Jml Lowongan Pasker',
            @json($lowonganPaskerChartLabels ?? []), // Sesuaikan nama variabel
            @json($lowonganPaskerChartDataValues ?? []), // Sesuaikan nama variabel
            '#10b981', // Hijau
            [{offset: 0, color: 'rgba(16, 185, 129, 0.5)'}, {offset: 1, color: 'rgba(16, 185, 129, 0.1)'}]
        );

        // Chart untuk Tren Persetujuan RPTKA
        createChart(
            'echart-binapenta-rptka-trend',
            'Persetujuan RPTKA',
            @json($rptkaDiterimaChartLabels ?? []), // Sesuaikan nama variabel
            @json($rptkaDiterimaChartDataValues ?? []), // Sesuaikan nama variabel
            '#8b5cf6', // Ungu/Violet
            [{offset: 0, color: 'rgba(139, 92, 246, 0.5)'}, {offset: 1, color: 'rgba(139, 92, 246, 0.1)'}]
        );
    });
</script>
@endpush