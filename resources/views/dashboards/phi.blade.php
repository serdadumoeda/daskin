@extends('layouts.app')

@section('title', 'Dashboard PHI & Jamsos')
@section('page_title', 'PHI & Jamsos')

@section('header_filters')
    <form method="GET" action="{{ route('phi.dashboard') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full">
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
            <a href="{{ route('phi.dashboard') }}" class="w-full sm:w-auto text-center text-sm font-medium text-filter-btn-clear-text bg-filter-btn-clear-bg border border-filter-btn-clear-border hover:bg-red-200 focus:ring-4 focus:outline-none focus:ring-red-100 rounded-md px-4 py-2 transition-colors duration-200">
                Bersihkan
            </a>
        </div>
    </form>
@endsection

@section('content')
<div class="space-y-8">


    <!-- <h2 class="text-xl font-semibold text-gray-800 -mb-4">Kinerja Umum PHI & Jamsos</h2> -->
    @php
        $yearToDisplay = $selectedYear ?: date('Y');
        $monthValue = null;
        if ($selectedMonth && is_numeric($selectedMonth)) {
            $monthValue = (int)$selectedMonth;
        }

        if ($monthValue && $monthValue >= 1 && $monthValue <= 12) {
            $endMonthName = \Carbon\Carbon::create()->month($monthValue)->isoFormat('MMMM');
            $periodText = "Periode: Januari - " . $endMonthName . " " . $yearToDisplay;
        } else {
            $periodText = "Sepanjang Tahun " . $yearToDisplay;
        }
    @endphp

    {{-- Bagian Grafik --}}
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-5 rounded-lg shadow">
             {{-- Kartu Jumlah PHK --}}
            <a href="{{ route('phi.jumlah-phk.index') }}" class="stat-card-link-wrapper">
                <div class="stat-card">
                    <div class="stat-card-icon-wrapper bg-red-100 mr-4">
                        <i class="ri-user-unfollow-fill text-red-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">Jumlah PHK</p>
                        {{-- Pastikan variabel $totalPhk digunakan dengan benar --}}
                        <p class="stat-card-value">{{ number_format($totalTkPhk ?? 0) }} </p>
                        <p class="text-xs text-gray-500">{{ number_format($totalPerusahaanPhk ?? 0) }} Perusahaan</p>
                    </div>
                </div>
                <div class="stat-card-footer">{{ $periodText }}</div>
            </a>
            <div id="echart-phi-phk-trend" style="width: 100%; height: 300px;"></div>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
            {{-- Kartu Perselisihan (TL) --}}
            <a href="{{ route('phi.perselisihan-ditindaklanjuti.index') }}" class="stat-card-link-wrapper">
                <div class="stat-card">
                    <div class="stat-card-icon-wrapper bg-yellow-100 mr-4">
                        <i class="ri-auction-line text-yellow-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">Perselisihan (Case/Kejadian)</p>
                        {{-- Pastikan variabel $totalPerselisihan digunakan dengan benar --}}
                        <p class="stat-card-value">{{ number_format($totalPerselisihanDitindaklanjuti ?? 0) }} </p>
                    </div>
                </div>
                <div class="stat-card-footer">{{ $periodText }}</div>
            </a>
            <div id="echart-phi-perselisihan-trend" style="width: 100%; height: 300px;"></div>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
             {{-- Kartu Mediasi Berhasil --}}
            <a href="{{ route('phi.mediasi-berhasil.index') }}" class="stat-card-link-wrapper">
                <div class="stat-card">
                    <div class="stat-card-icon-wrapper bg-green-100 mr-4">
                        <i class="ri-shake-hands-line text-green-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">Mediasi Berhasil</p>
                        <p class="stat-card-value">{{ number_format($totalMediasiBerhasil ?? 0) }}</p>
                    </div>
                </div>
                <div class="stat-card-footer">{{ $periodText }}</div>
            </a>
            <div id="echart-phi-mediasi-trend" style="width: 100%; height: 300px;"></div>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
            {{-- Kartu Perusahaan Penerap SUSU --}}
            <a href="{{ route('phi.perusahaan-menerapkan-susu.index') }}" class="stat-card-link-wrapper">
                <div class="stat-card">
                    <div class="stat-card-icon-wrapper bg-blue-100 mr-4">
                        <i class="ri-currency-line text-blue-500 text-2xl"></i>
                    </div>
                    <div class="stat-card-info">
                        <p class="stat-card-title">Perusahaan Penerap SUSU</p>
                        <p class="stat-card-value">{{ number_format($totalPerusahaanSusu ?? 0) }}</p>
                    </div>
                </div>
                <div class="stat-card-footer">{{ $periodText }}</div>
            </a>
            <div id="echart-phi-susu-trend" style="width: 100%; height: 300px;"></div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {

        function createMultiSeriesChart(elementId, labels, seriesConfig) {
            const chartDom = document.getElementById(elementId);
            if (!chartDom) {
                console.error('Element chart tidak ditemukan:', elementId);
                return;
            }
            const myChart = echarts.init(chartDom);

            const series = seriesConfig.map(s => ({
                name: s.name, type: s.type, yAxisIndex: s.yAxisIndex || 0, stack: s.stack || null,
                smooth: s.type === 'line', data: s.data, itemStyle: { color: s.color }, lineStyle: { color: s.color }
            }));

            const legendData = series.map(s => s.name);

            const option = {
                tooltip: { trigger: 'axis', axisPointer: { type: 'cross' } },
                legend: { data: legendData, bottom: 0, type: 'scroll' }, // Tambahkan type: 'scroll' jika legenda terlalu banyak
                grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true }, // Bottom disesuaikan untuk legenda
                xAxis: [{ type: 'category', data: labels, axisPointer: { type: 'shadow' } }],
                yAxis: [
                    { type: 'value', name: 'Jumlah', min: 0, position: 'left' },
                    { type: 'value', name: 'Kumulatif', min: 0, position: 'right', splitLine: { show: false } }
                ],
                series: series
            };
            myChart.setOption(option);
            window.addEventListener('resize', () => myChart.resize());
        }

        const labels = @json($chartLabels ?? []);
        const chartData = @json($chartData ?? null);

        if (!chartData) {
            console.error('Data chart tidak tersedia dari controller.');
            return;
        }

        // 1. Chart PHK (2 Batang + 1 Garis)
        if (chartData.phk) {
            createMultiSeriesChart('echart-phi-phk-trend', labels, [
                { name: 'Jml TK PHK', type: 'bar', yAxisIndex: 0, data: chartData.phk.tk, color: '#ef4444' },
                { name: 'Jml Perusahaan PHK', type: 'bar', yAxisIndex: 0, data: chartData.phk.perusahaan, color: '#10b981' },
                { name: 'Kumulatif TK PHK', type: 'line', yAxisIndex: 1, data: chartData.phk.kumulatif, color: '#a855f7' }
            ]);
        }

        // 2. Chart Perselisihan (2 Batang Identik + 1 Garis) - TELAH DIPERBARUI
        if (chartData.perselisihan) {
            createMultiSeriesChart('echart-phi-perselisihan-trend', labels, [
                { name: 'Perselisihan Ditindaklanjuti', type: 'bar', yAxisIndex: 0, data: chartData.perselisihan.ditindaklanjuti, color: '#f59e0b' },
                { name: 'Jumlah Perselisihan', type: 'bar', yAxisIndex: 0, data: chartData.perselisihan.total_perselisihan, color: '#84cc16' }, // Batang baru
                { name: 'Kumulatif Total', type: 'line', yAxisIndex: 1, data: chartData.perselisihan.kumulatif, color: '#14b8a6' }
            ]);
        }

        // 3. Chart Mediasi (2 Batang + 1 Garis)
        if (chartData.mediasi) {
            createMultiSeriesChart('echart-phi-mediasi-trend', labels, [
                { name: 'Total Mediasi', type: 'bar', yAxisIndex: 0, data: chartData.mediasi.total, color: '#22c55e' },
                { name: 'Mediasi Berhasil', type: 'bar', yAxisIndex: 0, data: chartData.mediasi.berhasil, color: '#f97316' },
                { name: 'Kumulatif Berhasil', type: 'line', yAxisIndex: 1, data: chartData.mediasi.kumulatif, color: '#3b82f6' }
            ]);
        }

        // 4. Chart SUSU (1 Batang + 1 Garis)
        if (chartData.susu) {
            createMultiSeriesChart('echart-phi-susu-trend', labels, [
                { name: 'Perusahaan Terapkan SUSU', type: 'bar', yAxisIndex: 0, data: chartData.susu.susu, color: '#3b82f6' },
                { name: 'Kumulatif', type: 'none', yAxisIndex: 1, data: chartData.susu.kumulatif, color: '#ef4444' }
            ]);
        }
    });
</script>
@endpush
