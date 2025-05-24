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
            $periodText = "Periode: Januari - " . $endMonthName . " " . $yearToDisplay;
        } else {
            $periodText = "Sepanjang Tahun " . $yearToDisplay;
        }
    @endphp

    {{-- Kartu Statistik Sekretariat Jenderal --}}
    {{-- Baris Pertama: 3 Kartu --}}
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ route('sekretariat-jenderal.progress-mou.index') }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">Jumlah MoU</p>
                    <p class="stat-card-value">{{ number_format($totalMoU ?? 0) }}</p>
                </div>
                <div class="stat-card-icon-wrapper bg-blue-100">
                    <i class="ri-honour-line text-blue-500 text-2xl"></i>
                </div>
            </div>
            <div class="stat-card-footer">{{ $periodText }}</div>
        </a>

        <a href="{{ route('sekretariat-jenderal.jumlah-regulasi-baru.index') }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">Jumlah Regulasi Baru</p>
                    <p class="stat-card-value">{{ number_format($totalRegulasi ?? 0) }}</p>
                </div>
                <div class="stat-card-icon-wrapper bg-green-100">
                    <i class="ri-file-list-3-line text-green-500 text-2xl"></i>
                </div>
            </div>
            <div class="stat-card-footer">{{ $periodText }}</div>
        </a>
        
        <a href="{{ route('sekretariat-jenderal.jumlah-penanganan-kasus.index') }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">Jumlah Penanganan Kasus</p>
                    <p class="stat-card-value">{{ number_format($totalKasusDitangani ?? 0) }}</p>
                </div>
                <div class="stat-card-icon-wrapper bg-purple-100">
                    <i class="ri-scales-2-line text-purple-500 text-2xl"></i>
                </div>
            </div>
            <div class="stat-card-footer">{{ $periodText }}</div>
        </a>
    </section>

    {{-- Baris Kedua: 3 Kartu --}}
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
        <a href="{{ route('sekretariat-jenderal.penyelesaian-bmn.index') }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">Jumlah Penyelesaian BMN</p>
                    <p class="stat-card-value">Rp {{ number_format($totalNilaiPenyelesaianBmn ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="stat-card-icon-wrapper bg-orange-100">
                    <i class="ri-archive-drawer-line text-orange-500 text-2xl"></i>
                </div>
            </div>
            <div class="stat-card-footer">{{ $periodText }}</div>
        </a>
        
        <a href="{{ route('sekretariat-jenderal.persentase-kehadiran.index') }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">Total SDM Hadir (WFO)</p>
                    <p class="stat-card-value">{{ number_format($totalWFO ?? 0) }} <span class="text-sm">Orang</span></p>
                </div>
                <div class="stat-card-icon-wrapper bg-yellow-100">
                    <i class="ri-user-follow-line text-yellow-500 text-2xl"></i>
                </div>
            </div>
            <div class="stat-card-footer">{{ $periodText }}</div>
        </a>

        <a href="{{ route('sekretariat-jenderal.monev-monitoring-media.index') }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">Monev Monitoring Media</p>
                    <p class="stat-card-value">{{ number_format($totalBeritaMonev ?? 0) }}</p>
                </div>
                <div class="stat-card-icon-wrapper bg-teal-100">
                    <i class="ri-rss-line text-teal-500 text-2xl"></i>
                </div>
            </div>
            <div class="stat-card-footer">{{ $periodText }}</div>
        </a>
    </section>

    {{-- Baris Ketiga: 2 Kartu --}}
    {{-- Kita gunakan grid-cols-1 md:grid-cols-2 lg:grid-cols-3 agar 2 kartu terakhir tetap rapi jika layar lebar --}}
    {{-- atau bisa juga lg:grid-cols-2 jika ingin pas 2 kartu di layar besar --}}
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
        <a href="{{ route('sekretariat-jenderal.lulusan-polteknaker-bekerja.index') }}" class="stat-card-link-wrapper lg:col-span-1"> {{-- lg:col-span-1 agar tidak terlalu lebar jika hanya 2 item --}}
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">Lulusan Polteknaker Bekerja</p>
                    <p class="stat-card-value">{{ number_format($totalLulusanBekerja ?? 0) }} <span class="text-sm">Orang</span></p>
                </div>
                <div class="stat-card-icon-wrapper bg-indigo-100">
                    <i class="ri-user-star-line text-indigo-500 text-2xl"></i>
                </div>
            </div>
            <div class="stat-card-footer">{{ $periodText }}</div>
        </a>

        <a href="{{ route('sekretariat-jenderal.sdm-mengikuti-pelatihan.index') }}" class="stat-card-link-wrapper lg:col-span-1">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">SDM Mengikuti Pelatihan</p>
                    <p class="stat-card-value">{{ number_format($totalSdmPelatihan ?? 0) }}</p>
                </div>
                <div class="stat-card-icon-wrapper bg-pink-100">
                    <i class="ri-team-line text-pink-500 text-2xl"></i>
                </div>
            </div>
            <div class="stat-card-footer">{{ $periodText }}</div>
        </a>
    </section>


    {{-- Bagian Grafik --}}
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Jumlah MoU ({{ $yearToDisplay }})</h3>
            <div id="echart-sekjen-mou-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Jumlah Regulasi Baru ({{ $yearToDisplay }})</h3>
            <div id="echart-sekjen-regulasi-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Jumlah Penanganan Kasus ({{ $yearToDisplay }})</h3>
            <div id="echart-sekjen-kasus-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Jumlah Penyelesaian BMN ({{ $yearToDisplay }})</h3>
            <div id="echart-sekjen-bmn-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren % Kehadiran ({{ $yearToDisplay }})</h3>
            <div id="echart-sekjen-kehadiran-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Monev Monitoring Media ({{ $yearToDisplay }})</h3>
            <div id="echart-sekjen-media-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Lulusan Polteknaker Bekerja ({{ $yearToDisplay }})</h3>
            <div id="echart-sekjen-polteknaker-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren SDM Mengikuti Pelatihan ({{ $yearToDisplay }})</h3>
            <div id="echart-sekjen-sdm-pelatihan-trend" style="width: 100%; height: 300px;"></div>
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

        function createChart(chartId, legendDataName, chartDataLabels, chartDataValues, itemColor, areaColorStops, yAxisFormatter = null) {
            var chartDom = document.getElementById(chartId);
            if (chartDom) {
                var myChart = echarts.init(chartDom, null);
                var yAxisOptions = {
                    type: 'value', name: 'Jumlah', min: 0,
                    axisLine: { lineStyle: { color: axisLineColor } },
                    axisLabel: { color: textColor, formatter: yAxisFormatter ? yAxisFormatter : function (value) { return value.toLocaleString('id-ID'); } },
                    nameTextStyle: { color: textColor }
                };
                if (yAxisFormatter && legendDataName.includes('%')) { yAxisOptions.max = 100; }

                var option = {
                    tooltip: { 
                        trigger: 'axis', 
                        formatter: function (params) { 
                            let value = params[0].value;
                            let formattedValue = yAxisFormatter ? yAxisFormatter(value) : value.toLocaleString('id-ID');
                            return params[0].name + '<br/>' + params[0].seriesName + ' : ' + formattedValue; 
                        } 
                    },
                    legend: { data: [legendDataName], textStyle: { color: legendTextColor }, bottom: 0 },
                    grid: { left: '3%', right: '4%', bottom: '10%', containLabel: true },
                    xAxis: { type: 'category', boundaryGap: false, data: chartDataLabels, axisLine: { lineStyle: { color: axisLineColor } }, axisLabel: { color: textColor } },
                    yAxis: yAxisOptions,
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

        // Inisialisasi semua 8 chart
        // Pastikan nama variabel chart (cth: $mouChartLabels) sesuai dengan yang dikirim controller
        createChart('echart-sekjen-mou-trend', 'Jumlah MoU', @json($mouChartLabels ?? []), @json($mouChartDataValues ?? []), '#3b82f6', [{offset: 0, color: 'rgba(59, 130, 246, 0.5)'}, {offset: 1, color: 'rgba(59, 130, 246, 0.1)'}]);
        createChart('echart-sekjen-regulasi-trend', 'Jumlah Regulasi Baru', @json($regulasiPerJenis->pluck('name') ?? []), @json($regulasiPerJenis ?? []), '#10b981', [{offset: 0, color: 'rgba(16, 185, 129, 0.5)'}, {offset: 1, color: 'rgba(16, 185, 129, 0.1)'}]);
        createChart('echart-sekjen-kasus-trend', 'Jumlah Penanganan Kasus', @json($kasusChartLabels ?? []), @json($kasusChartDataValues ?? []), '#8b5cf6', [{offset: 0, color: 'rgba(139, 92, 246, 0.5)'}, {offset: 1, color: 'rgba(139, 92, 246, 0.1)'}]);
        createChart('echart-sekjen-bmn-trend', 'Jumlah Penyelesaian BMN', @json($bmnChartLabels ?? []), @json($bmnChartDataValues ?? []), '#f97316', [{offset: 0, color: 'rgba(249, 115, 22, 0.5)'}, {offset: 1, color: 'rgba(249, 115, 22, 0.1)'}]);
        createChart('echart-sekjen-kehadiran-trend', '% Kehadiran', @json($kehadiranChartLabels ?? []), @json($kehadiranWFOData ?? []), '#f59e0b', [{offset: 0, color: 'rgba(245, 158, 11, 0.5)'}, {offset: 1, color: 'rgba(245, 158, 11, 0.1)'}], function (value) { return parseFloat(value).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '%'; });
        createChart('echart-sekjen-media-trend', 'Monev Monitoring Media', @json($monevMediaChartLabels ?? []), @json($monevMediaChartDataValues ?? []), '#06b6d4', [{offset: 0, color: 'rgba(6, 182, 212, 0.5)'}, {offset: 1, color: 'rgba(6, 182, 212, 0.1)'}]);
        createChart('echart-sekjen-polteknaker-trend', 'Lulusan Polteknaker Bekerja', @json($lulusanPolteknakerChartLabels ?? []), @json($lulusanPolteknakerChartDataValues ?? []), '#6366f1', [{offset: 0, color: 'rgba(99, 102, 241, 0.5)'}, {offset: 1, color: 'rgba(99, 102, 241, 0.1)'}]);
        createChart('echart-sekjen-sdm-pelatihan-trend', 'SDM Mengikuti Pelatihan', @json($sdmPelatihanChartLabels ?? []), @json($sdmPelatihanChartDataValues ?? []), '#ec4899', [{offset: 0, color: 'rgba(236, 72, 153, 0.5)'}, {offset: 1, color: 'rgba(236, 72, 153, 0.1)'}]);
    });
</script>
@endpush