@extends('layouts.app')

@section('title', 'Dashboard Binapenta')
@section('page_title', 'Binapenta & PKK') {{-- Sesuai HTML template --}}

@section('header_filters')
    <form method="GET" action="{{ route('binapenta.dashboard') }}" class="w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 items-end">
            <div class="flex-grow">
                <label for="year_filter_binapenta" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                <select name="year_filter" id="year_filter_binapenta" class="form-input mt-1 w-full bg-white">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow">
                <label for="month_filter_binapenta" class="text-sm text-gray-600 whitespace-nowrap">Bulan:</label>
                <select name="month_filter" id="month_filter_binapenta" class="form-input mt-1 w-full bg-white">
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
                 <a href="{{ route('binapenta.dashboard') }}" class="w-full sm:w-auto px-4 py-1.5 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
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
                <h3 class="text-sm font-medium text-gray-600">Jumlah Penempatan oleh Kemnaker</h3>
                <a href="{{ route('binapenta.jumlah-penempatan-kemnaker.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalPenempatanKemnaker ?? 0) }} <span class="text-sm font-normal">Orang</span></div>
            <div class="mt-3 h-20 chart-container" id="chart-binapenta-penempatan-summary">Penempatan Summary</div>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Jumlah Lowongan Pekerjaan Baru di Pasker</h3>
                <a href="{{ route('binapenta.jumlah-lowongan-pasker.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalLowonganPasker ?? 0) }} <span class="text-sm font-normal">Lowongan</span></div>
            <div class="mt-3 h-20 chart-container" id="chart-binapenta-lowongan-summary">Lowongan Pasker Summary</div>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Jumlah TKA yang Disetujui</h3>
                <a href="{{ route('binapenta.jumlah-tka-disetujui.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalTkaDisetujui ?? 0) }} <span class="text-sm font-normal">TKA</span></div>
            <div class="mt-3 h-20 chart-container" id="chart-binapenta-tka-disetujui-summary">TKA Disetujui Summary</div>
        </div>
    </div>
     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
         <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Jumlah TKA yang Tidak Disetujui</h3>
                {{-- <a href="#" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a> --}}
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalTkaTidakDisetujui ?? 0) }} <span class="text-sm font-normal">TKA</span></div>
            <p class="text-xs text-gray-400">(Data belum tersedia)</p>
            <div class="mt-3 h-20 chart-container" id="chart-binapenta-tka-ditolak-summary">TKA Ditolak Summary</div>
        </div>
         <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Jumlah Penempatan Disabilitas</h3>
                <a href="{{ route('binapenta.jumlah-penempatan-kemnaker.index', ['status_disabilitas_filter' => 1]) }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalPenempatanDisabilitas ?? 0) }} <span class="text-sm font-normal">Orang</span></div>
            <div class="mt-3 h-20 chart-container" id="chart-binapenta-disabilitas-summary">Penempatan Disabilitas Summary</div>
        </div>
     </div>

    {{-- Baris untuk Chart Utama --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Penempatan oleh Kemnaker per Bulan (Tahun {{ $selectedYear }})</h3>
            <div id="echart-binapenta-penempatan-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Komposisi Penempatan berdasarkan Jenis Kelamin (Tahun {{ $selectedYear }}{{ $selectedMonth ? ' - '.\Carbon\Carbon::create()->month($selectedMonth)->isoFormat('MMMM') : '' }})</h3>
            <div id="echart-binapenta-penempatan-gender" style="width: 100%; height: 300px;"></div>
        </div>
    </div>
     <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Lowongan Pasker per Bulan (Tahun {{ $selectedYear }})</h3>
            <div id="echart-binapenta-lowongan-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Top 5 Lapangan Usaha Lowongan Pasker (Tahun {{ $selectedYear }}{{ $selectedMonth ? ' - '.\Carbon\Carbon::create()->month($selectedMonth)->isoFormat('MMMM') : '' }})</h3>
            <div id="echart-binapenta-lowongan-kbli" style="width: 100%; height: 300px;"></div>
        </div>
    </div>
     <div class="bg-white p-5 rounded-lg shadow mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Jumlah TKA Disetujui per Bulan (Tahun {{ $selectedYear }})</h3>
        <div id="echart-binapenta-tka-disetujui-trend" style="width: 100%; height: 300px;"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Chart Tren Penempatan Kemnaker
        var penempatanChartDom = document.getElementById('echart-binapenta-penempatan-trend');
        if (penempatanChartDom) {
            var penempatanChart = echarts.init(penempatanChartDom);
            var penempatanOption = {
                tooltip: { trigger: 'axis' },
                legend: { data: ['Jumlah Penempatan'] },
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: false, data: @json($penempatanChartLabels) },
                yAxis: { type: 'value', name: 'Jumlah Orang', min: 0 },
                series: [{
                    name: 'Jumlah Penempatan', type: 'line', smooth: true,
                    data: @json($penempatanChartDataValues),
                    itemStyle: { color: '#3b82f6' }, 
                    areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: 'rgba(59, 130, 246, 0.5)'}, {offset: 1, color: 'rgba(59, 130, 246, 0.1)'}])}
                }]
            };
            penempatanChart.setOption(penempatanOption);
            window.addEventListener('resize', () => penempatanChart.resize());
        }

        // 2. Chart Komposisi Penempatan berdasarkan Jenis Kelamin
        var penempatanGenderChartDom = document.getElementById('echart-binapenta-penempatan-gender');
        if (penempatanGenderChartDom) {
            var penempatanGenderChart = echarts.init(penempatanGenderChartDom);
            var penempatanGenderOption = {
                tooltip: { trigger: 'item', formatter: '{a} <br/>{b}: {c} ({d}%)' },
                legend: { orient: 'vertical', left: 'left', data: @json(collect($penempatanPerJenisKelamin)->pluck('name')) },
                series: [{
                    name: 'Jenis Kelamin', type: 'pie', radius: '70%', center: ['60%', '50%'],
                    data: @json($penempatanPerJenisKelamin),
                    emphasis: { itemStyle: { shadowBlur: 10, shadowOffsetX: 0, shadowColor: 'rgba(0, 0, 0, 0.5)' } }
                }]
            };
            penempatanGenderChart.setOption(penempatanGenderOption);
            window.addEventListener('resize', () => penempatanGenderChart.resize());
        }

        // 3. Chart Tren Lowongan Pasker
        var lowonganPaskerChartDom = document.getElementById('echart-binapenta-lowongan-trend');
        if (lowonganPaskerChartDom) {
            var lowonganPaskerChart = echarts.init(lowonganPaskerChartDom);
            var lowonganPaskerOption = {
                tooltip: { trigger: 'axis' },
                legend: { data: ['Jumlah Lowongan Pasker'] },
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: false, data: @json($lowonganPaskerChartLabels) },
                yAxis: { type: 'value', name: 'Jumlah Lowongan', min: 0 },
                series: [{
                    name: 'Jumlah Lowongan Pasker', type: 'line', smooth: true,
                    data: @json($lowonganPaskerChartDataValues),
                    itemStyle: { color: '#10b981' }, // Green
                    areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: 'rgba(16, 185, 129, 0.5)'}, {offset: 1, color: 'rgba(16, 185, 129, 0.1)'}])}
                }]
            };
            lowonganPaskerChart.setOption(lowonganPaskerOption);
            window.addEventListener('resize', () => lowonganPaskerChart.resize());
        }

        // 4. Chart Top 5 KBLI Lowongan Pasker
        var lowonganKbliChartDom = document.getElementById('echart-binapenta-lowongan-kbli');
        if (lowonganKbliChartDom) {
            var lowonganKbliChart = echarts.init(lowonganKbliChartDom);
            var lowonganKbliOption = {
                tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
                legend: { data: ['Jumlah Lowongan'] },
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                xAxis: { type: 'category', data: @json(collect($lowonganPerKbli)->pluck('name')), axisLabel: { interval: 0, rotate: 30, fontSize: 10 } },
                yAxis: { type: 'value', name: 'Jumlah Lowongan', min: 0 },
                series: [{
                    name: 'Jumlah Lowongan', type: 'bar', barMaxWidth: 30,
                    data: @json(collect($lowonganPerKbli)->pluck('value')),
                    itemStyle: { color: '#f59e0b' } // Amber
                }]
            };
            lowonganKbliChart.setOption(lowonganKbliOption);
            window.addEventListener('resize', () => lowonganKbliChart.resize());
        }
        
        // 5. Chart Tren TKA Disetujui
        var tkaDisetujuiChartDom = document.getElementById('echart-binapenta-tka-disetujui-trend');
        if (tkaDisetujuiChartDom) {
            var tkaDisetujuiChart = echarts.init(tkaDisetujuiChartDom);
            var tkaDisetujuiOption = {
                tooltip: { trigger: 'axis' },
                legend: { data: ['Jumlah TKA Disetujui'] },
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: false, data: @json($tkaDisetujuiChartLabels) },
                yAxis: { type: 'value', name: 'Jumlah TKA', min: 0 },
                series: [{
                    name: 'Jumlah TKA Disetujui', type: 'line', smooth: true,
                    data: @json($tkaDisetujuiChartDataValues),
                    itemStyle: { color: '#8b5cf6' }, // Violet
                    areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: 'rgba(139, 92, 246, 0.5)'}, {offset: 1, color: 'rgba(139, 92, 246, 0.1)'}])}
                }]
            };
            tkaDisetujuiChart.setOption(tkaDisetujuiOption);
            window.addEventListener('resize', () => tkaDisetujuiChart.resize());
        }

    });
</script>
@endpush
