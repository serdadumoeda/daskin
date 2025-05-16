@extends('layouts.app')

@section('title', 'Dashboard Binwasnaker & K3')
@section('page_title', 'Binwasnaker & K3')

@section('header_filters')
    <form method="GET" action="{{ route('binwasnaker.dashboard') }}" class="w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 items-end">
            <div class="flex-grow">
                <label for="year_filter_binwasnaker" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                <select name="year_filter" id="year_filter_binwasnaker" class="form-input mt-1 w-full bg-white">
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
                <label for="month_filter_binwasnaker" class="text-sm text-gray-600 whitespace-nowrap">Bulan:</label>
                <select name="month_filter" id="month_filter_binwasnaker" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua Bulan</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $selectedMonth == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex items-center space-x-2 pt-5">
                <button type="submit" class="w-full sm:w-auto px-4 py-1.5 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
                    <i class="ri-filter-3-line mr-1"></i> Terapkan
                </button>
                 <a href="{{ route('binwasnaker.dashboard') }}" class="w-full sm:w-auto px-4 py-1.5 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
                    Reset
                </a>
            </div>
        </div>
    </form>
@endsection

@section('content')
<div class="space-y-6">
    @php
        $yearToDisplayBinwasnaker = $selectedYear ?: date('Y');
        $monthValueBinwasnaker = null; 
        if ($selectedMonth && is_numeric($selectedMonth)) {
            $monthValueBinwasnaker = (int)$selectedMonth;
        }

        if ($monthValueBinwasnaker && $monthValueBinwasnaker >= 1 && $monthValueBinwasnaker <= 12) {
            $endMonthNameBinwasnaker = \Carbon\Carbon::create()->month($monthValueBinwasnaker)->isoFormat('MMMM');
            $periodTextBinwasnaker = "Periode: Januari - " . $endMonthNameBinwasnaker . " " . $yearToDisplayBinwasnaker;
        } else {
            $periodTextBinwasnaker = "Sepanjang Tahun " . $yearToDisplayBinwasnaker;
        }
    @endphp

    {{-- Baris 1: Kartu Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Perusahaan yang melapor WLKP Online</h3>
                <a href="{{ route('binwasnaker.pelaporan-wlkp-online.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalWlkpReported ?? 0) }} <span class="text-sm">Perusahaan</span></div>
            <p class="text-xs text-gray-400 mt-1">{{ $periodTextBinwasnaker }}</p>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Pengaduan Pelanggaran Norma yang Ditindaklanjuti</h3>
                <a href="{{ route('binwasnaker.pengaduan-pelanggaran-norma.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalPengaduanNorma ?? 0) }} <span class="text-sm">Kasus</span></div>
            <p class="text-xs text-gray-400 mt-1">{{ $periodTextBinwasnaker }}</p>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Perusahaan yang menerapkan SMK3</h3>
                <a href="{{ route('binwasnaker.penerapan-smk3.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalPenerapanSmk3 ?? 0) }} <span class="text-sm">Perusahaan</span></div>
            <p class="text-xs text-gray-400 mt-1">{{ $periodTextBinwasnaker }}</p>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Perusahaan yang melakukan self-assessment norma 100</h3>
                <a href="{{ route('binwasnaker.self-assessment-norma100.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalSelfAssessment ?? 0) }} <span class="text-sm">Perusahaan</span></div>
            <p class="text-xs text-gray-400 mt-1">{{ $periodTextBinwasnaker }}</p>
        </div>
    </div>

    {{-- Baris untuk Chart Utama --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Perusahaan yang Melaporkan WLKP online Tahun {{ $selectedYear }}</h3>
            <div id="echart-binwasnaker-wlkp-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Komposisi Pengaduan Pelanggaran Norma ({{ $periodTextBinwasnaker }})</h3>
            <div id="echart-binwasnaker-pengaduan-jenis" style="width: 100%; height: 300px;"></div>
        </div>
    </div>
     <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white p-5 rounded-lg shadow">
            {{-- JUDUL CHART & ID DIUBAH --}}
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Grafik Tren Perusahaan yang menerapkan SMK3 Tahun {{ $selectedYear }}</h3>
            <div id="echart-binwasnaker-smk3-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            {{-- JUDUL CHART & ID DIUBAH --}}
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Grafik Tren Perusahaan yang melakukan self-assessment norma 100 Tahun {{ $selectedYear }}</h3>
            <div id="echart-binwasnaker-sa-trend" style="width: 100%; height: 300px;"></div>
        </div>
    </div>

    {{-- Contoh Jika Ada Chart Tren Tahunan WLKP (perlu data dari controller) --}}
    @if(isset($wlkpAnnualTrendLabels) && isset($wlkpAnnualTrendDataValues) && count($wlkpAnnualTrendLabels) > 0)
    <div class="bg-white p-5 rounded-lg shadow mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Tahunan Perusahaan yang Melaporkan WLKP Online</h3>
        <div id="echart-binwasnaker-wlkp-annual-trend" style="width: 100%; height: 300px;"></div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Chart Tren WLKP Online (Bulanan)
        var wlkpChartDom = document.getElementById('echart-binwasnaker-wlkp-trend');
        if (wlkpChartDom) {
            var wlkpChart = echarts.init(wlkpChartDom);
            var wlkpOption = {
                tooltip: { trigger: 'axis', formatter: function (params) { let res = params[0].name + '<br/>'; params.forEach(function(item){ res += item.seriesName + ' : ' + item.value.toLocaleString('id-ID') + '<br/>'; }); return res; } },
                legend: { data: ['Jml Perusahaan Lapor WLKP'], bottom: 5 },
                grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: false, data: @json($wlkpChartLabels) },
                yAxis: { type: 'value', name: 'Jumlah Perusahaan', min: 0, axisLabel: { formatter: function (value) { return value.toLocaleString('id-ID'); } } },
                series: [{
                    name: 'Jml Perusahaan Lapor WLKP', type: 'line', smooth: true,
                    data: @json($wlkpChartDataValues),
                    itemStyle: { color: '#3b82f6' }, 
                    areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: 'rgba(59, 130, 246, 0.5)'}, {offset: 1, color: 'rgba(59, 130, 246, 0.1)'}])}
                }]
            };
            wlkpChart.setOption(wlkpOption);
            window.addEventListener('resize', () => wlkpChart.resize());
        }

        // 2. Chart Komposisi Pengaduan Pelanggaran Norma
        var pengaduanChartDom = document.getElementById('echart-binwasnaker-pengaduan-jenis');
        if (pengaduanChartDom) {
            var pengaduanChart = echarts.init(pengaduanChartDom);
            var pengaduanOption = {
                tooltip: { trigger: 'item', formatter: function(params) { return `${params.seriesName}<br/>${params.name}: ${params.value.toLocaleString('id-ID')} (${params.percent}%)`; } },
                legend: { orient: 'vertical', left: 'left', type: 'scroll', data: @json(collect($pengaduanChartData)->pluck('name')) },
                series: [{
                    name: 'Jenis Pelanggaran', type: 'pie', radius: '70%', center: ['65%', '50%'],
                    data: @json($pengaduanChartData),
                    emphasis: { itemStyle: { shadowBlur: 10, shadowOffsetX: 0, shadowColor: 'rgba(0, 0, 0, 0.5)' } }
                }]
            };
            pengaduanChart.setOption(pengaduanOption);
            window.addEventListener('resize', () => pengaduanChart.resize());
        }

        // 3. Chart Tren Penerapan SMK3 (BARU - Line Chart)
        var smk3TrendChartDom = document.getElementById('echart-binwasnaker-smk3-trend'); // ID Baru
        if (smk3TrendChartDom) {
            var smk3TrendChart = echarts.init(smk3TrendChartDom);
            var smk3TrendOption = {
                tooltip: { trigger: 'axis', formatter: function (params) { let res = params[0].name + '<br/>'; params.forEach(function(item){ res += item.seriesName + ' : ' + item.value.toLocaleString('id-ID') + '<br/>'; }); return res; } },
                legend: { data: ['Jml Perusahaan SMK3'], bottom: 5 },
                grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: false, data: @json($smk3TrendChartLabels) }, // Data dari controller
                yAxis: { type: 'value', name: 'Jumlah Perusahaan', min: 0, axisLabel: { formatter: function (value) { return value.toLocaleString('id-ID'); } } },
                series: [{
                    name: 'Jml Perusahaan SMK3', type: 'line', smooth: true,
                    data: @json($smk3TrendChartDataValues), // Data dari controller
                    itemStyle: { color: '#10b981' }, // Green
                    areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: 'rgba(16, 185, 129, 0.5)'}, {offset: 1, color: 'rgba(16, 185, 129, 0.1)'}])}
                }]
            };
            smk3TrendChart.setOption(smk3TrendOption);
            window.addEventListener('resize', () => smk3TrendChart.resize());
        }
        
        // 4. Chart Tren Self Assessment Norma 100 (BARU - Line Chart)
        var saTrendChartDom = document.getElementById('echart-binwasnaker-sa-trend'); // ID Baru
        if (saTrendChartDom) {
            var saTrendChart = echarts.init(saTrendChartDom);
            var saTrendOption = {
                tooltip: { trigger: 'axis', formatter: function (params) { let res = params[0].name + '<br/>'; params.forEach(function(item){ res += item.seriesName + ' : ' + item.value.toLocaleString('id-ID') + '<br/>'; }); return res; } },
                legend: { data: ['Jml Perusahaan SA Norma 100'], bottom: 5 },
                grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: false, data: @json($saTrendChartLabels) }, // Data dari controller
                yAxis: { type: 'value', name: 'Jumlah Perusahaan', min: 0, axisLabel: { formatter: function (value) { return value.toLocaleString('id-ID'); } } },
                series: [{
                    name: 'Jml Perusahaan SA Norma 100', type: 'line', smooth: true,
                    data: @json($saTrendChartDataValues), // Data dari controller
                    itemStyle: { color: '#f59e0b' }, // Amber
                    areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: 'rgba(245, 158, 11, 0.5)'}, {offset: 1, color: 'rgba(245, 158, 11, 0.1)'}])}
                }]
            };
            saTrendChart.setOption(saTrendOption);
            window.addEventListener('resize', () => saTrendChart.resize());
        }

        // 5. Chart Tren Tahunan WLKP (jika data ada)
        var wlkpAnnualChartDom = document.getElementById('echart-binwasnaker-wlkp-annual-trend');
        // Periksa apakah variabel JS ada dan memiliki data
        if (wlkpAnnualChartDom && typeof @json($wlkpAnnualTrendLabels) !== 'undefined' && typeof @json($wlkpAnnualTrendDataValues) !== 'undefined' && @json($wlkpAnnualTrendLabels).length > 0) {
            var wlkpAnnualChart = echarts.init(wlkpAnnualChartDom);
            var wlkpAnnualOption = {
                tooltip: { trigger: 'axis', formatter: function (params) { let res = params[0].name + '<br/>'; params.forEach(function(item){ res += item.seriesName + ' : ' + item.value.toLocaleString('id-ID') + '<br/>'; }); return res; } },
                legend: { data: ['Jml Perusahaan Lapor WLKP (Tahunan)'], bottom: 5 },
                grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: true, data: @json($wlkpAnnualTrendLabels) },
                yAxis: { type: 'value', name: 'Jumlah Perusahaan', min: 0, axisLabel: { formatter: function (value) { return value.toLocaleString('id-ID'); } } },
                series: [{
                    name: 'Jml Perusahaan Lapor WLKP (Tahunan)', type: 'bar', barMaxWidth: 50,
                    data: @json($wlkpAnnualTrendDataValues),
                    itemStyle: { color: '#ef4444' } 
                }]
            };
            wlkpAnnualChart.setOption(wlkpAnnualOption);
            window.addEventListener('resize', () => wlkpAnnualChart.resize());
        }
    });
</script>
@endpush