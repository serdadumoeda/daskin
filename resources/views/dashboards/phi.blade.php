@extends('layouts.app')

@section('title', 'Dashboard PHI & Jamsos')
@section('page_title', 'PHI & Jamsos')

@section('header_filters')
    {{-- Bagian ini dikosongkan karena filter dipindahkan ke @section('content') --}}
@endsection

@section('content')
<div class="space-y-8">

    {{-- Filter --}}
    <section>
        <form method="GET" action="{{ route('phi.dashboard') }}" class="w-full mb-6">
            <h3 class="text-md font-semibold text-gray-700 mb-3">Filter Data:</h3>
            <div class="p-4 bg-white rounded-lg shadow">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 items-end">
                    <div class="flex-grow">
                        <label for="year_filter" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                        <select name="year_filter" id="year_filter" class="form-input mt-1 w-full bg-white border-gray-300">
                            @if($availableYears->isEmpty() && $selectedYear)
                                 <option value="{{ $selectedYear }}" selected>{{ $selectedYear }}</option>
                            @elseif($availableYears->isEmpty())
                                <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                            @else
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="flex-grow">
                        <label for="month_filter" class="text-sm text-gray-600 whitespace-nowrap">Bulan:</label>
                        <select name="month_filter" id="month_filter" class="form-input mt-1 w-full bg-white border-gray-300">
                            <option value="">Semua Bulan</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $selectedMonth == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}</option>
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

    <h2 class="text-xl font-semibold text-gray-800 -mb-4">Kinerja Umum PHI & Jamsos</h2>
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

    {{-- Kartu Statistik PHI & Jamsos --}}
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Kartu Jumlah PHK --}}
        <a href="{{ route('phi.jumlah-phk.index') }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">Jumlah PHK</p>
                    {{-- Pastikan variabel $totalPhk digunakan dengan benar --}}
                    <p class="stat-card-value">{{ number_format($totalTkPhk ?? 0) }} </p>
                    <p class="text-xs text-gray-500">{{ number_format($totalPerusahaanPhk ?? 0) }} Perusahaan</p>
                </div>
                <div class="stat-card-icon-wrapper bg-red-100">
                    <i class="ri-user-unfollow-fill text-red-500 text-2xl"></i>
                </div>
            </div>
            <div class="stat-card-footer">{{ $periodText }}</div>
        </a>

        {{-- Kartu Perselisihan (TL) --}}
        <a href="{{ route('phi.perselisihan-ditindaklanjuti.index') }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">Perselisihan (TL)</p>
                    {{-- Pastikan variabel $totalPerselisihan digunakan dengan benar --}}
                    <p class="stat-card-value">{{ number_format($totalPerselisihanDitindaklanjuti ?? 0) }} </p>
                </div>
                <div class="stat-card-icon-wrapper bg-yellow-100">
                    <i class="ri-auction-line text-yellow-500 text-2xl"></i>
                </div>
            </div>
            <div class="stat-card-footer">{{ $periodText }}</div>
        </a>

        {{-- Kartu Mediasi Berhasil --}}
        <a href="{{ route('phi.mediasi-berhasil.index') }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">Mediasi Berhasil</p>
                    <p class="stat-card-value">{{ number_format($totalMediasiBerhasil ?? 0) }}</p>
                </div>
                <div class="stat-card-icon-wrapper bg-green-100">
                    <i class="ri-shake-hands-line text-green-500 text-2xl"></i>
                </div>
            </div>
            <div class="stat-card-footer">{{ $periodText }}</div>
        </a>
        
        {{-- Kartu Perusahaan Penerap SUSU --}}
        <a href="{{ route('phi.perusahaan-menerapkan-susu.index') }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">Perusahaan Penerap SUSU</p>
                    <p class="stat-card-value">{{ number_format($totalPerusahaanSusu ?? 0) }}</p>
                </div>
                <div class="stat-card-icon-wrapper bg-blue-100">
                    <i class="ri-currency-line text-blue-500 text-2xl"></i>
                </div>
            </div>
            <div class="stat-card-footer">{{ $periodText }}</div>
        </a>
    </section>

    {{-- Bagian Grafik --}}
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Jumlah PHK ({{ $yearToDisplay }})</h3>
            <div id="echart-phi-phk-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Perselisihan Ditindaklanjuti ({{ $yearToDisplay }})</h3>
            <div id="echart-phi-perselisihan-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Mediasi Berhasil ({{ $yearToDisplay }})</h3>
            <div id="echart-phi-mediasi-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Perusahaan Menerapkan SUSU ({{ $yearToDisplay }})</h3>
            <div id="echart-phi-susu-trend" style="width: 100%; height: 300px;"></div>
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

        // Chart untuk Tren Jumlah PHK
        createChart(
            'echart-phi-phk-trend',
            'Jumlah PHK',
            @json($phkChartLabels ?? []), // Pastikan variabel ini benar
            @json($phkChartDataValues ?? []), // Pastikan variabel ini benar
            '#ef4444', // Merah
            [{offset: 0, color: 'rgba(239, 68, 68, 0.5)'}, {offset: 1, color: 'rgba(239, 68, 68, 0.1)'}]
        );

        // Chart untuk Tren Perselisihan Ditindaklanjuti
        createChart(
            'echart-phi-perselisihan-trend',
            'Jumlah Perselisihan (TL)',
            @json($perselisihanChartLabels ?? []), // Pastikan variabel ini benar
            @json($perselisihanTlChartDataValues ?? []), // Pastikan variabel ini benar
            '#f59e0b', // Kuning/Amber
            [{offset: 0, color: 'rgba(245, 158, 11, 0.5)'}, {offset: 1, color: 'rgba(245, 158, 11, 0.1)'}]
        );

        // Chart untuk Tren Mediasi Berhasil
        createChart(
            'echart-phi-mediasi-trend',
            'Jumlah Mediasi Berhasil',
            @json($mediasiChartLabels ?? []),
            @json($mediasiBerhasilData ?? []),
            '#10b981', // Hijau Emerald
            [{offset: 0, color: 'rgba(16, 185, 129, 0.5)'}, {offset: 1, color: 'rgba(16, 185, 129, 0.1)'}]
        );
        
        // Chart untuk Tren Perusahaan Menerapkan SUSU
        createChart(
            'echart-phi-susu-trend',
            'Jumlah Perusahaan SUSU',
            @json($susuChartLabels ?? []),
            @json($susuChartDataValues ?? []),
            '#3b82f6', // Biru primary
            [{offset: 0, color: 'rgba(59, 130, 246, 0.5)'}, {offset: 1, color: 'rgba(59, 130, 246, 0.1)'}]
        );
    });
</script>
@endpush