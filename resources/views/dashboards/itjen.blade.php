@extends('layouts.app')

@section('title', 'Dashboard Inspektorat Jenderal')
@section('page_title', 'Inspektorat Jenderal')

@section('header_filters')
<form method="GET" action="{{ route('inspektorat.dashboard') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full">
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
            <a href="{{ route('inspektorat.dashboard') }}" class="w-full sm:w-auto text-center text-sm font-medium text-filter-btn-clear-text bg-filter-btn-clear-bg border border-filter-btn-clear-border hover:bg-red-200 focus:ring-4 focus:outline-none focus:ring-red-100 rounded-md px-4 py-2 transition-colors duration-200">
                Bersihkan
            </a>
        </div>
    </form>

@endsection

@section('content')
<div class="space-y-6">
    {{-- Baris untuk Kartu Ringkasan Statistik --}}
    {{-- Variabel $summaryBpk dan $summaryInternal diasumsikan dikirim dari ItjenDashboardController --}}
    {{-- Variabel persentase juga diasumsikan dikirim dari controller --}}
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
        {{-- Kartu 1: Progres Tindak Lanjut Temuan BPK (Administratif) --}}
        <a href="{{ route('inspektorat.progress-temuan-bpk.index', ['jenis_temuan_filter' => 'administratif']) }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">TL BPK (Administratif)</p>
                    <p class="stat-card-value">{{ number_format($persentaseSelesaiBpkAdmin ?? 0, 2) }}% Selesai</p>
                </div>
                <div class="stat-card-icon-wrapper bg-blue-100">
                    <i class="ri-file-shield-2-line text-blue-500 text-2xl"></i>
                </div>
            </div>
            <div class="stat-card-footer">
                <span class="{{ ($persentaseSelesaiBpkAdmin ?? 0) >= 75 ? 'text-green-500' : (($persentaseSelesaiBpkAdmin ?? 0) >= 50 ? 'text-yellow-500' : 'text-red-500') }}">
                {{ number_format($summaryBpk->total_tl_admin_kasus ?? 0) }}/{{ number_format($summaryBpk->total_temuan_admin_kasus ?? 0) }} Kasus
                </span>
                
            </div>
        </a>



        {{-- Kartu 3: Progres Tindak Lanjut Temuan Internal (Administratif) --}}
        <a href="{{ route('inspektorat.progress-temuan-internal.index', ['jenis_temuan_filter' => 'administratif']) }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">TL Internal (Administratif)</p>
                     <p class="stat-card-value">{{ number_format($persentaseSelesaiInternalAdmin ?? 0, 2) }}% Selesai </p>
                </div>
                <div class="stat-card-icon-wrapper bg-purple-100">
                    <i class="ri-file-search-line text-purple-500 text-2xl"></i>
                </div>
            </div>
             <div class="stat-card-footer">
                <span class="{{ ($persentaseSelesaiInternalAdmin ?? 0) >= 75 ? 'text-green-500' : (($persentaseSelesaiInternalAdmin ?? 0) >= 50 ? 'text-yellow-500' : 'text-red-500') }}">
                {{ number_format($summaryInternal->total_tl_admin_kasus ?? 0) }}/{{ number_format($summaryInternal->total_temuan_admin_kasus ?? 0) }} Kasus
                </span>
                 
            </div>
        </a>

   
    </section>

    {{-- Baris untuk Chart --}}
    <div class="bg-white p-6 rounded-xl shadow-md lg:col-span-2">
        <div class="bg-white p-6 rounded-xl shadow-md">
            <h3 class="font-semibold text-lg text-gray-800 mb-4">Progress Tindak Lanjut Temuan BPK</h3>
            <div id="echart-itjen-bpk-trend" style="height: 400px;"></div>
        </div>

    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
       
        <div class="bg-white p-6 rounded-xl shadow-md lg:col-span-2">
            <h3 class="font-semibold text-lg text-gray-800 mb-4">Progress Tindak Lanjut Temuan Internal</h3>
            <div id="echart-itjen-internal-trend" style="height: 400px;"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        
        // Fungsi untuk membuat chart dengan banyak seri (batang/garis)
        function createMultiSeriesChart(elementId, labels, seriesConfig, yAxisNames = ['Jumlah Kasus', 'Persentase (%)']) {
            const chartDom = document.getElementById(elementId);
            if (!chartDom) { 
                console.error('Elemen chart tidak ditemukan:', elementId);
                return; 
            }
            let existingChart = echarts.getInstanceByDom(chartDom);
            if (existingChart) { existingChart.dispose(); }
            const myChart = echarts.init(chartDom);
            
            const series = seriesConfig.map(s => ({
                name: s.name, type: s.type, yAxisIndex: s.yAxisIndex || 0, stack: s.stack || null,
                smooth: s.type === 'line', data: s.data, itemStyle: { color: s.color }, lineStyle: { color: s.color, width: s.lineWidth || 2 },
                symbol: 'circle', symbolSize: s.symbolSize || (s.type === 'line' ? 6 : 0),
                tooltip: {
                    valueFormatter: function (value) {
                        return parseFloat(value).toLocaleString('id-ID') + (s.unit || '');
                    }
                }
            }));
            const legendData = series.map(s => s.name);

            const option = {
                tooltip: { 
                    trigger: 'axis', 
                    axisPointer: { type: 'cross' },
                },
                legend: { data: legendData, bottom: 0, type: 'scroll' },
                grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
                xAxis: [{ type: 'category', data: labels, axisPointer: { type: 'shadow' } }],
                yAxis: [
                    { type: 'value', name: yAxisNames[0], min: 0, position: 'left', 
                      axisLabel: { formatter: function(value) { return parseFloat(value).toLocaleString('id-ID');} } 
                    },
                    { type: 'value', name: yAxisNames[1], min: 0, max: 100, // Sumbu Y untuk persentase (0-100)
                      position: 'right', splitLine: { show: false }, 
                      axisLabel: { formatter: '{value} %'} 
                    }
                ],
                series: series
            };
            myChart.setOption(option);
            window.addEventListener('resize', () => myChart.resize());
        }

        const allChartData = @json($allChartData ?? null);

        if (!allChartData) {
            console.error('Variabel allChartData utama tidak tersedia dari controller.');
            const chartIds = ['echart-itjen-bpk-trend', 'echart-itjen-internal-trend'];
            chartIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) { el.innerHTML = '<p class="text-center text-gray-500 py-5">Data chart tidak tersedia.</p>'; }
            });
            return;
        }

        // Fungsi render helper
        function renderItjenKasusChart(chartId, dataKey, chartTitlePrefix) {
            const chartEl = document.getElementById(chartId);
            if (chartEl) {
                if (allChartData[dataKey] && allChartData[dataKey].labels && 
                    allChartData[dataKey].temuan_admin_kasus && allChartData[dataKey].tl_admin_kasus &&
                    allChartData[dataKey].persentase_kumulatif) {
                    
                    const isDataEmpty = allChartData[dataKey].temuan_admin_kasus.every(val => val === 0) &&
                                        allChartData[dataKey].tl_admin_kasus.every(val => val === 0);

                    if (allChartData[dataKey].labels.length > 0 && !isDataEmpty) {
                        createMultiSeriesChart(chartId, allChartData[dataKey].labels, [
                            { name: `Temuan Admin ${chartTitlePrefix} (Kasus)`, type: 'bar', yAxisIndex: 0, data: allChartData[dataKey].temuan_admin_kasus, color: '#3b82f6', unit: ' Kasus' },
                            { name: `TL Admin ${chartTitlePrefix} (Kasus)`, type: 'bar', yAxisIndex: 0, data: allChartData[dataKey].tl_admin_kasus, color: '#10b981', unit: ' Kasus' },
                            { name: `Penyelesaian Kumulatif ${chartTitlePrefix} (%)`, type: 'line', yAxisIndex: 1, data: allChartData[dataKey].persentase_kumulatif, color: '#ef4444', unit: ' %', lineWidth: 3, symbolSize: 8 }
                        ]);
                    } else {
                        chartEl.innerHTML = `<p class="text-center text-gray-500 py-5">Tidak ada data untuk ditampilkan pada chart ${chartTitlePrefix}.</p>`;
                    }
                } else {
                    console.warn(`Data untuk chart ${chartTitlePrefix} tidak lengkap. Data diterima:`, allChartData[dataKey]);
                    chartEl.innerHTML = `<p class="text-center text-gray-500 py-5">Data chart ${chartTitlePrefix} tidak tersedia.</p>`;
                }
            }
        }

        // 1. Render Chart Temuan BPK (Fokus Kasus & Persentase Penyelesaian)
        renderItjenKasusChart('echart-itjen-bpk-trend', 'bpk', 'BPK');
        
        // 2. Render Chart Temuan Internal (Fokus Kasus & Persentase Penyelesaian)
        renderItjenKasusChart('echart-itjen-internal-trend', 'internal', 'Internal');

    });
</script>
@endpush