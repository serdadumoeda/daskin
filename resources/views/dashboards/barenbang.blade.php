@extends('layouts.app')

@section('title', 'Dashboard Barenbang')
@section('page_title', 'Barenbang')

@section('header_filters')
    {{-- Bagian ini dikosongkan karena filter dipindahkan ke @section('content') --}}
@endsection

@section('content')

<div class="space-y-6">
        <h2 class="text-xl font-semibold text-grey-500 mb-4 mt-6">Kinerja Umum Barenbang</h2>
    {{-- Filter dan Seksi Data Barenbang Utama (Kajian & Aplikasi) --}}
    <section>
    {{-- Filter untuk Data Kajian, Rekomendasi, Aplikasi --}}
        <form method="GET" action="{{ route('barenbang.dashboard') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full mb-6">
            {{-- Tahun --}}
            <div class="flex-1 w-full sm:w-auto">
                <label for="tahun_main" class="sr-only">Tahun</label>
                {{-- Menggunakan class dari UI baru --}}
                <select name="tahun_main" id="tahun_main" class="form-input w-full px-3 py-2 border border-gray-300 rounded-md text-sm shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    {{-- Logika & Variabel dari form barenbang --}}
                    @foreach ($availableYearsMain as $year)
                        <option value="{{ $year }}" {{ $selectedYearMain == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Bulan --}}
            <div class="flex-1 w-full sm:w-auto">
                <label for="bulan_main" class="sr-only">Bulan</label>
                {{-- Menggunakan class dari UI baru --}}
                <select name="bulan_main" id="bulan_main" class="form-input w-full px-3 py-2 border border-gray-300 rounded-md text-sm shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <option value="">Semua Bulan</option>
                    {{-- Logika & Variabel dari form barenbang --}}
                    @php $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
                    @foreach ($months as $i => $monthName)
                        <option value="{{ $i + 1 }}" {{ $selectedMonthMain == ($i + 1) ? 'selected' : '' }}>{{ $monthName }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- Tombol Aksi --}}
            <div class="flex items-center gap-2 w-full sm:w-auto">
                {{-- Tombol submit dengan teks dari barenbang & class dari UI baru --}}
                <button type="submit" class="w-full sm:w-auto text-sm font-medium text-filter-btn-apply-text bg-filter-btn-apply-bg border border-filter-btn-apply-border hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-md px-4 py-2 transition-colors duration-200">
                    Filter Kajian/Aplikasi
                </button>
                {{-- Tombol clear yang mengarah ke route barenbang --}}
                <a href="{{ route('barenbang.dashboard') }}" class="w-full sm:w-auto text-center text-sm font-medium text-filter-btn-clear-text bg-filter-btn-clear-bg border border-filter-btn-clear-border hover:bg-red-200 focus:ring-4 focus:outline-none focus:ring-red-100 rounded-md px-4 py-2 transition-colors duration-200">
                    Bersihkan
                </a>
            </div>
        </form>

        </section>


        
        @php
            $yearToDisplayMain = $selectedYearMain ?: date('Y');
            $monthValueMain = null;
            if ($selectedMonthMain && is_numeric($selectedMonthMain)) {
                $monthValueMain = (int)$selectedMonthMain;
            }

            if ($monthValueMain && $monthValueMain >= 1 && $monthValueMain <= 12) {
                $endMonthNameMain = \Carbon\Carbon::create()->month($monthValueMain)->isoFormat('MMMM');
                $periodTextMain = "Periode: Januari - " . $endMonthNameMain . " " . $yearToDisplayMain;
            } else {
                $periodTextMain = "Sepanjang Tahun " . $yearToDisplayMain;
            }
        @endphp
        
        
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          
            
           
          
        </section>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white p-6 rounded-xl shadow-md">
                  <a href="{{ route('barenbang.jumlah-kajian-rekomendasi.index') }}" class="stat-card-link-wrapper">
                <div class="stat-card">
                    <div class="stat-card-info">
                        <p class="stat-card-title">Total Kajian</p>
                        <p class="stat-card-value">{{ number_format($totalKajian ?? 0) }}</p>
                    </div>
                    <div class="stat-card-icon-wrapper bg-purple-100">
                        <i class="ri-lightbulb-flash-line text-purple-500 text-2xl"></i>
                    </div>
                </div>
                <div class="stat-card-footer"></div>
            </a>
                <h3 class="font-semibold text-lg text-gray-800 mb-4">Tren Jumlah Kajian</h3>
                <div id="echart-barenbang-kajian-trend" style="height: 350px;"></div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md">
                 <a href="{{ route('barenbang.jumlah-kajian-rekomendasi.index') }}" class="stat-card-link-wrapper">
                <div class="stat-card">
                    <div class="stat-card-info">
                        <p class="stat-card-title">Total Rekomendasi Kebijakan</p>
                        <p class="stat-card-value">{{ number_format($totalRekomendasi ?? 0) }}</p>
                    </div>
                    <div class="stat-card-icon-wrapper bg-green-100">
                        <i class="ri-link-m text-green-500 text-2xl"></i>
                    </div>
                </div>
                <div class="stat-card-footer"></div>
            </a>
                <h3 class="font-semibold text-lg text-gray-800 mb-4">Tren Jumlah Rekomendasi</h3>
                <div id="echart-barenbang-rekomendasi-trend" style="height: 350px;"></div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md">
                  <a href="{{ route('barenbang.aplikasi-integrasi-siapkerja.index') }}" class="stat-card-link-wrapper">
                <div class="stat-card">
                    <div class="stat-card-info">
                        <p class="stat-card-title">Total Aplikasi Terintegrasi SiapKerja</p>
                        <p class="stat-card-value">{{ number_format($totalAplikasiTerintegrasi ?? 0) }}</p>
                    </div>
                    <div class="stat-card-icon-wrapper bg-green-100">
                        <i class="ri-link-m text-green-500 text-2xl"></i>
                    </div>
                </div>
                <div class="stat-card-footer"></div>
            </a>
                <h3 class="font-semibold text-lg text-gray-800 mb-4">Tren Aplikasi Terintegrasi</h3>
                <div id="echart-barenbang-aplikasi-trend" style="height: 350px;"></div>
            </div>
        </div>
    

    <hr class="my-8 border-gray-300">

    {{-- Filter dan Seksi Data Ketenagakerjaan (Sakernas) --}}
    <section>
    <h2 class="text-xl font-semibold text-gray-800 mb-4 mt-6">Data Ketenagakerjaan (Sumber: Sakernas BPS)</h2>
    {{-- Filter untuk Data Ketenagakerjaan (Sakernas) --}}
    {{-- Letakkan form ini di bawah form filter Kajian/Aplikasi --}}
        <form method="GET" action="{{ route('barenbang.dashboard') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full mb-6">
            {{-- Tahun --}}
            <div class="flex-1 w-full sm:w-auto">
                {{-- <label for="tahun_sakernas" class="sr-only">Tahun</label> --}}
                {{-- Menggunakan class dari UI baru --}}
                <select name="tahun_sakernas" id="tahun_sakernas" class="form-input w-full px-3 py-2 border border-gray-300 rounded-md text-sm shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    {{-- Logika & Variabel dari form Sakernas --}}
                    @foreach ($availableYearsSakernas as $year)
                        <option value="{{ $year }}" {{ $selectedYearSakernas == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Periode Sakernas --}}
            <div class="flex-1 w-full sm:w-auto">
                {{-- <label for="periode_sakernas" class="sr-only">Periode Sakernas</label> --}}
                {{-- Menggunakan class dari UI baru --}}
                <select name="periode_sakernas" id="periode_sakernas" class="form-input w-full px-3 py-2 border border-gray-300 rounded-md text-sm shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    {{-- Opsi statis & logika dari form Sakernas --}}
                    <option value="">Februari & Agustus</option>
                    <option value="feb" {{ $selectedPeriodeSakernas == 'feb' ? 'selected' : '' }}>Februari</option>
                    <option value="ags" {{ $selectedPeriodeSakernas == 'ags' ? 'selected' : '' }}>Agustus</option>
                </select>
            </div>
            
            {{-- Tombol Aksi --}}
            <div class="flex items-center gap-2 w-full sm:w-auto">
                {{-- Tombol submit dengan teks dari Sakernas & class dari UI baru --}}
                <button type="submit" class="w-full sm:w-auto text-sm font-medium text-filter-btn-apply-text bg-filter-btn-apply-bg border border-filter-btn-apply-border hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-md px-4 py-2 transition-colors duration-200">
                    Filter Sakernas
                </button>
                {{-- Tombol clear yang mengarah ke route barenbang --}}
                <a href="{{ route('barenbang.dashboard') }}" class="w-full sm:w-auto text-center text-sm font-medium text-filter-btn-clear-text bg-filter-btn-clear-bg border border-filter-btn-clear-border hover:bg-red-200 focus:ring-4 focus:outline-none focus:ring-red-100 rounded-md px-4 py-2 transition-colors duration-200">
                    Bersihkan
                </a>
            </div>
        </form>

        
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        @if(isset($latestSakernasData))
             <div class="stat-card-link-wrapper">
                <div class="stat-card">
                    <div class="stat-card-info">
                        <p class="stat-card-title">TPAK ({{ $latestSakernasData->bulan == 2 ? 'Feb' : 'Ags' }} {{ $latestSakernasData->tahun }})</p>
                        <p class="stat-card-value">{{ number_format($latestSakernasData->tpak ?? 0, 2) }}%</p>
                    </div>
                    <div class="stat-card-icon-wrapper bg-red-100">
                        <i class="ri-user-voice-line text-red-500 text-2xl"></i>
                    </div>
                </div>
                <div class="stat-card-footer"></div>
            </div>
             <div class="stat-card-link-wrapper">
                <div class="stat-card">
                    <div class="stat-card-info">
                        <p class="stat-card-title">TPT ({{ $latestSakernasData->bulan == 2 ? 'Feb' : 'Ags' }} {{ $latestSakernasData->tahun }})</p>
                        <p class="stat-card-value">{{ number_format($latestSakernasData->tpt ?? 0, 2) }}%</p>
                    </div>
                    <div class="stat-card-icon-wrapper bg-yellow-100">
                        <i class="ri-user-unfollow-line text-yellow-500 text-2xl"></i>
                    </div>
                </div>
                <div class="stat-card-footer"></div>
            </div>
            @endif
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="font-semibold text-lg text-gray-800 mb-4">Data Angkatan Kerja & Bekerja (Sakernas)</h3>
                <div id="echart-barenbang-sakernas-angkatan-bekerja" style="height: 400px;"></div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="font-semibold text-lg text-gray-800 mb-4">Tingkat Partisipasi & Pengangguran (Sakernas)</h3>
                <div id="echart-barenbang-sakernas-tpak-tpt" style="height: 400px;"></div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        
        // Fungsi untuk membuat chart tren bulanan & kumulatif reguler
        function createMultiSeriesChart(elementId, labels, seriesConfig, yAxisName = 'Jumlah') {
            const chartDom = document.getElementById(elementId);
            if (!chartDom) { return; }
            let existingChart = echarts.getInstanceByDom(chartDom);
            if (existingChart) { existingChart.dispose(); }
            const myChart = echarts.init(chartDom);
            
            const series = seriesConfig.map(s => ({
                name: s.name, type: s.type, yAxisIndex: s.yAxisIndex || 0, stack: s.stack || null,
                smooth: s.type === 'line', data: s.data, itemStyle: { color: s.color }, lineStyle: { color: s.color, width: s.lineWidth || 2},
                symbol: 'circle', symbolSize: s.symbolSize || (s.type === 'line' ? 6 : 0)
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

        // Fungsi untuk membuat chart Data Ketenagakerjaan (bisa line atau bar)
        function createSakernasChart(elementId, labels, seriesDataArray, yAxisName = 'Ribu Jiwa / Persen') {
            const chartDom = document.getElementById(elementId);
            if (!chartDom) { return; }
            let existingChart = echarts.getInstanceByDom(chartDom);
            if (existingChart) { existingChart.dispose(); }
            const myChart = echarts.init(chartDom);

            const series = seriesDataArray.map(s => ({
                name: s.name,
                type: s.type || 'line', // Default ke line, bisa di-override jadi 'bar'
                smooth: s.type === 'line' || s.smooth === undefined ? true : s.smooth,
                data: s.data,
                itemStyle: { color: s.color },
                lineStyle: { color: s.color, width: s.lineWidth || 2 },
                barWidth: s.type === 'bar' ? '30%' : undefined,
                label: { show: s.showLabel || false, position: 'top' }
            }));
            const legendData = series.map(s => s.name);
            const option = {
                tooltip: { trigger: 'axis', axisPointer: { type: 'cross' } },
                legend: { data: legendData, bottom: 0, type: 'scroll' },
                grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
                xAxis: [{ type: 'category', data: labels, axisPointer: { type: 'shadow' } }],
                yAxis: { type: 'value', name: yAxisName, min: 0, axisLabel: { formatter: '{value}' } },
                series: series
            };
            myChart.setOption(option);
            window.addEventListener('resize', () => myChart.resize());
        }


        const chartData = @json($chartData ?? null);

        if (!chartData) {
            console.error('Variabel chartData utama tidak tersedia dari controller.');
            return;
        }

        // Fungsi render helper untuk chart tren reguler
        function renderRegularTrendChart(chartId, dataKey, seriesName, barColor, lineColor, yAxisName = 'Jumlah') {
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

        // Render Chart Kajian, Rekomendasi, Aplikasi
        renderRegularTrendChart('echart-barenbang-kajian-trend', 'kajian', 'Kajian', '#3b82f6', '#1e40af');
        renderRegularTrendChart('echart-barenbang-rekomendasi-trend', 'rekomendasi', 'Rekomendasi', '#10b981', '#059669');
        renderRegularTrendChart('echart-barenbang-aplikasi-trend', 'aplikasi', 'Aplikasi Terintegrasi', '#f59e0b', '#d97706');

        // Render Chart Data Ketenagakerjaan (Sakernas)
        if (chartData.sakernas && chartData.sakernas.labels && chartData.sakernas.labels.length > 0) {
            const sakernasAngkatanBekerjaEl = document.getElementById('echart-barenbang-sakernas-angkatan-bekerja');
            if (sakernasAngkatanBekerjaEl) {
                 if (chartData.sakernas.angkatan_kerja.some(val => val > 0) || chartData.sakernas.bekerja.some(val => val > 0)) {
                    createSakernasChart('echart-barenbang-sakernas-angkatan-bekerja', chartData.sakernas.labels, [
                        { name: 'Angkatan Kerja (Ribu Jiwa)', type: 'bar', data: chartData.sakernas.angkatan_kerja, color: '#6366f1' },
                        { name: 'Bekerja (Ribu Jiwa)', type: 'bar', data: chartData.sakernas.bekerja, color: '#ec4899' },
                        { name: 'Pengangguran Terbuka (Ribu Jiwa)', type: 'line', data: chartData.sakernas.pengangguran_terbuka, color: '#ef4444', lineWidth: 3 },
                        { name: 'Bukan Angkatan Kerja (Ribu Jiwa)', type: 'line', data: chartData.sakernas.bukan_angkatan_kerja, color: '#f59e0b', lineWidth: 3 }
                    ], 'Ribu Jiwa');
                 } else {
                    sakernasAngkatanBekerjaEl.innerHTML = `<p class="text-center text-gray-500 py-5">Tidak ada data Angkatan Kerja & Bekerja.</p>`;
                 }
            }

            const sakernasTpakTptEl = document.getElementById('echart-barenbang-sakernas-tpak-tpt');
            if (sakernasTpakTptEl) {
                if (chartData.sakernas.tpak.some(val => val > 0) || chartData.sakernas.tpt.some(val => val > 0)) {
                    createSakernasChart('echart-barenbang-sakernas-tpak-tpt', chartData.sakernas.labels, [
                        { name: 'TPAK (%)', type: 'line', data: chartData.sakernas.tpak, color: '#8b5cf6', lineWidth: 3 },
                        { name: 'TPT (%)', type: 'line', data: chartData.sakernas.tpt, color: '#22c55e', lineWidth: 3 },
                        { name: 'Tingkat Kesempatan Kerja (%)', type: 'line', data: chartData.sakernas.tingkat_kesempatan_kerja, color: '#06b6d4', lineWidth: 3 }
                    ], 'Persentase (%)');
                } else {
                    sakernasTpakTptEl.innerHTML = `<p class="text-center text-gray-500 py-5">Tidak ada data TPAK & TPT.</p>`;
                }
            }
        } else {
            console.warn('Data Sakernas tidak lengkap atau label kosong.');
            const sakernasChartIds = ['echart-barenbang-sakernas-angkatan-bekerja', 'echart-barenbang-sakernas-tpak-tpt'];
            sakernasChartIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) { el.innerHTML = `<p class="text-center text-gray-500 py-5">Data Sakernas tidak tersedia.</p>`; }
            });
        }

    });
</script>
@endpush