@extends('layouts.app')

@section('title', 'Dashboard Barenbang')
@section('page_title', 'Barenbang')

@section('header_filters')
    <form method="GET" action="{{ route('barenbang.dashboard') }}" class="w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 items-end">
            <div class="flex-grow">
                <label for="year_filter_barenbang" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                <select name="year_filter" id="year_filter_barenbang" class="form-input mt-1 w-full bg-white">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow">
                <label for="month_filter_barenbang" class="text-sm text-gray-600 whitespace-nowrap">Bulan:</label>
                <select name="month_filter" id="month_filter_barenbang" class="form-input mt-1 w-full bg-white">
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
                 <a href="{{ route('barenbang.dashboard') }}" class="w-full sm:w-auto px-4 py-1.5 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
                    Reset
                </a>
            </div>
        </div>
    </form>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Baris 1: Kartu Kajian & Rekomendasi --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Jumlah Kajian</h3>
                <a href="{{ route('barenbang.jumlah-kajian-rekomendasi.index', ['jenis_output_filter' => 1]) }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalKajian ?? 0) }}</div>
            {{-- <div class="mt-3 h-32 chart-container" id="echart-barenbang-kajian-summary">
                 <p class="text-xs text-gray-400">Ringkasan Kajian</p>
            </div> --}}
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Jumlah Rekomendasi</h3>
                <a href="{{ route('barenbang.jumlah-kajian-rekomendasi.index', ['jenis_output_filter' => 2]) }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalRekomendasi ?? 0) }}</div>
            {{-- <div class="mt-3 h-32 chart-container" id="echart-barenbang-rekomendasi-summary">
                 <p class="text-xs text-gray-400">Ringkasan Rekomendasi</p>
            </div> --}}
        </div>
    </div>

    {{-- Baris 2: Kartu Data Ketenagakerjaan (KPI Utama) --}}
    <div class="bg-white p-5 rounded-lg shadow">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-medium text-gray-800">Indikator Ketenagakerjaan Utama ({{ $selectedMonth ? \Carbon\Carbon::create()->month($selectedMonth)->isoFormat('MMMM') : 'Tahun' }} {{ $selectedYear }})</h3>
            <a href="{{ route('barenbang.data-ketenagakerjaan.index') }}" class="text-xs text-primary hover:text-primary/80">Lihat Detail &rarr;</a>
        </div>
        @if($latestKetenagakerjaan)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div>
                <p class="text-xs text-gray-500">Penduduk 15+</p>
                <p class="text-xl font-semibold">{{ number_format($latestKetenagakerjaan->penduduk_15_tahun_ke_atas ?? 0, 1, ',', '.') }} <span class="text-xs">Ribu</span></p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Angkatan Kerja</p>
                <p class="text-xl font-semibold">{{ number_format($latestKetenagakerjaan->angkatan_kerja ?? 0, 1, ',', '.') }} <span class="text-xs">Ribu</span></p>
            </div>
             <div>
                <p class="text-xs text-gray-500">Bukan Angkatan Kerja</p>
                <p class="text-xl font-semibold">{{ number_format($latestKetenagakerjaan->bukan_angkatan_kerja ?? 0, 1, ',', '.') }} <span class="text-xs">Ribu</span></p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Bekerja</p>
                <p class="text-xl font-semibold">{{ number_format($latestKetenagakerjaan->bekerja ?? 0, 1, ',', '.') }} <span class="text-xs">Ribu</span></p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Pengangguran Terbuka</p>
                <p class="text-xl font-semibold">{{ number_format($latestKetenagakerjaan->pengangguran_terbuka ?? 0, 1, ',', '.') }} <span class="text-xs">Ribu</span></p>
            </div>
            <div>
                <p class="text-xs text-gray-500">TPAK</p>
                <p class="text-xl font-semibold">{{ number_format($latestKetenagakerjaan->tingkat_partisipasi_angkatan_kerja ?? 0, 2) }}%</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">TPT</p>
                <p class="text-xl font-semibold">{{ number_format($latestKetenagakerjaan->tingkat_pengangguran_terbuka ?? 0, 2) }}%</p>
            </div>
             <div>
                <p class="text-xs text-gray-500">TKK</p>
                <p class="text-xl font-semibold">{{ number_format($latestKetenagakerjaan->tingkat_kesempatan_kerja ?? 0, 2) }}%</p>
            </div>
        </div>
        @else
        <p class="text-gray-500">Data ketenagakerjaan untuk periode ini tidak tersedia.</p>
        @endif
    </div>
    
    {{-- Baris 3: Kartu Aplikasi Terintegrasi --}}
    <div class="bg-white p-5 rounded-lg shadow">
        <div class="flex items-center justify-between mb-1">
            <h3 class="text-sm font-medium text-gray-600">Aplikasi Terintegrasi SiapKerja</h3>
            <a href="{{ route('barenbang.aplikasi-integrasi-siapkerja.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
        </div>
        <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalAplikasiTerintegrasi ?? 0) }} <span class="text-sm font-normal">Aplikasi</span></div>
        <div class="mt-3 h-48" id="echart-barenbang-aplikasi-jenis-instansi"></div>
    </div>


    {{-- Baris untuk Chart Detail --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Kajian vs Rekomendasi per Bulan (Tahun {{ $selectedYear }})</h3>
            <div id="echart-barenbang-kajian-rekomendasi-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren TPAK vs TPT per Bulan (Tahun {{ $selectedYear }})</h3>
            <div id="echart-barenbang-tpak-tpt-trend" style="width: 100%; height: 300px;"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- ECharts sudah di-include di layouts/app.blade.php --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Chart Tren Kajian vs Rekomendasi
        var kajianRekChartDom = document.getElementById('echart-barenbang-kajian-rekomendasi-trend');
        if (kajianRekChartDom) {
            var kajianRekChart = echarts.init(kajianRekChartDom);
            var kajianRekOption = {
                tooltip: { trigger: 'axis', axisPointer: { type: 'shadow'} },
                legend: { data: ['Jumlah Kajian', 'Jumlah Rekomendasi'] },
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                xAxis: { type: 'category', data: @json($chartLabelsBulan) },
                yAxis: { type: 'value', name: 'Jumlah', min: 0 },
                series: [
                    {
                        name: 'Jumlah Kajian', type: 'bar', barMaxWidth: 20, stack: 'Total',
                        data: @json($kajianChartData),
                        itemStyle: { color: '#3b82f6' } 
                    },
                    {
                        name: 'Jumlah Rekomendasi', type: 'bar', barMaxWidth: 20, stack: 'Total',
                        data: @json($rekomendasiChartData),
                        itemStyle: { color: '#10b981' } 
                    }
                ]
            };
            kajianRekChart.setOption(kajianRekOption);
            window.addEventListener('resize', () => kajianRekChart.resize());
        }

        // 2. Chart Tren TPAK vs TPT
        var tpakTptChartDom = document.getElementById('echart-barenbang-tpak-tpt-trend');
        if (tpakTptChartDom) {
            var tpakTptChart = echarts.init(tpakTptChartDom);
            var tpakTptOption = {
                tooltip: { trigger: 'axis', formatter: function (params) {
                    let tooltipHtml = params[0].name + '<br/>';
                    params.forEach(function (item) {
                        tooltipHtml += item.marker + item.seriesName + ': ' + (item.value !== null ? item.value.toFixed(2) + '%' : '-') + '<br/>';
                    });
                    return tooltipHtml;
                }},
                legend: { data: ['TPAK (%)', 'TPT (%)'] },
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: false, data: @json($ketenagakerjaanBulanLabels) },
                yAxis: { type: 'value', name: 'Persentase (%)', min: 0, max: 100, axisLabel: {formatter: '{value}%'} },
                series: [
                    {
                        name: 'TPAK (%)', type: 'line', smooth: true,
                        data: @json($tpakChartData),
                        itemStyle: { color: '#f59e0b' },
                        connectNulls: true 
                    },
                    {
                        name: 'TPT (%)', type: 'line', smooth: true,
                        data: @json($tptChartData),
                        itemStyle: { color: '#ef4444' },
                        connectNulls: true 
                    }
                ]
            };
            tpakTptChart.setOption(tpakTptOption);
            window.addEventListener('resize', () => tpakTptChart.resize());
        }
        
        // 3. Chart Komposisi Aplikasi Terintegrasi per Jenis Instansi
        var aplikasiJenisChartDom = document.getElementById('echart-barenbang-aplikasi-jenis-instansi');
        if (aplikasiJenisChartDom) {
            var aplikasiJenisChart = echarts.init(aplikasiJenisChartDom);
            var aplikasiJenisOption = {
                tooltip: { trigger: 'item', formatter: '{a} <br/>{b}: {c} ({d}%)' },
                legend: { 
                    orient: 'horizontal', 
                    bottom: 0, 
                    data: @json(collect($aplikasiPerJenisInstansi)->pluck('name')),
                    textStyle: { fontSize: 10 }
                },
                series: [{
                    name: 'Jenis Instansi', type: 'pie', radius: ['40%', '65%'], center: ['50%', '45%'],
                    avoidLabelOverlap: false,
                    itemStyle: { borderRadius: 5, borderColor: '#fff', borderWidth: 1 },
                    label: { show: true, formatter: '{b}\n{c} ({d}%)', fontSize: 10 },
                    emphasis: { label: { show: true, fontSize: '12', fontWeight: 'bold' } },
                    labelLine: { show: true, length: 5, length2: 5 },
                    data: @json($aplikasiPerJenisInstansi)
                }]
            };
            aplikasiJenisChart.setOption(aplikasiJenisOption);
            window.addEventListener('resize', () => aplikasiJenisChart.resize());
        }

    });
</script>
@endpush
