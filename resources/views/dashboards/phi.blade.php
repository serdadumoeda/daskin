@extends('layouts.app')

@section('title', 'Dashboard PHI & Jamsosnak')
@section('page_title', 'PHI & Jaminan Sosial Tenaga Kerja')

@section('header_filters')
    <form method="GET" action="{{ route('phi.dashboard') }}" class="w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 items-end">
            <div class="flex-grow">
                <label for="year_filter_phi" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                <select name="year_filter" id="year_filter_phi" class="form-input mt-1 w-full bg-white">
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
    @php
        $yearToDisplayPhi = $selectedYear ?: date('Y');
        $monthValuePhi = null; 
        if ($selectedMonth && is_numeric($selectedMonth)) {
            $monthValuePhi = (int)$selectedMonth;
        }

        if ($monthValuePhi && $monthValuePhi >= 1 && $monthValuePhi <= 12) {
            $endMonthNamePhi = \Carbon\Carbon::create()->month($monthValuePhi)->isoFormat('MMMM');
            $periodTextPhi = "Periode: Januari - " . $endMonthNamePhi . " " . $yearToDisplayPhi;
        } else {
            $periodTextPhi = "Sepanjang Tahun " . $yearToDisplayPhi;
        }
    @endphp

    {{-- Baris 1: Kartu Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                {{-- Judul Disesuaikan --}}
                <h3 class="text-sm font-medium text-gray-600">Jumlah PHK</h3>
                <a href="{{ route('phi.jumlah-phk.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalTkPhk ?? 0) }} <span class="text-sm font-normal">TK</span></div>
            <p class="text-xs text-gray-500">{{ number_format($totalPerusahaanPhk ?? 0) }} Perusahaan</p>
            {{-- Periode Ditambahkan --}}
            <p class="text-xs text-gray-400 mt-1">{{ $periodTextPhi }}</p>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                {{-- Judul Disesuaikan --}}
                <h3 class="text-sm font-medium text-gray-600">Jumlah perselisihan yang ditindaklanjuti</h3>
                <a href="{{ route('phi.perselisihan-ditindaklanjuti.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalPerselisihanDitindaklanjuti ?? 0) }} <span class="text-sm font-normal">Kasus</span></div>
            {{-- <p class="text-xs text-gray-500">dari {{ number_format($totalPerselisihan ?? 0) }} total perselisihan</p> --}}
            {{-- Periode Ditambahkan --}}
            <p class="text-xs text-gray-400 mt-1">{{ $periodTextPhi }}</p>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                {{-- Judul Disesuaikan --}}
                <h3 class="text-sm font-medium text-gray-600">Jumlah mediasi yang berhasil</h3>
                <a href="{{ route('phi.mediasi-berhasil.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalMediasiBerhasil ?? 0) }} <span class="text-sm font-normal">Kasus</span></div>
            {{-- <p class="text-xs text-gray-500">dari {{ number_format($totalMediasi ?? 0) }} total mediasi</p> --}}
            {{-- Periode Ditambahkan --}}
            <p class="text-xs text-gray-400 mt-1">{{ $periodTextPhi }}</p>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                {{-- Judul Disesuaikan --}}
                <h3 class="text-sm font-medium text-gray-600">Jumlah Perusahaan yang menerapkan SUSU</h3>
                <a href="{{ route('phi.perusahaan-menerapkan-susu.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalPerusahaanSusu ?? 0) }} <span class="text-sm font-normal">Perusahaan</span></div>
            {{-- Periode Ditambahkan --}}
            <p class="text-xs text-gray-400 mt-1">{{ $periodTextPhi }}</p>
        </div>
    </div>

    {{-- Baris untuk Chart Utama --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Jumlah Tenaga Kerja di PHK per Bulan (Tahun {{ $selectedYear }})</h3>
            <div id="echart-phi-phk-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            {{-- Judul Chart dan ID Diubah --}}
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Jumlah Perselisihan Ditindaklanjuti per Bulan (Tahun {{ $selectedYear }})</h3>
            <div id="echart-phi-perselisihan-tl-trend" style="width: 100%; height: 300px;"></div>
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
                tooltip: { trigger: 'axis', formatter: function (params) { let res = params[0].name + '<br/>'; params.forEach(function(item){ res += item.seriesName + ' : ' + item.value.toLocaleString('id-ID') + '<br/>'; }); return res; } },
                legend: { data: ['Jumlah TK di PHK'], bottom: 5 },
                grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: false, data: @json($phkChartLabels) },
                yAxis: { type: 'value', name: 'Jumlah Tenaga Kerja', min: 0, axisLabel: { formatter: function (value) { return value.toLocaleString('id-ID'); } } },
                series: [{
                    name: 'Jumlah TK di PHK', type: 'line', smooth: true,
                    data: @json($phkChartDataValues),
                    itemStyle: { color: '#ef4444' }, 
                    areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: 'rgba(239, 68, 68, 0.5)'}, {offset: 1, color: 'rgba(239, 68, 68, 0.1)'}])}
                }]
            };
            phkChart.setOption(phkOption);
            window.addEventListener('resize', () => phkChart.resize());
        }

        // 2. Chart Tren Jumlah Perselisihan Ditindaklanjuti (BARU - Line Chart)
        var perselisihanTlChartDom = document.getElementById('echart-phi-perselisihan-tl-trend'); // ID Baru
        if (perselisihanTlChartDom) {
            var perselisihanTlChart = echarts.init(perselisihanTlChartDom);
            var perselisihanTlOption = {
                tooltip: { trigger: 'axis', formatter: function (params) { let res = params[0].name + '<br/>'; params.forEach(function(item){ res += item.seriesName + ' : ' + item.value.toLocaleString('id-ID') + '<br/>'; }); return res; } },
                legend: { data: ['Jml Perselisihan Ditindaklanjuti'], bottom: 5 },
                grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: false, data: @json($perselisihanTlChartLabels) }, // Data dari controller
                yAxis: { type: 'value', name: 'Jumlah Kasus', min: 0, axisLabel: { formatter: function (value) { return value.toLocaleString('id-ID'); } } },
                series: [{
                    name: 'Jml Perselisihan Ditindaklanjuti', type: 'line', smooth: true,
                    data: @json($perselisihanTlChartDataValues), // Data dari controller
                    itemStyle: { color: '#f97316' }, // Orange
                    areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: 'rgba(249, 115, 22, 0.5)'}, {offset: 1, color: 'rgba(249, 115, 22, 0.1)'}])}
                }]
            };
            perselisihanTlChart.setOption(perselisihanTlOption);
            window.addEventListener('resize', () => perselisihanTlChart.resize());
        }

        // 3. Chart Tren Mediasi vs Berhasil
        var mediasiChartDom = document.getElementById('echart-phi-mediasi-trend');
        if (mediasiChartDom) {
            var mediasiChart = echarts.init(mediasiChartDom);
            var mediasiOption = {
                tooltip: { trigger: 'axis', axisPointer: { type: 'shadow'}, formatter: function (params) { let res = params[0].name + '<br/>'; params.forEach(function(item){ res += item.seriesName + ' : ' + item.value.toLocaleString('id-ID') + '<br/>'; }); return res; } },
                legend: { data: ['Total Mediasi', 'Mediasi Berhasil'], top: 5 },
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                xAxis: { type: 'category', data: @json($mediasiChartLabels) },
                yAxis: { type: 'value', name: 'Jumlah Kasus', min: 0, axisLabel: { formatter: function (value) { return value.toLocaleString('id-ID'); } } },
                series: [
                    {
                        name: 'Total Mediasi', type: 'bar', barMaxWidth: 20,
                        data: @json($mediasiTotalData),
                        itemStyle: { color: '#f59e0b' } 
                    },
                    {
                        name: 'Mediasi Berhasil', type: 'bar', barMaxWidth: 20,
                        data: @json($mediasiBerhasilData),
                        itemStyle: { color: '#10b981' } 
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
                tooltip: { trigger: 'axis', formatter: function (params) { let res = params[0].name + '<br/>'; params.forEach(function(item){ res += item.seriesName + ' : ' + item.value.toLocaleString('id-ID') + '<br/>'; }); return res; } },
                legend: { data: ['Jumlah Perusahaan SUSU'], bottom: 5 },
                grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: false, data: @json($susuChartLabels) },
                yAxis: { type: 'value', name: 'Jumlah Perusahaan', min: 0, axisLabel: { formatter: function (value) { return value.toLocaleString('id-ID'); } } },
                series: [{
                    name: 'Jumlah Perusahaan SUSU', type: 'line', smooth: true,
                    data: @json($susuChartDataValues),
                    itemStyle: { color: '#8b5cf6' }, 
                    areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: 'rgba(139, 92, 246, 0.5)'}, {offset: 1, color: 'rgba(139, 92, 246, 0.1)'}])}
                }]
            };
            susuChart.setOption(susuOption);
            window.addEventListener('resize', () => susuChart.resize());
        }

    });
</script>
@endpush