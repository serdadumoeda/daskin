@extends('layouts.app')

@section('title', 'Dashboard PHI & Jamsos')
@section('page_title', 'PHI & Jamsos')

@section('header_filters')
    <form method="GET" action="{{ route('phi.dashboard') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full">
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
            <a href="{{ route('phi.dashboard') }}" class="w-full sm:w-auto text-center text-sm font-medium text-filter-btn-clear-text bg-filter-btn-clear-bg border border-filter-btn-clear-border hover:bg-red-200 focus:ring-4 focus:outline-none focus:ring-red-100 rounded-md px-4 py-2 transition-colors duration-200">
                Bersihkan
            </a>
        </div>
    </form>
@endsection

@section('content')
<div class="space-y-8">


    <!-- <h2 class="text-xl font-semibold text-gray-800 -mb-4">Kinerja Umum PHI & Jamsos</h2> -->
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