@extends('layouts.app')

@section('title', 'Dashboard Binalavotas')
@section('page_title', 'Binalavotas')

@section('header_filters')
    <form method="GET" action="{{ route('binalavotas.dashboard') }}" class="w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 items-end">
            <div class="flex-grow">
                <label for="year_filter_binalavotas" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                <select name="year_filter" id="year_filter_binalavotas" class="form-input mt-1 w-full bg-white">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow">
                <label for="month_filter_binalavotas" class="text-sm text-gray-600 whitespace-nowrap">Bulan:</label>
                <select name="month_filter" id="month_filter_binalavotas" class="form-input mt-1 w-full bg-white">
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
                 <a href="{{ route('binalavotas.dashboard') }}" class="w-full sm:w-auto px-4 py-1.5 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
                    Reset
                </a>
            </div>
        </div>
    </form>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Baris 1: Kartu Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Lulus Pelatihan Internal</h3>
                <a href="{{ route('binalavotas.jumlah-kepesertaan-pelatihan.index', ['penyelenggara_filter' => 1, 'status_kelulusan_filter' => 1]) }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalLulusInternal ?? 0) }} <span class="text-sm font-normal">Peserta</span></div>
            <div class="mt-3 h-32 chart-container" id="echart-binalavotas-lulus-internal"></div>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Lulus Pelatihan Eksternal</h3>
                <a href="{{ route('binalavotas.jumlah-kepesertaan-pelatihan.index', ['penyelenggara_filter' => 2, 'status_kelulusan_filter' => 1]) }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalLulusEksternal ?? 0) }} <span class="text-sm font-normal">Peserta</span></div>
             <div class="mt-3 h-32 chart-container" id="echart-binalavotas-lulus-eksternal"></div>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Sertifikasi Kompetensi</h3>
                <a href="{{ route('binalavotas.jumlah-sertifikasi-kompetensi.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalSertifikasi ?? 0) }} <span class="text-sm font-normal">Sertifikat</span></div>
            <div class="mt-3 h-32 chart-container" id="echart-binalavotas-sertifikasi"></div>
        </div>
    </div>

    {{-- Baris untuk Chart Utama --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Komposisi Tipe Lembaga Pelatihan (Lulus - Tahun {{ $selectedYear }}{{ $selectedMonth ? ' - '.\Carbon\Carbon::create()->month($selectedMonth)->isoFormat('MMMM') : '' }})</h3>
            <div id="echart-binalavotas-tipe-lembaga" style="width: 100%; height: 350px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Komposisi Jenis LSP Sertifikasi (Tahun {{ $selectedYear }}{{ $selectedMonth ? ' - '.\Carbon\Carbon::create()->month($selectedMonth)->isoFormat('MMMM') : '' }})</h3>
            <div id="echart-binalavotas-jenis-lsp" style="width: 100%; height: 350px;"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- ECharts sudah di-include di layouts/app.blade.php --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Chart Tren Lulus Pelatihan Internal
        var lulusInternalChartDom = document.getElementById('echart-binalavotas-lulus-internal');
        if (lulusInternalChartDom) {
            var lulusInternalChart = echarts.init(lulusInternalChartDom);
            var lulusInternalOption = {
                tooltip: { trigger: 'axis' },
                grid: { left: '3%', right: '10%', bottom: '3%', top: '15%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: false, data: @json($chartLabels) },
                yAxis: { type: 'value', min: 0 },
                series: [{
                    name: 'Lulus Internal', type: 'line', smooth: true,
                    data: @json($lulusInternalChartData),
                    itemStyle: { color: '#3b82f6' }, 
                    areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: 'rgba(59, 130, 246, 0.3)'}, {offset: 1, color: 'rgba(59, 130, 246, 0)'}])}
                }]
            };
            lulusInternalChart.setOption(lulusInternalOption);
            window.addEventListener('resize', () => lulusInternalChart.resize());
        }

        // 2. Chart Tren Lulus Pelatihan Eksternal
        var lulusEksternalChartDom = document.getElementById('echart-binalavotas-lulus-eksternal');
        if (lulusEksternalChartDom) {
            var lulusEksternalChart = echarts.init(lulusEksternalChartDom);
            var lulusEksternalOption = {
                tooltip: { trigger: 'axis' },
                grid: { left: '3%', right: '10%', bottom: '3%', top: '15%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: false, data: @json($chartLabels) },
                yAxis: { type: 'value', min: 0 },
                series: [{
                    name: 'Lulus Eksternal', type: 'line', smooth: true,
                    data: @json($lulusEksternalChartData),
                    itemStyle: { color: '#10b981' }, 
                    areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: 'rgba(16, 185, 129, 0.3)'}, {offset: 1, color: 'rgba(16, 185, 129, 0)'}])}
                }]
            };
            lulusEksternalChart.setOption(lulusEksternalOption);
            window.addEventListener('resize', () => lulusEksternalChart.resize());
        }
        
        // 3. Chart Tren Sertifikasi Kompetensi
        var sertifikasiChartDom = document.getElementById('echart-binalavotas-sertifikasi');
        if (sertifikasiChartDom) {
            var sertifikasiChart = echarts.init(sertifikasiChartDom);
            var sertifikasiOption = {
                tooltip: { trigger: 'axis' },
                grid: { left: '3%', right: '10%', bottom: '3%', top: '15%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: false, data: @json($chartLabels) },
                yAxis: { type: 'value', min: 0 },
                series: [{
                    name: 'Jumlah Sertifikasi', type: 'line', smooth: true,
                    data: @json($sertifikasiChartData),
                    itemStyle: { color: '#f59e0b' }, 
                    areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: 'rgba(245, 158, 11, 0.3)'}, {offset: 1, color: 'rgba(245, 158, 11, 0)'}])}
                }]
            };
            sertifikasiChart.setOption(sertifikasiOption);
            window.addEventListener('resize', () => sertifikasiChart.resize());
        }

        // 4. Chart Komposisi Tipe Lembaga Pelatihan (Lulus)
        var tipeLembagaChartDom = document.getElementById('echart-binalavotas-tipe-lembaga');
        if (tipeLembagaChartDom) {
            var tipeLembagaChart = echarts.init(tipeLembagaChartDom);
            var tipeLembagaOption = {
                tooltip: { trigger: 'item', formatter: '{a} <br/>{b}: {c} ({d}%)' },
                legend: { 
                    orient: 'horizontal', 
                    bottom: 0, 
                    data: @json(collect($pelatihanPerTipeLembaga)->pluck('name')),
                    textStyle: { fontSize: 10 }
                },
                series: [{
                    name: 'Tipe Lembaga (Lulus)', type: 'pie', radius: ['45%', '70%'], center: ['50%', '45%'],
                    avoidLabelOverlap: false,
                    label: { show: false, position: 'center' },
                    emphasis: { label: { show: true, fontSize: '16', fontWeight: 'bold' } },
                    labelLine: { show: false },
                    data: @json($pelatihanPerTipeLembaga)
                }]
            };
            tipeLembagaChart.setOption(tipeLembagaOption);
            window.addEventListener('resize', () => tipeLembagaChart.resize());
        }
        
        // 5. Chart Komposisi Jenis LSP Sertifikasi
        var jenisLspChartDom = document.getElementById('echart-binalavotas-jenis-lsp');
        if (jenisLspChartDom) {
            var jenisLspChart = echarts.init(jenisLspChartDom);
            var jenisLspOption = {
                tooltip: { trigger: 'item', formatter: '{a} <br/>{b}: {c} ({d}%)' },
                legend: { 
                    orient: 'horizontal', 
                    bottom: 0, 
                    data: @json(collect($sertifikasiPerJenisLsp)->pluck('name')),
                    textStyle: { fontSize: 10 }
                },
                series: [{
                    name: 'Jenis LSP', type: 'pie', radius: ['45%', '70%'], center: ['50%', '45%'],
                    avoidLabelOverlap: false,
                    label: { show: false, position: 'center' },
                    emphasis: { label: { show: true, fontSize: '16', fontWeight: 'bold' } },
                    labelLine: { show: false },
                    data: @json($sertifikasiPerJenisLsp)
                }]
            };
            jenisLspChart.setOption(jenisLspOption);
            window.addEventListener('resize', () => jenisLspChart.resize());
        }

    });
</script>
@endpush
