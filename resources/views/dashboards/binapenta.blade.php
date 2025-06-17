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
    {{-- <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
       
    </section> --}}

    {{-- Bagian Grafik --}}
    {{-- Pastikan ID chart dan variabel data chart sesuai dengan yang ada di BinapentaDashboardController --}}
    <div class="grid grid-cols-1 lg:grid-cols-1 gap-6 mb-6">
        {{-- <div class="bg-white p-6 rounded-xl shadow-md"> --}}
        
    </section>

    {{-- Bagian Grafik --}}
    {{-- Pastikan ID chart dan variabel data chart sesuai dengan yang ada di BinapentaDashboardController --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white p-6 rounded-xl shadow-md">
            {{-- Kartu Jml Penempatan oleh Kemnaker --}}
            <a href="{{ route('binapenta.jumlah-penempatan-kemnaker.index') }}" class="stat-card-link-wrapper">
                <div class="stat-card">
                    <div class="stat-card-icon-wrapper bg-blue-100 mr-4">
                        <i class="ri-user-add-line text-blue-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">Jml Penempatan oleh Kemnaker</p>
                        <p class="stat-card-value">{{ number_format($totalPenempatanKemnaker ?? 0) }} <span class="text-sm font-normal">Orang</span></p> {{-- Sesuaikan nama variabel --}}
                    </div>
                </div>
                <div class="stat-card-footer">{{ $periodText }}</div>
            </a>
            <h3 class="font-semibold text-lg text-gray-800 mb-4">Tren Penempatan oleh Kemnaker</h3>
            <div id="echart-binapenta-penempatan-trend" style="height: 400px;"></div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md">
             {{-- Kartu Jml Lowongan Kerja Baru (Pasker) --}}
            <a href="{{ route('binapenta.jumlah-lowongan-pasker.index') }}" class="stat-card-link-wrapper">
                <div class="stat-card ">
                    <div class="stat-card-icon-wrapper bg-green-100 mr-4">
                        <i class="ri-briefcase-4-line text-green-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">Jml Lowongan Kerja Baru (Pasker)</p>
                        <p class="stat-card-value">{{ number_format($totalLowonganPasker ?? 0) }} <span class="text-sm font-normal">Lowongan</span></p> {{-- Sesuaikan nama variabel --}}
                    </div>
                </div>
                <div class="stat-card-footer">{{ $periodText }}</div>
            </a>
            <h3 class="font-semibold text-lg text-gray-800 mb-4">Tren Lowongan Kerja Pasker</h3>
            <div id="echart-binapenta-lowongan-pasker-trend" style="height: 400px;"></div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md"> <h3 class="font-semibold text-lg text-gray-800 mb-4">Tren Persetujuan RPTKA (TKA Disetujui)</h3>
            <div id="echart-binapenta-tka-disetujui-trend" style="height: 400px;"></div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md">
            {{-- Kartu Persetujuan RPTKA --}}
            <a href="{{ route('binapenta.persetujuan-rptka.index') }}" class="stat-card-link-wrapper">
                <div class="stat-card">
                    <div class="stat-card-icon-wrapper bg-purple-100 mr-4">
                        <i class="ri-user-shared-line text-purple-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">Persetujuan RPTKA</p>
                        <p class="stat-card-value">{{ number_format($totalTkaDisetujui ?? 0) }} <span class="text-sm font-normal">TKA</span></p> {{-- Sesuaikan nama variabel --}}
                    </div>
                </div>
                <div class="stat-card-footer">{{ $periodText }}</div>
            </a>
            <h3 class="font-semibold text-lg text-gray-800 mb-4">Komposisi Penempatan berdasarkan Jenis Kelamin</h3>
            <div id="echart-binapenta-penempatan-jk-pie" style="height: 400px;"></div>
        </div>
    </div>

    
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        
        // Fungsi untuk membuat chart tren bulanan & kumulatif
        function createMultiSeriesChart(elementId, labels, seriesConfig) {
            const chartDom = document.getElementById(elementId);
            if (!chartDom) { return; }
            let existingChart = echarts.getInstanceByDom(chartDom);
            if (existingChart) { existingChart.dispose(); }
            const myChart = echarts.init(chartDom);
            
            const series = seriesConfig.map(s => ({
                name: s.name, type: s.type, yAxisIndex: s.yAxisIndex || 0, stack: s.stack || null,
                smooth: s.type === 'line', data: s.data, itemStyle: { color: s.color }, lineStyle: { color: s.color }
            }));
            const legendData = series.map(s => s.name);
            const option = {
                tooltip: { trigger: 'axis', axisPointer: { type: 'cross' } },
                legend: { data: legendData, bottom: 0, type: 'scroll' },
                grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
                xAxis: [{ type: 'category', data: labels, axisPointer: { type: 'shadow' } }],
                yAxis: [
                    { type: 'value', name: 'Jumlah', min: 0, position: 'left', axisLabel: { formatter: '{value}' } },
                    { type: 'value', name: 'Kumulatif', min: 0, position: 'right', splitLine: { show: false }, axisLabel: { formatter: '{value}' } }
                ],
                series: series
            };
            myChart.setOption(option);
            window.addEventListener('resize', () => myChart.resize());
        }

        // Fungsi untuk membuat Pie Chart
        function createPieChart(elementId, titleText, data) {
            const chartDom = document.getElementById(elementId);
            if (!chartDom) { return; }
            let existingChart = echarts.getInstanceByDom(chartDom);
            if (existingChart) { existingChart.dispose(); }
            const myChart = echarts.init(chartDom);
            const option = {
                title: { text: titleText, left: 'center', visibility: 'hidden' },
                tooltip: { trigger: 'item', formatter: '{b} : {c} ({d}%)' },
                legend: { orient: 'vertical', left: 'left', top: 'center', type: 'scroll' },
                series: [{
                    name: titleText, type: 'pie', radius: '70%', center: ['60%', '50%'],
                    data: data,
                    emphasis: { itemStyle: { shadowBlur: 10, shadowOffsetX: 0, shadowColor: 'rgba(0, 0, 0, 0.5)' } }
                }]
            };
            myChart.setOption(option);
            window.addEventListener('resize', () => myChart.resize());
        }

        const chartData = @json($chartData ?? null);
        const pieChartData = @json($pieChartData ?? null);

        if (!chartData) {
            console.error('Variabel chartData utama tidak tersedia dari controller.');
            return;
        }

        // Fungsi render helper untuk chart tren
        function renderTrendChart(chartId, dataKey, seriesName, barColor, lineColor) {
            const chartEl = document.getElementById(chartId);
            if (chartEl) {
                if (chartData[dataKey] && chartData[dataKey].labels && Array.isArray(chartData[dataKey].bulanan) && Array.isArray(chartData[dataKey].kumulatif)) {
                    const isDataEffectivelyEmpty = chartData[dataKey].bulanan.every(val => val === 0);
                    if (chartData[dataKey].labels.length > 0 && !isDataEffectivelyEmpty) {
                        createMultiSeriesChart(chartId, chartData[dataKey].labels, [
                            { name: `${seriesName} (Bulanan)`, type: 'bar', yAxisIndex: 0, data: chartData[dataKey].bulanan, color: barColor },
                            { name: `Kumulatif ${seriesName}`, type: 'line', yAxisIndex: 1, data: chartData[dataKey].kumulatif, color: lineColor }
                        ]);
                    } else {
                        chartEl.innerHTML = `<p class="text-center text-gray-500 py-5">Tidak ada data untuk ditampilkan pada chart ${seriesName}.</p>`;
                    }
                } else {
                    console.warn(`Data untuk chart ${seriesName} tidak lengkap. Data diterima:`, chartData[dataKey]);
                    chartEl.innerHTML = `<p class="text-center text-gray-500 py-5">Data chart ${seriesName} tidak tersedia.</p>`;
                }
            }
        }
        
        // Fungsi render helper untuk pie chart
        function renderPieChart(chartId, dataKey, title) {
            const chartEl = document.getElementById(chartId);
            if (chartEl && pieChartData && pieChartData[dataKey] && Array.isArray(pieChartData[dataKey]) && pieChartData[dataKey].length > 0) {
                const allZero = pieChartData[dataKey].every(item => item.value === 0);
                if(!allZero) {
                    createPieChart(chartId, title, pieChartData[dataKey]);
                } else {
                    chartEl.innerHTML = `<p class="text-center text-gray-500 py-5">Tidak ada data untuk ditampilkan pada chart ${title}.</p>`;
                }
            } else {
                console.warn(`Data untuk pie chart ${title} tidak lengkap. Data diterima:`, pieChartData ? pieChartData[dataKey] : 'pieChartData undefined');
                if(chartEl) chartEl.innerHTML = `<p class="text-center text-gray-500 py-5">Data chart ${title} tidak tersedia.</p>`;
            }
        }

        // 1. Render Chart Tren Penempatan oleh Kemnaker
        renderTrendChart('echart-binapenta-penempatan-trend', 'penempatan', 'Penempatan Kemnaker', '#3b82f6', '#1e40af');
        
        // 2. Render Chart Tren Lowongan Kerja Pasker
        renderTrendChart('echart-binapenta-lowongan-pasker-trend', 'lowongan_pasker', 'Lowongan Pasker', '#10b981', '#059669');

        // 3. Render Chart Tren TKA Disetujui (RPTKA)
        renderTrendChart('echart-binapenta-tka-disetujui-trend', 'tka_disetujui', 'TKA Disetujui (RPTKA)', '#f59e0b', '#d97706');
        
        // 4. Render Pie Chart (Opsional) - Penempatan berdasarkan Jenis Kelamin
        renderPieChart('echart-binapenta-penempatan-jk-pie', 'penempatan_jk', 'Penempatan berdasarkan Jenis Kelamin');

    });
</script>
@endpush