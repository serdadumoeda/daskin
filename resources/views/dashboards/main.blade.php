@extends('layouts.app')

@section('page_title', 'Dashboard Utama Kinerja Kementerian')

@section('header_filters')
    <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full">
        <div class="flex-1 w-full sm:w-auto">
            <label for="tahun" class="sr-only">Tahun</label>
            <select name="tahun" id="tahun" class="form-input w-full px-3 py-2 border border-gray-300 rounded-md text-sm shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                @foreach ($availableYears as $yearOption)
                    <option value="{{ $yearOption }}" {{ $selectedYear == $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 w-full sm:w-auto">
            <label for="bulan" class="sr-only">Bulan</label>
            <select name="bulan" id="bulan" class="form-input w-full px-3 py-2 border border-gray-300 rounded-md text-sm shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                <option value="">Semua Bulan (Tahunan)</option>
                @php $monthsForFilter = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']; @endphp
                @foreach ($monthsForFilter as $monthKey => $monthName)
                    <option value="{{ $monthKey + 1 }}" {{ $selectedMonth == ($monthKey + 1) ? 'selected' : '' }}>{{ $monthName }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button type="submit" class="w-full sm:w-auto text-sm font-medium text-filter-btn-apply-text bg-filter-btn-apply-bg border border-filter-btn-apply-border hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-md px-4 py-2">
                Terapkan
            </button>
            <a href="{{ route('dashboard') }}" class="w-full sm:w-auto text-center text-sm font-medium text-filter-btn-clear-text bg-filter-btn-clear-bg border border-filter-btn-clear-border hover:bg-red-200 focus:ring-4 focus:outline-none focus:ring-red-100 rounded-md px-4 py-2">
                Bersihkan
            </a>
        </div>
    </form>
@endsection

@section('content')
<div class="space-y-6">

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

    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">

        <div class="bg-white p-5 rounded-xl shadow-md">
            <a href="{{ route('binapenta.persetujuan-rptka.index') }}" class="stat-card-link-wrapper-include">
                <div class="stat-card flex items-center">
                    <div class="stat-card-icon-wrapper bg-purple-100 mr-4">
                        <i class="ri-account-box-line text-purple-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">Penempatan Tenaga Kerja</p>
                        <p class="stat-card-value">{{ number_format($totalPenempatanKemenaker ?? 0) }} <span class="text-sm font-normal">Orang</span></p> 
                    </div>
                </div>
            </a>
            <div id="main-chart-penempatan-kemnaker" style="height: 350px;"></div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-md">
            <a href="{{ route('binapenta.persetujuan-rptka.index') }}" class="stat-card-link-wrapper-include">
                <div class="stat-card flex items-center">
                    <div class="stat-card-icon-wrapper bg-yellow-100 mr-4">
                        <i class="ri-team-line text-yellow-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">Peserta Pelatihan</p>
                        <p class="stat-card-value">{{ number_format($totalPesertaPelatihan ?? 0) }} <span class="text-sm font-normal">Orang</span></p> 
                    </div>
                </div>
            </a>
            <div id="main-chart-peserta-pelatihan" style="height: 350px;"></div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-md">
            <a href="{{ route('binapenta.persetujuan-rptka.index') }}" class="stat-card-link-wrapper-include">
                <div class="stat-card flex items-center">
                    <div class="stat-card-icon-wrapper bg-blue-100 mr-4">
                        <i class="ri-currency-line text-blue-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info mb-6">
                        <p class="stat-card-title">Perusahaan Penerap SUSU</p>
                        <p class="stat-card-value">{{ number_format($totalPerusahaanSusu ?? 0) }}</p>
                    </div>
                </div>
                <div class="stat-card-footer">{{ $periodText }}</div>
            </a>
            <div id="echart-phi-susu-trend" style="width: 100%; height: 300px;"></div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-md">
            <a href="{{ route('binapenta.persetujuan-rptka.index') }}" class="stat-card-link-wrapper-include">
                <div class="stat-card flex items-center">
                    <div class="stat-card-icon-wrapper bg-blue-100 mr-4">
                        <i class="ri-computer-line text-blue-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">WLKP Online</p>
                        <p class="stat-card-value">{{ number_format($totalWlkpReported ?? 0) }}</p>
                    </div>
                </div>
                <div class="stat-card-footer">{{ $periodText }}</div>
            </a>
            <div id="echart-binwasnaker-wlkp-trend" style="width: 100%; height: 300px;"></div>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
            <a href="{{ route('binwasnaker.pelaporan-wlkp-online.index') }}" class="stat-card-link-wrapper">
                <div class="stat-card">
                    <div class="stat-card-icon-wrapper bg-green-100 mr-4">
                        <i class="ri-file-list-3-line text-green-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">Regulasi</p>
                        <p class="stat-card-value">{{ number_format($totalRegulasiBaru ?? 0) }} <span class="text-sm">Dokumen</span></p>
                    </div>
                </div>
            </a>
            <div id="echart-sekjen-regulasi-trend" style="height: 250px;"></div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-md">
            <a href="{{ route('binapenta.persetujuan-rptka.index') }}" class="stat-card-link-wrapper-include">
                <div class="stat-card flex items-center">
                    <div class="stat-card-icon-wrapper bg-blue-100 mr-4">
                        <i class="ri-apps-2-line text-blue-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">Rekomendasi Kebijakan</p>
                        <p class="stat-card-value">{{ number_format($totalRekomendasiKebijakan ?? 0) }} </p> 
                    </div>
                </div>
            </a>
            <div id="main-chart-rekomendasi-kebijakan" style="height: 350px;"></div>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
            <a href="{{ route('binwasnaker.pelaporan-wlkp-online.index') }}" class="stat-card-link-wrapper">
                <div class="stat-card">
                    <div class="stat-card-icon-wrapper bg-green-100 mr-4">
                        <i class="ri-briefcase-4-line text-green-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">Jumlah Lowongan Kerja</p>
                        <p class="stat-card-value">{{ number_format($totalLowonganPasker ?? 0) }} <span class="text-sm font-normal">Lowongan</span></p> {{-- Sesuaikan nama variabel --}}
                    </div>
                </div>
            </a>
            <div id="echart-binapenta-lowongan-pasker-trend" style="height: 400px;"></div>
        </div>
        
        <!-- <div class="bg-white p-5 rounded-xl shadow-md">
            <a href="{{ route('binapenta.jumlah-penempatan-kemnaker.index') }}" class="stat-card-link-wrapper-include">
                <div class="stat-card flex items-center">
                    <div class="stat-card-icon-wrapper bg-blue-100 mr-4">
                        <i class="ri-auction-fill text-blue-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">TLHP BPK</p>
                        <p class="stat-card-value">{{ number_format($persenSelesaiBpk ?? 0, 2) }} <span class="text-sm font-normal">%</span></p> 
                    </div>
                </div>
            </a>
            <div id="main-chart-penyelesaian-bpk" style="height: 350px;"></div>
        </div> -->

        <!-- <div class="bg-white p-5 rounded-xl shadow-md">
            <a href="{{ route('binapenta.jumlah-lowongan-pasker.index') }}" class="stat-card-link-wrapper-include">
                <div class="stat-card flex items-center">
                    <div class="stat-card-icon-wrapper bg-green-100 mr-4">
                        <i class="ri-checkbox-multiple-fill text-green-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">TLHP Itjen</p>
                        <p class="stat-card-value">{{ number_format($persenSelesaiInternal ?? 0, 2) }} <span class="text-sm font-normal">%</span></p> 
                    </div>
                </div>
            </a>
            <div id="main-chart-penyelesaian-internal" style="height: 350px;"></div>
        </div> -->

        <!-- <div class="bg-white p-5 rounded-xl shadow-md">
            <a href="{{ route('binapenta.persetujuan-rptka.index') }}" class="stat-card-link-wrapper-include">
                <div class="stat-card flex items-center">
                    <div class="stat-card-icon-wrapper bg-red-100 mr-4">
                        <i class=" ri-user-2-fill text-red-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">Lulusan Polteknaker Bekerja</p>
                        <p class="stat-card-value">{{ number_format($totalLulusanBekerja ?? 0) }} <span class="text-sm font-normal">Orang</span></p> 
                    </div>
                </div>
            </a>
            <div id="main-chart-lulusan-bekerja" style="height: 350px;"></div>
        </div> -->

        <!-- <div class="bg-white p-5 rounded-xl shadow-md">
            <a href="{{ route('binapenta.persetujuan-rptka.index') }}" class="stat-card-link-wrapper-include">
                <div class="stat-card flex items-center">
                    <div class="stat-card-icon-wrapper bg-green-100 mr-4">
                        <i class="ri-bank-card-fill text-green-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">IKPA</p>
                        <p class="stat-card-value">{{ number_format($avgIkpaKementerian ?? 0, 2) }} </p> 
                    </div>
                </div>
            </a>
            <div id="main-chart-ikpa" style="height: 350px;"></div>
        </div> -->
        
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        
        // Fungsi untuk membuat chart dengan banyak seri (batang/garis)
        function createMainMultiSeriesChart(elementId, labels, seriesConfig, yAxisNames = ['Jumlah', 'Persentase (%)']) {
            const chartDom = document.getElementById(elementId);
            if (!chartDom) { return; }
            let existingChart = echarts.getInstanceByDom(chartDom);
            if (existingChart) { existingChart.dispose(); }
            const myChart = echarts.init(chartDom);
            
            const series = seriesConfig.map(s => ({
                name: s.name, type: s.type, yAxisIndex: s.yAxisIndex || 0, stack: s.stack || null,
                smooth: s.type === 'line', data: s.data, itemStyle: { color: s.color }, lineStyle: { color: s.color, width: s.lineWidth || 2 },
                symbol: 'circle', symbolSize: s.symbolSize || (s.type === 'line' ? 6 : 0),
                tooltip: { valueFormatter: function (value) { return parseFloat(value).toLocaleString('id-ID') + (s.unit || ''); } }
            }));
            const legendData = series.map(s => s.name);

            const option = {
                tooltip: { trigger: 'axis', axisPointer: { type: 'cross' } },
                legend: { data: legendData, bottom: 0, type: 'scroll' },
                grid: { left: '5%', right: '8%', bottom: '15%', containLabel: true },
                xAxis: [{ type: 'category', data: labels, axisPointer: { type: 'shadow' } }],
                yAxis: [

                    { type: 'value', name: 'Jumlah Kasus', min: 0, position: 'left', axisLabel: { formatter: '{value}' } },
                    { type: 'value', name: 'Penyelesaian (%)', min: 0, max: yAxisNames[1].includes('%') ? 100 : undefined, position: 'right', splitLine: { show: false }, axisLabel: { formatter: yAxisNames[1].includes('%') ? '{value}%' : '{value}' } }
                ],
                series: series
            };
            myChart.setOption(option);
            window.addEventListener('resize', () => myChart.resize());
        }

        const chartData = @json($chartData ?? null);

        if (!chartData) {
            console.error('Variabel chartData utama tidak tersedia dari controller.');
            // Fallback bisa ditambahkan di sini untuk setiap div chart
            return;
        }

        // Fungsi render helper umum
        function renderMainChart(chartId, dataKey, defaultSeriesName, barColor, lineColor, yAxisName = 'Jumlah', kumulatifLineName = 'Kumulatif') {
            const chartEl = document.getElementById(chartId);
            if (chartEl && chartData[dataKey] && chartData[dataKey].labels && Array.isArray(chartData[dataKey].bulanan) && Array.isArray(chartData[dataKey].kumulatif)) {
                const isDataEffectivelyEmpty = chartData[dataKey].bulanan.every(val => val === 0);
                if (chartData[dataKey].labels.length > 0 && !isDataEffectivelyEmpty) {
                    createMainMultiSeriesChart(chartId, chartData[dataKey].labels, [
                        { name: `${defaultSeriesName} (Bulanan)`, type: 'bar', yAxisIndex: 0, data: chartData[dataKey].bulanan, color: barColor, unit: '' },
                        { name: `${kumulatifLineName} ${defaultSeriesName}`, type: 'line', yAxisIndex: 1, data: chartData[dataKey].kumulatif, color: lineColor, unit: '' }
                    ], yAxisName, kumulatifLineName);
                } else {
                    chartEl.innerHTML = `<p class="text-center text-gray-500 py-5">Tidak ada data untuk ditampilkan pada chart ${defaultSeriesName}.</p>`;
                }
            } else {
                console.warn(`Data untuk chart ${defaultSeriesName} tidak lengkap. Data diterima:`, chartData[dataKey]);
                if(chartEl) chartEl.innerHTML = `<p class="text-center text-gray-500 py-5">Data chart ${defaultSeriesName} tidak tersedia.</p>`;
            }
        }
        
        // Fungsi render khusus untuk chart penyelesaian temuan (dengan persentase)
        function renderPenyelesaianChart(chartId, dataKey, titlePrefix) {
            const chartEl = document.getElementById(chartId);
            if (chartEl && chartData[dataKey] && chartData[dataKey].labels && Array.isArray(chartData[dataKey].bulanan_temuan) && Array.isArray(chartData[dataKey].bulanan_tl) && Array.isArray(chartData[dataKey].persentase_kumulatif)) {
                const isDataEmpty = chartData[dataKey].bulanan_temuan.every(val => val === 0) && chartData[dataKey].bulanan_tl.every(val => val === 0);
                 if (chartData[dataKey].labels.length > 0 && !isDataEmpty) {
                    createMainMultiSeriesChart(chartId, chartData[dataKey].labels, [
                        { name: `Temuan ${titlePrefix} (Kasus)`, type: 'bar', yAxisIndex: 0, data: chartData[dataKey].bulanan_temuan, color: '#3b82f6', unit: ' Kasus' },
                        { name: `TL ${titlePrefix} (Kasus)`, type: 'bar', yAxisIndex: 0, data: chartData[dataKey].bulanan_tl, color: '#10b981', unit: ' Kasus' },
                        { name: `Penyelesaian Kumulatif ${titlePrefix} (%)`, type: 'line', yAxisIndex: 1, data: chartData[dataKey].persentase_kumulatif, color: '#ef4444', unit: '%', lineWidth: 3 }
                    ], 'Jumlah Kasus', 'Penyelesaian (%)');
                 } else {
                    chartEl.innerHTML = `<p class="text-center text-gray-500 py-5">Tidak ada data untuk chart ${titlePrefix}.</p>`;
                 }
            } else {
                console.warn(`Data untuk chart Penyelesaian ${titlePrefix} tidak lengkap. Data diterima:`, chartData[dataKey]);
                if(chartEl) chartEl.innerHTML = `<p class="text-center text-gray-500 py-5">Data chart Penyelesaian ${titlePrefix} tidak tersedia.</p>`;
            }
        }

        // Fungsi Render Penerapan SUSU
        function renderSusuMainChart(chartId, dataKey, defaultSeriesName, barColor, lineColor, yAxisName = 'Jumlah', kumulatifLineName = 'Kumulatif') {
            const chartEl = document.getElementById(chartId);
            if (chartEl && chartData[dataKey] && chartData[dataKey].labels && Array.isArray(chartData[dataKey].bulanan) && Array.isArray(chartData[dataKey].kumulatif)) {
                const isDataEffectivelyEmpty = chartData[dataKey].bulanan.every(val => val === 0);
                if (chartData[dataKey].labels.length > 0 && !isDataEffectivelyEmpty) {
                    createMainMultiSeriesChart(chartId, chartData[dataKey].labels, [
                        { name: `${defaultSeriesName}`, type: 'bar', yAxisIndex: 0, data: chartData[dataKey].bulanan, color: barColor, unit: '' },
                        { name: `${kumulatifLineName} ${defaultSeriesName}`, type: 'none', yAxisIndex: 1, data: chartData[dataKey].kumulatif, color: lineColor, unit: '' }
                    ], yAxisName, kumulatifLineName);
                } else {
                    chartEl.innerHTML = `<p class="text-center text-gray-500 py-5">Tidak ada data untuk ditampilkan pada chart ${defaultSeriesName}.</p>`;
                }
            } else {
                console.warn(`Data untuk chart ${defaultSeriesName} tidak lengkap. Data diterima:`, chartData[dataKey]);
                if(chartEl) chartEl.innerHTML = `<p class="text-center text-gray-500 py-5">Data chart ${defaultSeriesName} tidak tersedia.</p>`;
            }
        }

        // Fungsi Render WLKP Online
        function renderWLKPChart(chartId, dataKey, defaultSeriesName, barColor, lineColor, yAxisName = 'Jumlah', kumulatifLineName = 'Kumulatif') {
            const chartEl = document.getElementById(chartId);
            if (chartEl && chartData[dataKey] && chartData[dataKey].labels && Array.isArray(chartData[dataKey].bulanan) && Array.isArray(chartData[dataKey].kumulatif)) {
                const isDataEffectivelyEmpty = chartData[dataKey].bulanan.every(val => val === 0);
                if (chartData[dataKey].labels.length > 0 && !isDataEffectivelyEmpty) {
                    createMainMultiSeriesChart(chartId, chartData[dataKey].labels, [
                        { name: `${defaultSeriesName}`, type: 'bar', yAxisIndex: 0, data: chartData[dataKey].bulanan, color: barColor, unit: '' },
                        { name: `${kumulatifLineName}`, type: 'line', yAxisIndex: 1, data: chartData[dataKey].kumulatif, color: lineColor, unit: '' }
                    ], yAxisName, kumulatifLineName);
                } else {
                    chartEl.innerHTML = `<p class="text-center text-gray-500 py-5">Tidak ada data untuk ditampilkan pada chart ${defaultSeriesName}.</p>`;
                }
            } else {
                console.warn(`Data untuk chart ${defaultSeriesName} tidak lengkap. Data diterima:`, chartData[dataKey]);
                if(chartEl) chartEl.innerHTML = `<p class="text-center text-gray-500 py-5">Data chart ${defaultSeriesName} tidak tersedia.</p>`;
            }
        }

        // Render Charts
        renderPenyelesaianChart('main-chart-penyelesaian-bpk', 'penyelesaian_bpk', 'BPK');
        renderPenyelesaianChart('main-chart-penyelesaian-internal', 'penyelesaian_internal', 'Internal');
        renderMainChart('main-chart-penempatan-kemnaker', 'penempatan_kemnaker', 'Penempatan', '#8b5cf6', '#6d28d9');
        renderMainChart('main-chart-peserta-pelatihan', 'peserta_pelatihan', 'Peserta Pelatihan', '#f59e0b', '#d97706');
        renderMainChart('main-chart-lulusan-bekerja', 'lulusan_bekerja', 'Lulusan Bekerja', '#ec4899', '#be185d');
        renderMainChart('main-chart-rekomendasi-kebijakan', 'rekomendasi_kebijakan', 'Rekomendasi Kebijakan', '#6366f1', '#4338ca');
        renderMainChart('main-chart-ikpa', 'ikpa', 'IKPA', '#22c55e', '#15803d', 'Rata-rata Nilai', 'Kumulatif Rata-rata');
        renderSusuMainChart('echart-phi-susu-trend', 'susu', 'Perusahaan Terapkan SUSU', '#3b82f6', '#ef4444');
        renderWLKPChart('echart-binwasnaker-wlkp-trend', 'wlkp', 'Perusahaan Melapor WLKP', '#3b82f6', '#10b981', 'Test', 'Kumulatif Perusahaan');

    });
</script>
@endpush