@extends('layouts.app')

@section('title', 'Dashboard Sekretariat Jenderal')
@section('page_title', 'Sekretariat Jenderal')

@section('header_filters')
<form method="GET" action="{{ route('sekretariat-jenderal.dashboard') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full">
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
            <a href="{{ route('sekretariat-jenderal.dashboard') }}" class="w-full sm:w-auto text-center text-sm font-medium text-filter-btn-clear-text bg-filter-btn-clear-bg border border-filter-btn-clear-border hover:bg-red-200 focus:ring-4 focus:outline-none focus:ring-red-100 rounded-md px-4 py-2 transition-colors duration-200">
                Bersihkan
            </a>
        </div>
    </form>
@endsection

@section('content')
<div class="space-y-8">


    <!-- <h2 class="text-xl font-semibold text-gray-800 -mb-4">Kinerja Umum Sekretariat Jenderal</h2> -->
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
            $periodText = "Periode: " . $endMonthName . " " . $yearToDisplay;
        } else {
            $periodText = "Sepanjang Tahun " . $yearToDisplay;
        }
    @endphp

    {{-- Kartu Statistik Sekretariat Jenderal --}}
    {{-- Baris pertama 3 kartu --}}
    <section class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-md">
            <a href="{{ route('sekretariat-jenderal.progress-mou.index') }}">
                <div class="stat-card">
                    <div class="stat-card-icon-wrapper bg-blue-100 mr-4">
                        <i class="ri-honour-line text-blue-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">MOU</p>
                        <p class="stat-card-value">{{ number_format($totalMouBaru ?? 0) }} <span class="text-sm">Dokumen</span></p>
                    </div>
                    
                </div>
                <div class="stat-card-footer">{{ $periodText }}</div>
            </a>
            <div id="echart-sekjen-mou-trend" style="height: 250px;"></div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md">
            <a href="{{ route('sekretariat-jenderal.jumlah-regulasi-baru.index') }}">
                <div class="stat-card">
                    <div class="stat-card-icon-wrapper bg-green-100 mr-4">
                        <i class="ri-file-list-3-line text-green-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">Regulasi</p>
                        <p class="stat-card-value">{{ number_format($totalRegulasiBaru ?? 0) }} <span class="text-sm">Dokumen</span></p>
                    </div>
                    
                </div>
                <div class="stat-card-footer">{{ $periodText }}</div>
            </a>
            <div id="echart-sekjen-regulasi-trend" style="height: 250px;"></div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md">
            <a href="{{ route('sekretariat-jenderal.jumlah-penanganan-kasus.index') }}">
                <div class="stat-card">
                     <div class="stat-card-icon-wrapper bg-purple-100 mr-4">
                        <i class="ri-scales-2-line text-purple-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">Penanganan Kasus</p>
                        <p class="stat-card-value">{{ number_format($totalPenangananKasus ?? 0) }} <span class="text-sm">Kasus</span></p>
                    </div>
                   
                </div>
                <div class="stat-card-footer">{{ $periodText }}</div>
            </a>
            <div id="echart-sekjen-penanganan-kasus-trend" style="height: 250px;"></div>
        </div>
    </section>

    {{-- Baris Kedua 3 kartu --}}
    <section class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-md">
            <a href="{{ route('sekretariat-jenderal.penyelesaian-bmn.index') }}">
                <div class="stat-card">
                     <div class="stat-card-icon-wrapper bg-orange-100 mr-4">
                        <i class="ri-archive-drawer-line text-orange-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">BMN Selesai</p>
                        <p class="stat-card-value">{{ number_format($totalKuantitasBmn ?? 0) }}</p>
                    </div>
                   
                </div>
                <div class="stat-card-footer">{{ $periodText }}</div>
            </a>
            <div id="echart-sekjen-penyelesaian-bmn-trend" style="height: 250px;"></div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md">
            <a href="{{ route('sekretariat-jenderal.persentase-kehadiran.index') }}">
                <div class="stat-card">
                    <div class="stat-card-icon-wrapper bg-yellow-100 mr-4">
                        <i class="ri-user-follow-line text-yellow-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">Kehadiran WFO</p>
                        <p class="stat-card-value">{{ number_format($totalOrangHadirWFO ?? 0) }} <span class="text-sm">Orang</span></p>
                    </div>
                    
                </div>
                <div class="stat-card-footer">{{ $periodText }}</div>
            </a>
            <div id="echart-sekjen-kehadiran-wfo-trend" style="height: 250px;"></div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md">
            <a href="{{ route('sekretariat-jenderal.monev-monitoring-media.index') }}">
                <div class="stat-card">
                     <div class="stat-card-icon-wrapper bg-teal-100 mr-4">
                        <i class="ri-rss-line text-teal-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">Monev Monitoring Media</p>
                        <p class="stat-card-value">{{ number_format($totalBeritaMonev ?? 0) }}</p>
                    </div>
                   
                </div>
                <div class="stat-card-footer">{{ $periodText }}</div>
            </a>
            <div id="echart-sekjen-monev-media-trend" style="height: 250px;"></div>
        </div>
    </section>

    {{-- Baris Ketiga 3 kartu --}}
    <section class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-md">
            <a href="{{ route('sekretariat-jenderal.lulusan-polteknaker-bekerja.index') }}">
                <div class="stat-card">
                     <div class="stat-card-icon-wrapper bg-indigo-100 mr-4">
                        <i class="ri-user-star-line text-indigo-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">Polteknaker Bekerja</p>
                        <p class="stat-card-value">{{ number_format($totalLulusanBekerja ?? 0) }} <span class="text-sm">Orang</span></p>
                    </div>
                   
                </div>
                <div class="stat-card-footer">{{ $periodText }}</div>
            </a>
            <div id="echart-sekjen-lulusan-bekerja-trend" style="height: 250px;"></div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md">
            <a href="{{ route('sekretariat-jenderal.sdm-mengikuti-pelatihan.index') }}">
                <div class="stat-card">
                     <div class="stat-card-icon-wrapper bg-pink-100 mr-4">
                        <i class="ri-team-line text-pink-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">SDM Mengikuti Pelatihan</p>
                        <p class="stat-card-value">{{ number_format($totalSdmPelatihan ?? 0) }} <span class="text-sm">Orang</span></p>
                    </div>
                   
                </div>
                <div class="stat-card-footer">{{ $periodText }}</div>
            </a>
            <div id="echart-sekjen-sdm-pelatihan-trend" style="height: 250px;"></div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md">
            <a href="{{ route('sekretariat-jenderal.ikpa.index') }}">
                <div class="stat-card">
                    <div class="stat-card-icon-wrapper bg-teal-100 mr-4">
                        <i class="ri-secure-payment-fill text-teal-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">Rata-rata IKPA</p>
                        <p class="stat-card-value">{{ number_format($totalIkpa ?? 0, 2) }}</p>
                    </div>
                    
                </div>
                <div class="stat-card-footer">{{ $periodText }}</div>
            </a>
            <div id="echart-sekjen-ikpa-trend" style="height: 250px;"></div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {

        function createMultiSeriesChart(elementId, labels, seriesConfig, yAxisName = 'Jumlah') {
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
                    { type: 'value', name: yAxisName, min: 0, position: 'left', axisLabel: { formatter: '{value}' } },
                    { type: 'value', name: 'Kumulatif', min: 0, position: 'right', splitLine: { show: false }, axisLabel: { formatter: '{value}' } }
                ],
                series: series
            };
            myChart.setOption(option);
            window.addEventListener('resize', () => myChart.resize());
        }

        const chartData = @json($chartData ?? null);

        if (!chartData) {
            console.error('Variabel chartData utama tidak tersedia dari controller.');
            // Fallback untuk semua chart jika data utama tidak ada
            const chartIds = [
                'echart-sekjen-ikpa-trend', 'echart-sekjen-mou-trend', 'echart-sekjen-regulasi-trend',
                'echart-sekjen-penanganan-kasus-trend', 'echart-sekjen-penyelesaian-bmn-trend',
                'echart-sekjen-kehadiran-wfo-trend', 'echart-sekjen-monev-media-trend',
                'echart-sekjen-lulusan-bekerja-trend', 'echart-sekjen-sdm-pelatihan-trend'
            ];
            chartIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) { el.innerHTML = '<p class="text-center text-gray-500 py-5">Data chart tidak tersedia.</p>'; }
            });
            return;
        }

        // Fungsi render helper untuk chart tren
        function renderTrendChart(chartId, dataKey, seriesName, barColor, lineColor, yAxisName = 'Jumlah') {
            const chartEl = document.getElementById(chartId);
            if (chartEl) {
                if (chartData[dataKey] && chartData[dataKey].labels && Array.isArray(chartData[dataKey].bulanan) && Array.isArray(chartData[dataKey].kumulatif)) {
                    const isDataEffectivelyEmpty = chartData[dataKey].bulanan.every(val => val === 0);
                    if (chartData[dataKey].labels.length > 0 && !isDataEffectivelyEmpty) {
                        createMultiSeriesChart(chartId, chartData[dataKey].labels, [
                            { name: `${seriesName} (Bulanan)`, type: 'bar', yAxisIndex: 0, data: chartData[dataKey].bulanan, color: barColor },
                            { name: `Kumulatif ${seriesName}`, type: 'line', yAxisIndex: 1, data: chartData[dataKey].kumulatif, color: lineColor }
                        ], yAxisName);
                    } else {
                        chartEl.innerHTML = `<p class="text-center text-gray-500 py-5">Tidak ada data untuk ditampilkan pada chart ${seriesName}.</p>`;
                    }
                } else {
                    console.warn(`Data untuk chart ${seriesName} tidak lengkap. Data diterima:`, chartData[dataKey]);
                    chartEl.innerHTML = `<p class="text-center text-gray-500 py-5">Data chart ${seriesName} tidak tersedia.</p>`;
                }
            }
        }

        // Render semua chart tren
        renderTrendChart('echart-sekjen-ikpa-trend', 'ikpa', 'IKPA', '#8b5cf6', '#6d28d9', 'Nilai Rata-rata'); // IKPA mungkin perlu nama sumbu Y berbeda
        renderTrendChart('echart-sekjen-mou-trend', 'mou', 'MOU Baru', '#3b82f6', '#1e40af');
        renderTrendChart('echart-sekjen-regulasi-trend', 'regulasi', 'Regulasi Baru', '#10b981', '#059669');
        renderTrendChart('echart-sekjen-penanganan-kasus-trend', 'penanganan_kasus', 'Penanganan Kasus', '#ef4444', '#b91c1c');
        renderTrendChart('echart-sekjen-penyelesaian-bmn-trend', 'penyelesaian_bmn', 'Penyelesaian BMN (Kuantitas)', '#f59e0b', '#d97706');
        renderTrendChart('echart-sekjen-kehadiran-wfo-trend', 'kehadiran_wfo', 'Kehadiran WFO', '#ec4899', '#be185d');
        renderTrendChart('echart-sekjen-monev-media-trend', 'monev_media', 'Berita Monev Media', '#6366f1', '#4338ca');
        renderTrendChart('echart-sekjen-lulusan-bekerja-trend', 'lulusan_bekerja', 'Lulusan Polteknaker Bekerja', '#22c55e', '#15803d');
        renderTrendChart('echart-sekjen-sdm-pelatihan-trend', 'sdm_pelatihan', 'SDM Mengikuti Pelatihan', '#06b6d4', '#0e7490');

    });
</script>
@endpush
