@extends('layouts.app')

@section('title', 'Dashboard Barenbang')
@section('page_title', 'Barenbang')

@section('header_filters')
    {{-- Bagian ini dikosongkan karena filter dipindahkan ke @section('content') --}}
@endsection

@section('content')
<div class="space-y-8">

    {{-- Filter dan Seksi Data Barenbang Utama (Kajian & Aplikasi) --}}
    <section>
        {{-- Filter untuk Data Barenbang Utama (Kajian & Aplikasi) --}}
        <form method="GET" action="{{ route('barenbang.dashboard') }}" class="w-full mb-6">
            <h3 class="text-md font-semibold text-gray-700 mb-3">Filter Data Kajian & Aplikasi Terintegrasi:</h3>
            <div class="p-4 bg-white rounded-lg shadow">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 items-end">
                    <div class="flex-grow">
                        <label for="year_filter_main" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                        <select name="year_filter_main" id="year_filter_main" class="form-input mt-1 w-full bg-white border-gray-300">
                            @if($availableYearsMain->isEmpty() && $selectedYearMain)
                                 <option value="{{ $selectedYearMain }}" selected>{{ $selectedYearMain }}</option>
                            @elseif($availableYearsMain->isEmpty())
                                <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                            @else
                                @foreach($availableYearsMain as $year)
                                    <option value="{{ $year }}" {{ $selectedYearMain == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="flex-grow">
                        <label for="month_filter_main" class="text-sm text-gray-600 whitespace-nowrap">Bulan:</label>
                        <select name="month_filter_main" id="month_filter_main" class="form-input mt-1 w-full bg-white border-gray-300">
                            <option value="">Semua Bulan</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $selectedMonthMain == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}</option>
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
            {{-- Hidden input untuk mempertahankan filter sakernas saat filter utama diterapkan --}}
            @if(request('year_filter_sakernas')) <input type="hidden" name="year_filter_sakernas" value="{{ request('year_filter_sakernas') }}"> @endif
        </form>

        <h2 class="text-xl font-semibold text-gray-800 mb-4 mt-6">Kinerja Umum Barenbang</h2>
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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-5 rounded-lg shadow">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-sm font-medium text-gray-600">Jumlah Kajian & Rekomendasi Kebijakan</h3>
                    <a href="{{ route('barenbang.jumlah-kajian-rekomendasi.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
                </div>
                <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalKajianRekomendasi ?? 0) }}</div>
                <p class="text-xs text-gray-400 mt-1">{{ $periodTextMain }}</p>
            </div>
            <div class="bg-white p-5 rounded-lg shadow">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-sm font-medium text-gray-600">Jumlah Aplikasi Terintegrasi SiapKerja</h3>
                    <a href="{{ route('barenbang.aplikasi-integrasi-siapkerja.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
                </div>
                <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalAplikasiTerintegrasi ?? 0) }}</div>
                <p class="text-xs text-gray-400 mt-1">{{ $periodTextMain }}</p>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <div class="bg-white p-5 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Jumlah Kajian & Rekomendasi ({{ $yearToDisplayMain }})</h3>
                <div id="echart-barenbang-kajian-trend" style="width: 100%; height: 300px;"></div>
            </div>
            <div class="bg-white p-5 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Aplikasi Terintegrasi SiapKerja ({{ $yearToDisplayMain }})</h3>
                <div id="echart-barenbang-aplikasi-trend" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
    </section>

    <hr class="my-8 border-gray-300">

    {{-- Filter dan Seksi Data Ketenagakerjaan (Sakernas) --}}
    <section>
        <form method="GET" action="{{ route('barenbang.dashboard') }}" class="w-full mb-6">
            <h3 class="text-md font-semibold text-gray-700 mb-3">Filter Data Ketenagakerjaan (Sakernas):</h3>
            <div class="p-4 bg-white rounded-lg shadow">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 items-end">
                    <div class="flex-grow">
                        <label for="year_filter_sakernas" class="text-sm text-gray-600 whitespace-nowrap">Tahun Sakernas:</label>
                        <select name="year_filter_sakernas" id="year_filter_sakernas" class="form-input mt-1 w-full bg-white border-gray-300">
                             @if($availableYearsSakernas->isEmpty() && $selectedYearSakernas)
                                 <option value="{{ $selectedYearSakernas }}" selected>{{ $selectedYearSakernas }}</option>
                            @elseif($availableYearsSakernas->isEmpty())
                                <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                            @else
                                @foreach($availableYearsSakernas as $year)
                                    <option value="{{ $year }}" {{ $selectedYearSakernas == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="flex items-center space-x-2 pt-5 md:col-span-2">
                        <button type="submit" class="w-full sm:w-auto px-4 py-1.5 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
                            <i class="ri-filter-3-line mr-1"></i> Terapkan Filter
                        </button>
                         <a href="{{ route('barenbang.dashboard') }}" class="w-full sm:w-auto px-4 py-1.5 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
                            Reset Semua Filter
                        </a>
                    </div>
                </div>
            </div>
            {{-- Hidden input untuk mempertahankan filter utama saat filter sakernas diterapkan --}}
            @if(request('year_filter_main')) <input type="hidden" name="year_filter_main" value="{{ request('year_filter_main') }}"> @endif
            @if(request('month_filter_main')) <input type="hidden" name="month_filter_main" value="{{ request('month_filter_main') }}"> @endif
        </form>

        <h2 class="text-xl font-semibold text-gray-800 mb-4 mt-6">Data Ketenagakerjaan (Sumber: Sakernas BPS)</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white p-5 rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-600">Tingkat Partisipasi Angkatan Kerja (TPAK)</h3>
                <div class="text-3xl font-semibold text-gray-800">{{ number_format($latestTpak ?? 0, 2) }}%</div>
                <p class="text-xs text-gray-400 mt-1">Periode: {{ $latestSakernasPeriod ?: 'Data belum tersedia' }}</p>
            </div>
            <div class="bg-white p-5 rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-600">Tingkat Pengangguran Terbuka (TPT)</h3>
                <div class="text-3xl font-semibold text-gray-800">{{ number_format($latestTpt ?? 0, 2) }}%</div>
                <p class="text-xs text-gray-400 mt-1">Periode: {{ $latestSakernasPeriod ?: 'Data belum tersedia' }}</p>
            </div>
        </div>

        @if(!empty($tpakTptChartLabels) && count($tpakTptChartLabels) > 0)
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren TPAK vs TPT (Tahun {{ $selectedYearSakernas }})</h3>
            <div id="echart-barenbang-tpak-tpt-selected-year" style="width: 100%; height: 350px;"></div>
        </div>
        @else
        <div class="bg-white p-5 rounded-lg shadow text-center">
            <p class="text-gray-500">Data tren TPAK vs TPT untuk tahun {{ $selectedYearSakernas }} tidak tersedia.</p>
        </div>
        @endif

        @if(!empty($tpakMultiYearLabels) && count($tpakMultiYearLabels) > 0)
        <div class="bg-white p-5 rounded-lg shadow mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren TPAK vs TPT (Beberapa Tahun Terakhir)</h3>
            <div id="echart-barenbang-tpak-tpt-multi-year" style="width: 100%; height: 350px;"></div>
        </div>
        @else
         <div class="bg-white p-5 rounded-lg shadow mt-6 text-center">
            <p class="text-gray-500">Data tren TPAK vs TPT multi-tahun tidak tersedia.</p>
        </div>
        @endif
    </section>
</div>
@endsection

@push('scripts')
{{-- ECharts sudah di-include di layouts.app.blade.php --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Warna untuk tema terang (sesuai phi.blade.php yang tidak menentukan warna secara eksplisit, jadi kita gunakan warna default ECharts atau warna abu-abu yang umum)
        const textColor = '#374151';       // Tailwind gray-700 (untuk teks utama, label sumbu)
        const axisLineColor = '#D1D5DB';  // Tailwind gray-300 (untuk garis sumbu)
        const legendTextColor = '#4B5563'; // Tailwind gray-600 (untuk teks legenda)

        // Chart untuk Kajian & Rekomendasi
        var kajianChartDom = document.getElementById('echart-barenbang-kajian-trend');
        if (kajianChartDom) {
            var kajianChart = echarts.init(kajianChartDom, null); // null atau 'light' untuk tema terang
            var kajianOption = {
                tooltip: { trigger: 'axis', formatter: function (params) { let res = params[0].name + '<br/>' + params[0].seriesName + ' : ' + params[0].value.toLocaleString('id-ID'); return res; } },
                legend: { data: ['Jumlah Kajian/Rekomendasi'], textStyle: { color: legendTextColor }, bottom: 0 },
                grid: { left: '3%', right: '4%', bottom: '10%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: false, data: @json($kajianChartLabels), axisLine: { lineStyle: { color: axisLineColor } }, axisLabel: { color: textColor } },
                yAxis: { type: 'value', name: 'Jumlah', min: 0, axisLine: { lineStyle: { color: axisLineColor } }, axisLabel: { color: textColor, formatter: function (value) { return value.toLocaleString('id-ID'); } }, nameTextStyle: { color: textColor } },
                series: [{
                    name: 'Jumlah Kajian/Rekomendasi', type: 'line', smooth: true,
                    data: @json($kajianChartDataValues),
                    itemStyle: { color: '#3b82f6' }, // Warna biru primary
                    areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: 'rgba(59, 130, 246, 0.5)'}, {offset: 1, color: 'rgba(59, 130, 246, 0.1)'}])}
                }]
            };
            kajianChart.setOption(kajianOption);
            window.addEventListener('resize', () => kajianChart.resize());
        }

        // Chart untuk Aplikasi Terintegrasi
        var aplikasiChartDom = document.getElementById('echart-barenbang-aplikasi-trend');
        if (aplikasiChartDom) {
            var aplikasiChart = echarts.init(aplikasiChartDom, null);
            var aplikasiOption = {
                tooltip: { trigger: 'axis', formatter: function (params) { let res = params[0].name + '<br/>' + params[0].seriesName + ' : ' + params[0].value.toLocaleString('id-ID'); return res; } },
                legend: { data: ['Jumlah Aplikasi Terintegrasi'], textStyle: { color: legendTextColor }, bottom: 0 },
                grid: { left: '3%', right: '4%', bottom: '10%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: false, data: @json($aplikasiChartLabels), axisLine: { lineStyle: { color: axisLineColor } }, axisLabel: { color: textColor } },
                yAxis: { type: 'value', name: 'Jumlah', min: 0, axisLine: { lineStyle: { color: axisLineColor } }, axisLabel: { color: textColor, formatter: function (value) { return value.toLocaleString('id-ID'); } }, nameTextStyle: { color: textColor } },
                series: [{
                    name: 'Jumlah Aplikasi Terintegrasi', type: 'line', smooth: true,
                    data: @json($aplikasiChartDataValues),
                    itemStyle: { color: '#10b981' }, // Warna hijau emerald
                    areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: 'rgba(16, 185, 129, 0.5)'}, {offset: 1, color: 'rgba(16, 185, 129, 0.1)'}])}
                }]
            };
            aplikasiChart.setOption(aplikasiOption);
            window.addEventListener('resize', () => aplikasiChart.resize());
        }

        // Chart Tren TPAK vs TPT (Tahun Terpilih)
        var tpakTptChartDom = document.getElementById('echart-barenbang-tpak-tpt-selected-year');
        if (tpakTptChartDom && @json($tpakTptChartLabels).length > 0) {
            var tpakTptChart = echarts.init(tpakTptChartDom, null);
            var tpakTptOption = {
                tooltip: { trigger: 'axis', formatter: function (params) { let res = params[0].name + '<br/>'; params.forEach(function(item){ res += item.seriesName + ' : ' + item.value.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '%<br/>'; }); return res; } },
                legend: { data: ['TPAK (%)', 'TPT (%)'], top: 'bottom', textStyle: { color: legendTextColor } },
                grid: { left: '3%', right: '4%', bottom: '10%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: true, data: @json($tpakTptChartLabels), axisLine: { lineStyle: { color: axisLineColor } }, axisLabel: { color: textColor } },
                yAxis: { type: 'value', name: 'Persentase (%)', min: 0, max: 100, axisLine: { lineStyle: { color: axisLineColor } }, axisLabel: { color: textColor, formatter: '{value}%' }, nameTextStyle: { color: textColor } },
                series: [
                    {
                        name: 'TPAK (%)', type: 'line', smooth: true,
                        data: @json($tpakChartData),
                        itemStyle: { color: '#ef4444' } // Warna merah
                    },
                    {
                        name: 'TPT (%)', type: 'line', smooth: true,
                        data: @json($tptChartData),
                        itemStyle: { color: '#f59e0b' } // Warna kuning/amber
                    }
                ]
            };
            tpakTptChart.setOption(tpakTptOption);
            window.addEventListener('resize', () => tpakTptChart.resize());
        }

        // Chart Tren TPAK vs TPT (Multi Tahun)
        var tpakTptMultiYearChartDom = document.getElementById('echart-barenbang-tpak-tpt-multi-year');
        if (tpakTptMultiYearChartDom && @json($tpakMultiYearLabels).length > 0) {
            var tpakTptMultiYearChart = echarts.init(tpakTptMultiYearChartDom, null);
            var tpakTptMultiYearOption = {
                tooltip: { trigger: 'axis', formatter: function (params) { let res = params[0].name + '<br/>'; params.forEach(function(item){ res += item.seriesName + ' : ' + item.value.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '%<br/>'; }); return res; } },
                legend: { data: ['TPAK (%)', 'TPT (%)'], top: 'bottom', textStyle: { color: legendTextColor } },
                grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: true, data: @json($tpakMultiYearLabels), axisLine: { lineStyle: { color: axisLineColor } }, axisLabel: { color: textColor, interval: 0, rotate: 30, fontSize: 10 } },
                yAxis: { type: 'value', name: 'Persentase (%)', min: 0, max: 100, axisLine: { lineStyle: { color: axisLineColor } }, axisLabel: { color: textColor, formatter: '{value}%' }, nameTextStyle: { color: textColor } },
                series: [
                    {
                        name: 'TPAK (%)', type: 'line', smooth: true,
                        data: @json($tpakMultiYearValues),
                        itemStyle: { color: '#8b5cf6' } // Warna violet
                    },
                    {
                        name: 'TPT (%)', type: 'line', smooth: true,
                        data: @json($tptMultiYearValues),
                        itemStyle: { color: '#22c55e' } // Warna hijau
                    }
                ]
            };
            tpakTptMultiYearChart.setOption(tpakTptMultiYearOption);
            window.addEventListener('resize', () => tpakTptMultiYearChart.resize());
        }
    });
</script>
@endpush