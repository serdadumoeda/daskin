@extends('layouts.app')

@section('title', 'Dashboard Binwasnaker & K3')
@section('page_title', 'Binwasnaker & K3')

@section('header_filters')
    <form method="GET" action="{{ route('binwasnaker.dashboard') }}" class="w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 items-end">
            <div class="flex-grow">
                <label for="year_filter_binwasnaker" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                <select name="year_filter" id="year_filter_binwasnaker" class="form-input mt-1 w-full bg-white">
                    {{-- <option value="">Semua Tahun</option> --}}
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
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
    {{-- Baris 1: Kartu Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Laporan WLKP Online</h3>
                <a href="{{ route('binwasnaker.pelaporan-wlkp-online.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalWlkpReported ?? 0) }} <span class="text-sm">Perusahaan</span></div>
            <div class="mt-3 h-20 chart-container" id="chart-binwasnaker-wlkp-summary">WLKP Summary</div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Pengaduan Norma (TL)</h3>
                <a href="{{ route('binwasnaker.pengaduan-pelanggaran-norma.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalPengaduanNorma ?? 0) }} <span class="text-sm">Kasus</span></div>
            <div class="mt-3 h-20 chart-container" id="chart-binwasnaker-pengaduan-summary">Pengaduan Summary</div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Penerapan SMK3</h3>
                <a href="{{ route('binwasnaker.penerapan-smk3.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalPenerapanSmk3 ?? 0) }} <span class="text-sm">Perusahaan</span></div>
            <div class="mt-3 h-20 chart-container" id="chart-binwasnaker-smk3-summary">SMK3 Summary</div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Self Assessment Norma 100</h3>
                <a href="{{ route('binwasnaker.self-assessment-norma100.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalSelfAssessment ?? 0) }} <span class="text-sm">Perusahaan</span></div>
            <div class="mt-3 h-20 chart-container" id="chart-binwasnaker-sa-summary">SA Norma 100 Summary</div>
        </div>
    </div>

    {{-- Baris untuk Chart Utama --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Pelaporan WLKP Online per Bulan (Tahun {{ $selectedYear }})</h3>
            <div id="echart-binwasnaker-wlkp-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Komposisi Pengaduan Pelanggaran Norma (Tahun {{ $selectedYear }}{{ $selectedMonth ? ' - '.\Carbon\Carbon::create()->month($selectedMonth)->isoFormat('MMMM') : '' }})</h3>
            <div id="echart-binwasnaker-pengaduan-jenis" style="width: 100%; height: 300px;"></div>
        </div>
    </div>
     <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Penerapan SMK3 per Kategori Penilaian (Tahun {{ $selectedYear }}{{ $selectedMonth ? ' - '.\Carbon\Carbon::create()->month($selectedMonth)->isoFormat('MMMM') : '' }})</h3>
            <div id="echart-binwasnaker-smk3-kategori" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Distribusi Hasil Self Assessment Norma 100 (Tahun {{ $selectedYear }}{{ $selectedMonth ? ' - '.\Carbon\Carbon::create()->month($selectedMonth)->isoFormat('MMMM') : '' }})</h3>
            <div id="echart-binwasnaker-sa-hasil" style="width: 100%; height: 300px;"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Chart Tren WLKP Online
        var wlkpChartDom = document.getElementById('echart-binwasnaker-wlkp-trend');
        if (wlkpChartDom) {
            var wlkpChart = echarts.init(wlkpChartDom);
            var wlkpOption = {
                tooltip: { trigger: 'axis' },
                legend: { data: ['Jumlah Perusahaan Lapor WLKP'] },
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: false, data: @json($wlkpChartLabels) },
                yAxis: { type: 'value', name: 'Jumlah Perusahaan', min: 0 },
                series: [{
                    name: 'Jumlah Perusahaan Lapor WLKP', type: 'line', smooth: true,
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
                tooltip: { trigger: 'item', formatter: '{a} <br/>{b}: {c} ({d}%)' },
                legend: { orient: 'vertical', left: 'left', data: @json(collect($pengaduanChartData)->pluck('name')) },
                series: [{
                    name: 'Jenis Pelanggaran', type: 'pie', radius: '70%', center: ['60%', '50%'], // Center disesuaikan
                    data: @json($pengaduanChartData),
                    emphasis: { itemStyle: { shadowBlur: 10, shadowOffsetX: 0, shadowColor: 'rgba(0, 0, 0, 0.5)' } }
                }]
            };
            pengaduanChart.setOption(pengaduanOption);
            window.addEventListener('resize', () => pengaduanChart.resize());
        }

        // 3. Chart Penerapan SMK3 per Kategori
        var smk3ChartDom = document.getElementById('echart-binwasnaker-smk3-kategori');
        if (smk3ChartDom) {
            var smk3Chart = echarts.init(smk3ChartDom);
            var smk3Option = {
                tooltip: { trigger: 'axis', axisPointer: { type: 'shadow'} },
                legend: { data: ['Jumlah Perusahaan'] },
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                xAxis: { type: 'category', data: @json($smk3ChartLabels), axisLabel: { interval: 0, rotate: 30 } },
                yAxis: { type: 'value', name: 'Jumlah Perusahaan', min: 0 },
                series: [{
                    name: 'Jumlah Perusahaan', type: 'bar', barMaxWidth: 40,
                    data: @json($smk3ChartDataValues),
                    itemStyle: { color: '#10b981' } // Green
                }]
            };
            smk3Chart.setOption(smk3Option);
            window.addEventListener('resize', () => smk3Chart.resize());
        }
        
        // 4. Chart Distribusi Hasil Self Assessment Norma 100
        var saChartDom = document.getElementById('echart-binwasnaker-sa-hasil');
        if (saChartDom) {
            var saChart = echarts.init(saChartDom);
            var saOption = {
                tooltip: { trigger: 'item', formatter: '{a} <br/>{b}: {c} ({d}%)' },
                legend: { orient: 'vertical', left: 10, data: @json(collect($assessmentResults)->pluck('name')) },
                series: [{
                    name: 'Hasil Assessment', type: 'pie', radius: ['50%', '70%'], avoidLabelOverlap: false,
                    label: { show: false, position: 'center' },
                    emphasis: { label: { show: true, fontSize: '20', fontWeight: 'bold' } },
                    labelLine: { show: false },
                    data: @json($assessmentResults)
                }]
            };
            saChart.setOption(saOption);
            window.addEventListener('resize', () => saChart.resize());
        }

    });
</script>
@endpush
