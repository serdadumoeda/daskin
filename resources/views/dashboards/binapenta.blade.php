@extends('layouts.app')

@section('title', 'Dashboard Binapenta')
@section('page_title', 'Binapenta')

@section('header_filters')
    {{-- Bagian ini dikosongkan karena filter dipindahkan ke @section('content') --}}
@endsection

@section('content')
<div class="space-y-8">

    {{-- Filter --}}
    <section>
        <form method="GET" action="{{ route('binapenta.dashboard') }}" class="w-full mb-6">
            <h3 class="text-md font-semibold text-gray-700 mb-3">Filter Data:</h3>
            <div class="p-4 bg-white rounded-lg shadow">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 items-end">
                    <div class="flex-grow">
                        <label for="year_filter" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                        <select name="year_filter" id="year_filter" class="form-input mt-1 w-full bg-white border-gray-300">
                            @if(isset($availableYears) && $availableYears->isEmpty() && isset($selectedYear))
                                 <option value="{{ $selectedYear }}" selected>{{ $selectedYear }}</option>
                            @elseif(isset($availableYears) && $availableYears->isEmpty())
                                <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                            @elseif(isset($availableYears))
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ (isset($selectedYear) && $selectedYear == $year) ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            @else
                                <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option> {{-- Fallback jika $availableYears tidak ada --}}
                            @endif
                        </select>
                    </div>
                    <div class="flex-grow">
                        <label for="month_filter" class="text-sm text-gray-600 whitespace-nowrap">Bulan:</label>
                        <select name="month_filter" id="month_filter" class="form-input mt-1 w-full bg-white border-gray-300">
                            <option value="">Semua Bulan</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ (isset($selectedMonth) && $selectedMonth == $i) ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex items-center space-x-2 pt-5">
                        <button type="submit" class="w-full sm:w-auto px-4 py-1.5 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
                            <i class="ri-filter-3-line mr-1"></i> Terapkan Filter
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </section>

    <h2 class="text-xl font-semibold text-gray-800 -mb-4">Kinerja Umum Binapenta</h2>
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