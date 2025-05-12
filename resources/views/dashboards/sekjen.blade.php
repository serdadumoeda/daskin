@extends('layouts.app')

@section('title', 'Dashboard Sekretariat Jenderal')
@section('page_title', 'Sekretariat Jenderal')

@section('header_filters')
    <form method="GET" action="{{ route('sekretariat-jenderal.dashboard') }}" class="w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 items-end">
            <div class="flex-grow">
                <label for="year_filter_sekjen" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                <select name="year_filter" id="year_filter_sekjen" class="form-input mt-1 w-full bg-white">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow">
                <label for="month_filter_sekjen" class="text-sm text-gray-600 whitespace-nowrap">Bulan:</label>
                <select name="month_filter" id="month_filter_sekjen" class="form-input mt-1 w-full bg-white">
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
                 <a href="{{ route('sekretariat-jenderal.dashboard') }}" class="w-full sm:w-auto px-4 py-1.5 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
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
                <h3 class="text-sm font-medium text-gray-600">Jumlah MoU</h3>
                <a href="{{ route('sekretariat-jenderal.progress-mou.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalMoU ?? 0) }}</div>
            <div class="mt-3 h-20 chart-container" id="chart-sekjen-mou-summary">MoU Summary</div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Jumlah Regulasi Baru</h3>
                <a href="{{ route('sekretariat-jenderal.jumlah-regulasi-baru.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalRegulasi ?? 0) }}</div>
            <div class="mt-3 h-20 chart-container" id="chart-sekjen-regulasi-summary">Regulasi Summary</div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Jumlah Penanganan Kasus</h3>
                <a href="{{ route('sekretariat-jenderal.jumlah-penanganan-kasus.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalKasusDitangani ?? 0) }}</div>
            <div class="mt-3 h-20 chart-container" id="chart-sekjen-kasus-summary">Kasus Summary</div>
        </div>
    </div>

    {{-- Baris 2: Kartu Ringkasan Lanjutan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Total Nilai Penyelesaian BMN</h3>
                <a href="{{ route('sekretariat-jenderal.penyelesaian-bmn.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">Rp {{ number_format($totalNilaiPenyelesaianBmn ?? 0, 0, ',', '.') }}</div>
            <div class="mt-3 h-20 chart-container" id="chart-sekjen-bmn-summary">BMN Summary</div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Total SDM WFO</h3>
                <a href="{{ route('sekretariat-jenderal.persentase-kehadiran.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalWFO ?? 0) }} <span class="text-sm">Orang</span></div>
            <div class="mt-3 h-20 chart-container" id="chart-sekjen-kehadiran-summary">Kehadiran Summary</div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Total Berita Monev Media</h3>
                <a href="{{ route('sekretariat-jenderal.monev-monitoring-media.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalBeritaMonev ?? 0) }}</div>
            <div class="mt-3 h-20 chart-container" id="chart-sekjen-monev-summary">Monev Media Summary</div>
        </div>
    </div>
     <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">Lulusan Polteknaker Bekerja</h3>
                <a href="{{ route('sekretariat-jenderal.lulusan-polteknaker-bekerja.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalLulusanBekerja ?? 0) }} <span class="text-sm">Orang</span></div>
            <div class="mt-3 h-20 chart-container" id="chart-sekjen-lulusan-summary">Lulusan Summary</div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-medium text-gray-600">SDM Mengikuti Pelatihan</h3>
                <a href="{{ route('sekretariat-jenderal.sdm-mengikuti-pelatihan.index') }}" class="text-xs text-primary hover:text-primary/80">Detail &rarr;</a>
            </div>
            <div class="text-3xl font-semibold text-gray-800">{{ number_format($totalSdmPelatihan ?? 0) }} <span class="text-sm">Peserta</span></div>
            <div class="mt-3 h-20 chart-container" id="chart-sekjen-sdm-summary">SDM Pelatihan Summary</div>
        </div>
    </div>


    {{-- Baris untuk Chart Utama --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Jumlah MoU per Bulan (Tahun {{ $selectedYear }})</h3>
            <div id="echart-sekjen-mou-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Komposisi Jenis Regulasi Baru (Tahun {{ $selectedYear }}{{ $selectedMonth ? ' - '.\Carbon\Carbon::create()->month($selectedMonth)->isoFormat('MMMM') : '' }})</h3>
            <div id="echart-sekjen-regulasi-jenis" style="width: 100%; height: 300px;"></div>
        </div>
    </div>
     <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Jumlah Kasus Ditangani per Bulan (Tahun {{ $selectedYear }})</h3>
            <div id="echart-sekjen-kasus-trend" style="width: 100%; height: 300px;"></div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Nilai Penyelesaian BMN per Bulan (Tahun {{ $selectedYear }})</h3>
            <div id="echart-sekjen-bmn-trend" style="width: 100%; height: 300px;"></div>
        </div>
    </div>
     <div class="bg-white p-5 rounded-lg shadow mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Kehadiran (WFO vs Lainnya) per Bulan (Tahun {{ $selectedYear }})</h3>
        <div id="echart-sekjen-kehadiran-trend" style="width: 100%; height: 300px;"></div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Chart Tren MoU
        var mouChartDom = document.getElementById('echart-sekjen-mou-trend');
        if (mouChartDom) {
            var mouChart = echarts.init(mouChartDom);
            var mouOption = {
                tooltip: { trigger: 'axis' },
                legend: { data: ['Jumlah MoU'] },
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: false, data: @json($mouChartLabels) },
                yAxis: { type: 'value', name: 'Jumlah MoU', min: 0 },
                series: [{
                    name: 'Jumlah MoU', type: 'line', smooth: true,
                    data: @json($mouChartDataValues),
                    itemStyle: { color: '#3b82f6' }, // Primary color
                    areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: 'rgba(59, 130, 246, 0.5)'}, {offset: 1, color: 'rgba(59, 130, 246, 0.1)'}])}
                }]
            };
            mouChart.setOption(mouOption);
            window.addEventListener('resize', () => mouChart.resize());
        }

        // 2. Chart Komposisi Jenis Regulasi
        var regulasiChartDom = document.getElementById('echart-sekjen-regulasi-jenis');
        if (regulasiChartDom) {
            var regulasiChart = echarts.init(regulasiChartDom);
            var regulasiOption = {
                tooltip: { trigger: 'item', formatter: '{a} <br/>{b}: {c} ({d}%)' },
                legend: { orient: 'vertical', left: 'left', data: @json($regulasiPerJenis->pluck('name')) },
                series: [{
                    name: 'Jenis Regulasi', type: 'pie', radius: '70%', center: ['50%', '60%'],
                    data: @json($regulasiPerJenis),
                    emphasis: { itemStyle: { shadowBlur: 10, shadowOffsetX: 0, shadowColor: 'rgba(0, 0, 0, 0.5)' } }
                }]
            };
            regulasiChart.setOption(regulasiOption);
            window.addEventListener('resize', () => regulasiChart.resize());
        }

        // 3. Chart Tren Jumlah Kasus Ditangani
        var kasusChartDom = document.getElementById('echart-sekjen-kasus-trend');
        if (kasusChartDom) {
            var kasusChart = echarts.init(kasusChartDom);
            var kasusOption = {
                tooltip: { trigger: 'axis' },
                legend: { data: ['Jumlah Kasus'] },
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: false, data: @json($kasusChartLabels) },
                yAxis: { type: 'value', name: 'Jumlah Kasus', min: 0 },
                series: [{
                    name: 'Jumlah Kasus', type: 'bar', barMaxWidth: 30,
                    data: @json($kasusChartDataValues),
                    itemStyle: { color: '#10b981' } // Green
                }]
            };
            kasusChart.setOption(kasusOption);
            window.addEventListener('resize', () => kasusChart.resize());
        }

        // 4. Chart Tren Nilai Penyelesaian BMN
        var bmnChartDom = document.getElementById('echart-sekjen-bmn-trend');
        if (bmnChartDom) {
            var bmnChart = echarts.init(bmnChartDom);
            var bmnOption = {
                tooltip: { trigger: 'axis', axisPointer: {type: 'shadow'} },
                legend: { data: ['Total Nilai Aset BMN (Rp)'] },
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                xAxis: { type: 'category', data: @json($bmnChartLabels) },
                yAxis: { type: 'value', name: 'Nilai (Rp)', axisLabel: { formatter: function (value) { return (value/1000000).toFixed(0) + ' Jt'; } } },
                series: [{
                    name: 'Total Nilai Aset BMN (Rp)', type: 'line', smooth: true,
                    data: @json($bmnChartDataValues),
                    itemStyle: { color: '#f59e0b' } // Amber
                }]
            };
            bmnChart.setOption(bmnOption);
            window.addEventListener('resize', () => bmnChart.resize());
        }
        
        // 5. Chart Tren Kehadiran
        var kehadiranChartDom = document.getElementById('echart-sekjen-kehadiran-trend');
        if (kehadiranChartDom) {
            var kehadiranChart = echarts.init(kehadiranChartDom);
            var kehadiranOption = {
                tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
                legend: { data: ['WFO', 'Lainnya (Cuti, DL, Sakit, dll)'] },
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                xAxis: { type: 'category', data: @json($kehadiranChartLabels) },
                yAxis: { type: 'value', name: 'Jumlah Orang', min: 0 },
                series: [
                    {
                        name: 'WFO', type: 'bar', stack: 'total', barMaxWidth: 40,
                        emphasis: { focus: 'series' },
                        data: @json($kehadiranWFOData),
                        itemStyle: { color: '#22c55e' } // Green
                    },
                    {
                        name: 'Lainnya (Cuti, DL, Sakit, dll)', type: 'bar', stack: 'total', barMaxWidth: 40,
                        emphasis: { focus: 'series' },
                        data: @json($kehadiranLainData),
                        itemStyle: { color: '#ef4444' } // Red
                    }
                ]
            };
            kehadiranChart.setOption(kehadiranOption);
            window.addEventListener('resize', () => kehadiranChart.resize());
        }

    });
</script>
@endpush
