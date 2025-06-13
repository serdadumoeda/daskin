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
      
       
        
        
       
    </section>

    {{-- Bagian Grafik --}}
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-5 rounded-lg shadow">
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

            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Laporan WLKP Online ({{ $yearToDisplay }})</h3>
            <div id="echart-binwasnaker-wlkp-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
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

            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Pengaduan Pelanggaran Norma (TL) ({{ $yearToDisplay }})</h3>
            <div id="echart-binwasnaker-pengaduan-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
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
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Penerapan SMK3 ({{ $yearToDisplay }})</h3>
            <div id="echart-binwasnaker-smk3-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
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
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Self-Assessment Norma 100 ({{ $yearToDisplay }})</h3>
            <div id="echart-binwasnaker-sa-trend" style="width: 100%; height: 300px;"></div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        
        function createMultiSeriesChart(elementId, labels, seriesConfig) {
            const chartDom = document.getElementById(elementId);
            if (!chartDom) {
                // console.error('Element chart tidak ditemukan:', elementId); // Dihilangkan agar tidak muncul jika div memang tidak ada
                return;
            }
            let existingChart = echarts.getInstanceByDom(chartDom);
            if (existingChart) {
                existingChart.dispose();
            }
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
                    { type: 'value', name: 'Jumlah', min: 0, position: 'left' },
                    { type: 'value', name: 'Kumulatif', min: 0, position: 'right', splitLine: { show: false } }
                ],
                series: series
            };
            myChart.setOption(option);
            window.addEventListener('resize', () => myChart.resize());
        }

        const chartData = @json($chartData ?? null);

        if (!chartData) {
            console.error('Variabel chartData utama tidak tersedia dari controller.');
            const chartIds = ['echart-binwasnaker-wlkp-trend', 'echart-binwasnaker-pengaduan-trend', 'echart-binwasnaker-smk3-trend', 'echart-binwasnaker-sa-trend'];
            chartIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) { el.innerHTML = '<p class="text-center text-gray-500 py-5">Data chart tidak tersedia.</p>'; }
            });
            return;
        }

        // 1. Render Chart WLKP Online
        const wlkpChartEl = document.getElementById('echart-binwasnaker-wlkp-trend');
        if (wlkpChartEl) {
            if (chartData.wlkp && chartData.wlkp.labels && Array.isArray(chartData.wlkp.bulanan) && Array.isArray(chartData.wlkp.kumulatif)) {
                createMultiSeriesChart('echart-binwasnaker-wlkp-trend', chartData.wlkp.labels, [
                    { name: 'Perusahaan Melapor WLKP (Bulanan)', type: 'bar', yAxisIndex: 0, data: chartData.wlkp.bulanan, color: '#3b82f6' },
                    { name: 'Kumulatif Perusahaan', type: 'line', yAxisIndex: 1, data: chartData.wlkp.kumulatif, color: '#10b981' }
                ]);
            } else {
                console.warn('Data untuk chart WLKP tidak lengkap atau tidak tersedia.', chartData.wlkp);
                wlkpChartEl.innerHTML = '<p class="text-center text-gray-500 py-5">Data chart WLKP tidak tersedia.</p>';
            }
        }

        // 2. Render Chart Pengaduan Pelanggaran Norma
        const pengaduanChartEl = document.getElementById('echart-binwasnaker-pengaduan-trend');
        if (pengaduanChartEl) {
            if (chartData.pengaduan && chartData.pengaduan.labels && Array.isArray(chartData.pengaduan.bulanan) && Array.isArray(chartData.pengaduan.kumulatif)) {
                createMultiSeriesChart('echart-binwasnaker-pengaduan-trend', chartData.pengaduan.labels, [
                    { name: 'Pengaduan Norma (Bulanan)', type: 'bar', yAxisIndex: 0, data: chartData.pengaduan.bulanan, color: '#ef4444' },
                    { name: 'Kumulatif Pengaduan', type: 'line', yAxisIndex: 1, data: chartData.pengaduan.kumulatif, color: '#f97316' }
                ]);
            } else {
                console.warn('Data untuk chart Pengaduan tidak lengkap atau tidak tersedia.', chartData.pengaduan);
                pengaduanChartEl.innerHTML = '<p class="text-center text-gray-500 py-5">Data chart Pengaduan tidak tersedia.</p>';
            }
        }
        
        // 3. Render Chart Penerapan SMK3
        const smk3ChartEl = document.getElementById('echart-binwasnaker-smk3-trend');
        if (smk3ChartEl) {
            if (chartData.smk3 && chartData.smk3.labels && Array.isArray(chartData.smk3.bulanan) && Array.isArray(chartData.smk3.kumulatif)) {
                createMultiSeriesChart('echart-binwasnaker-smk3-trend', chartData.smk3.labels, [
                    { name: 'Penerapan SMK3 (Bulanan)', type: 'bar', yAxisIndex: 0, data: chartData.smk3.bulanan, color: '#10b981' },
                    { name: 'Kumulatif SMK3', type: 'line', yAxisIndex: 1, data: chartData.smk3.kumulatif, color: '#06b6d4' }
                ]);
            } else {
                console.warn('Data untuk chart SMK3 tidak lengkap atau tidak tersedia.', chartData.smk3);
                smk3ChartEl.innerHTML = '<p class="text-center text-gray-500 py-5">Data chart SMK3 tidak tersedia.</p>';
            }
        }

        // 4. Render Chart Self Assessment Norma 100
        const saChartEl = document.getElementById('echart-binwasnaker-sa-trend');
        if (saChartEl) { // Pastikan elemen HTML ada
            if (chartData.sa && chartData.sa.labels && Array.isArray(chartData.sa.bulanan) && Array.isArray(chartData.sa.kumulatif)) {
                 createMultiSeriesChart('echart-binwasnaker-sa-trend', chartData.sa.labels, [
                    { name: 'Self Assessment Norma (Bulanan)', type: 'bar', yAxisIndex: 0, data: chartData.sa.bulanan, color: '#f59e0b' },
                    { name: 'Kumulatif Self Assessment', type: 'line', yAxisIndex: 1, data: chartData.sa.kumulatif, color: '#8b5cf6' }
                ]);
            } else {
                console.warn('Data untuk chart Self Assessment (SA) tidak lengkap atau tidak tersedia. Data diterima:', chartData.sa);
                saChartEl.innerHTML = '<p class="text-center text-gray-500 py-5">Data chart Self Assessment tidak tersedia.</p>';
            }
        }
    });
</script>
@endpush