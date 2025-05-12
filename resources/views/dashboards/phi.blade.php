@extends('layouts.app')

@section('title', 'Dashboard PHI & Jamsosnak')
@section('page_title', 'PHI & Jaminan Sosial Tenaga Kerja')

@section('header_filters')
    <form method="GET" action="{{ route('phi.dashboard') }}" class="w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 items-end">
            <div class="flex-grow">
                <label for="year_filter_phi" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                <select name="year_filter" id="year_filter_phi" class="form-input mt-1 w-full bg-white">
                    {{-- <option value="">Semua Tahun</option> --}}
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow">
                <label for="month_filter_phi" class="text-sm text-gray-600 whitespace-nowrap">Bulan:</label>
                <select name="month_filter" id="month_filter_phi" class="form-input mt-1 w-full bg-white">
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
                 <a href="{{ route('phi.dashboard') }}" class="w-full sm:w-auto px-4 py-1.5 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
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
                <h3 class="text-sm font-medium text-gray-600">Jumlah PHK</h3>
                <a href="{{ route('phi.jumlah-phk.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalTkPhk ?? 0) }} <span class="text-sm font-normal">TK</span></div>
            <p class="text-xs text-gray-500">{{ number_format($totalPerusahaanPhk ?? 0) }} Perusahaan</p>
            <div class="mt-3 h-20 chart-container" id="chart-phi-phk-summary">PHK Summary</div>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Perselisihan Ditindaklanjuti</h3>
                <a href="{{ route('phi.perselisihan-ditindaklanjuti.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalPerselisihanDitindaklanjuti ?? 0) }} <span class="text-sm font-normal">Kasus</span></div>
            <p class="text-xs text-gray-500">dari {{ number_format($totalPerselisihan ?? 0) }} total perselisihan</p>
            <div class="mt-3 h-20 chart-container" id="chart-phi-perselisihan-summary">Perselisihan Summary</div>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Mediasi Berhasil</h3>
                <a href="{{ route('phi.mediasi-berhasil.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalMediasiBerhasil ?? 0) }} <span class="text-sm font-normal">Kasus</span></div>
            <p class="text-xs text-gray-500">dari {{ number_format($totalMediasi ?? 0) }} total mediasi</p>
            <div class="mt-3 h-20 chart-container" id="chart-phi-mediasi-summary">Mediasi Summary</div>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Perusahaan Menerapkan SUSU</h3>
                <a href="{{ route('phi.perusahaan-menerapkan-susu.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalPerusahaanSusu ?? 0) }} <span class="text-sm font-normal">Perusahaan</span></div>
            <div class="mt-3 h-20 chart-container" id="chart-phi-susu-summary">SUSU Summary</div>
        </div>
    </div>

    {{-- Baris untuk Chart Utama --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Jumlah Tenaga Kerja di PHK per Bulan (Tahun {{ $selectedYear }})</h3>
            <div id="echart-phi-phk-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Komposisi Jenis Perselisihan Ditindaklanjuti (Tahun {{ $selectedYear }}{{ $selectedMonth ? ' - '.\Carbon\Carbon::create()->month($selectedMonth)->isoFormat('MMMM') : '' }})</h3>
            <div id="echart-phi-perselisihan-jenis" style="width: 100%; height: 300px;"></div>
        </div>
    </div>
     <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Mediasi vs Mediasi Berhasil per Bulan (Tahun {{ $selectedYear }})</h3>
            <div id="echart-phi-mediasi-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Perusahaan Menerapkan SUSU per Bulan (Tahun {{ $selectedYear }})</h3>
            <div id="echart-phi-susu-trend" style="width: 100%; height: 300px;"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Chart Tren PHK
        var phkChartDom = document.getElementById('echart-phi-phk-trend');
        if (phkChartDom) {
            var phkChart = echarts.init(phkChartDom);
            var phkOption = {
                tooltip: { trigger: 'axis' },
                legend: { data: ['Jumlah TK di PHK'] },
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: false, data: @json($phkChartLabels) },
                yAxis: { type: 'value', name: 'Jumlah Tenaga Kerja', min: 0 },
                series: [{
                    name: 'Jumlah TK di PHK', type: 'line', smooth: true,
                    data: @json($phkChartDataValues),
                    itemStyle: { color: '#ef4444' }, // Red
                    areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: 'rgba(239, 68, 68, 0.5)'}, {offset: 1, color: 'rgba(239, 68, 68, 0.1)'}])}
                }]
            };
            phkChart.setOption(phkOption);
            window.addEventListener('resize', () => phkChart.resize());
        }

        // 2. Chart Komposisi Jenis Perselisihan
        var perselisihanChartDom = document.getElementById('echart-phi-perselisihan-jenis');
        if (perselisihanChartDom) {
            var perselisihanChart = echarts.init(perselisihanChartDom);
            var perselisihanOption = {
                tooltip: { trigger: 'item', formatter: '{a} <br/>{b}: {c} ({d}%)' },
                legend: { 
                    orient: 'vertical', 
                    left: 'left', 
                    data: @json(collect($perselisihanPerJenis)->pluck('name')),
                    textStyle: { fontSize: 10 }
                },
                series: [{
                    name: 'Jenis Perselisihan', type: 'pie', radius: '70%', center: ['65%', '50%'], // Disesuaikan agar legenda tidak tumpang tindih
                    data: @json($perselisihanPerJenis),
                    emphasis: { itemStyle: { shadowBlur: 10, shadowOffsetX: 0, shadowColor: 'rgba(0, 0, 0, 0.5)' } }
                }]
            };
            perselisihanChart.setOption(perselisihanOption);
            window.addEventListener('resize', () => perselisihanChart.resize());
        }

        // 3. Chart Tren Mediasi vs Berhasil
        var mediasiChartDom = document.getElementById('echart-phi-mediasi-trend');
        if (mediasiChartDom) {
            var mediasiChart = echarts.init(mediasiChartDom);
            var mediasiOption = {
                tooltip: { trigger: 'axis', axisPointer: { type: 'shadow'} },
                legend: { data: ['Total Mediasi', 'Mediasi Berhasil'] },
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                xAxis: { type: 'category', data: @json($mediasiChartLabels) },
                yAxis: { type: 'value', name: 'Jumlah Kasus', min: 0 },
                series: [
                    {
                        name: 'Total Mediasi', type: 'bar', barMaxWidth: 20,
                        data: @json($mediasiTotalData),
                        itemStyle: { color: '#f59e0b' } // Amber
                    },
                    {
                        name: 'Mediasi Berhasil', type: 'bar', barMaxWidth: 20,
                        data: @json($mediasiBerhasilData),
                        itemStyle: { color: '#10b981' } // Green
                    }
                ]
            };
            mediasiChart.setOption(mediasiOption);
            window.addEventListener('resize', () => mediasiChart.resize());
        }
        
        // 4. Chart Tren Perusahaan Menerapkan SUSU
        var susuChartDom = document.getElementById('echart-phi-susu-trend');
        if (susuChartDom) {
            var susuChart = echarts.init(susuChartDom);
            var susuOption = {
                tooltip: { trigger: 'axis' },
                legend: { data: ['Jumlah Perusahaan SUSU'] },
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: false, data: @json($susuChartLabels) },
                yAxis: { type: 'value', name: 'Jumlah Perusahaan', min: 0 },
                series: [{
                    name: 'Jumlah Perusahaan SUSU', type: 'line', smooth: true,
                    data: @json($susuChartDataValues),
                    itemStyle: { color: '#8b5cf6' }, // Violet
                    areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: 'rgba(139, 92, 246, 0.5)'}, {offset: 1, color: 'rgba(139, 92, 246, 0.1)'}])}
                }]
            };
            susuChart.setOption(susuOption);
            window.addEventListener('resize', () => susuChart.resize());
        }

    });
</script>
@endpush
