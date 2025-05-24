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
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Kartu 1: Progres Tindak Lanjut Temuan BPK (Administratif) --}}
        <a href="{{ route('inspektorat.progress-temuan-bpk.index', ['jenis_temuan_filter' => 'administratif']) }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">TL BPK (Administratif)</p>
                    <p class="stat-card-value">{{ number_format($summaryBpk->total_temuan_admin_kasus ?? 0) }} Kasus</p>
                </div>
                <div class="stat-card-icon-wrapper bg-blue-100">
                    <i class="ri-file-shield-2-line text-blue-500 text-2xl"></i>
                </div>
            </div>
            <div class="stat-card-footer">
                <span class="{{ ($persentaseSelesaiBpkAdmin ?? 0) >= 75 ? 'text-green-500' : (($persentaseSelesaiBpkAdmin ?? 0) >= 50 ? 'text-yellow-500' : 'text-red-500') }}">
                    {{ number_format($persentaseSelesaiBpkAdmin ?? 0, 2) }}% Selesai
                </span>
                ({{ number_format($summaryBpk->total_tl_admin_kasus ?? 0) }}/{{ number_format($summaryBpk->total_temuan_admin_kasus ?? 0) }})
            </div>
        </a>

        {{-- Kartu 2: Progres Tindak Lanjut Temuan BPK (Kerugian Negara) --}}
        <a href="{{ route('inspektorat.progress-temuan-bpk.index', ['jenis_temuan_filter' => 'kerugian_negara']) }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">TL BPK (Kerugian Negara)</p>
                    <p class="stat-card-value">Rp {{ number_format($summaryBpk->total_temuan_kerugian_rp ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="stat-card-icon-wrapper bg-red-100">
                    <i class="ri-money-dollar-circle-line text-red-500 text-2xl"></i>
                </div>
            </div>
            <div class="stat-card-footer">
                <span class="{{ ($persentaseSelesaiBpkKerugian ?? 0) >= 75 ? 'text-green-500' : (($persentaseSelesaiBpkKerugian ?? 0) >= 50 ? 'text-yellow-500' : 'text-red-500') }}">
                    {{ number_format($persentaseSelesaiBpkKerugian ?? 0, 2) }}% Selesai
                </span>
                (Rp {{ number_format($summaryBpk->total_tl_kerugian_rp ?? 0, 0, ',', '.') }})
            </div>
        </a>

        {{-- Kartu 3: Progres Tindak Lanjut Temuan Internal (Administratif) --}}
        <a href="{{ route('inspektorat.progress-temuan-internal.index', ['jenis_temuan_filter' => 'administratif']) }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">TL Internal (Administratif)</p>
                     <p class="stat-card-value">{{ number_format($summaryInternal->total_temuan_admin_kasus ?? 0) }} Kasus</p>
                </div>
                <div class="stat-card-icon-wrapper bg-purple-100">
                    <i class="ri-file-search-line text-purple-500 text-2xl"></i>
                </div>
            </div>
             <div class="stat-card-footer">
                <span class="{{ ($persentaseSelesaiInternalAdmin ?? 0) >= 75 ? 'text-green-500' : (($persentaseSelesaiInternalAdmin ?? 0) >= 50 ? 'text-yellow-500' : 'text-red-500') }}">
                    {{ number_format($persentaseSelesaiInternalAdmin ?? 0, 2) }}% Selesai
                </span>
                 ({{ number_format($summaryInternal->total_tl_admin_kasus ?? 0) }}/{{ number_format($summaryInternal->total_temuan_admin_kasus ?? 0) }})
            </div>
        </a>

        {{-- Kartu 4: Progres Tindak Lanjut Temuan Internal (Kerugian Negara) --}}
        <a href="{{ route('inspektorat.progress-temuan-internal.index', ['jenis_temuan_filter' => 'kerugian_negara']) }}" class="stat-card-link-wrapper">
            <div class="stat-card">
                <div class="stat-card-info">
                    <p class="stat-card-title">TL Internal (Kerugian Negara)</p>
                     <p class="stat-card-value">Rp {{ number_format($summaryInternal->total_temuan_kerugian_rp ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="stat-card-icon-wrapper bg-yellow-100">
                     <i class="ri-refund-2-line text-yellow-500 text-2xl"></i>
                </div>
            </div>
             <div class="stat-card-footer">
                <span class="{{ ($persentaseSelesaiInternalKerugian ?? 0) >= 75 ? 'text-green-500' : (($persentaseSelesaiInternalKerugian ?? 0) >= 50 ? 'text-yellow-500' : 'text-red-500') }}">
                     {{ number_format($persentaseSelesaiInternalKerugian ?? 0, 2) }}% Selesai
                </span>
                 (Rp {{ number_format($summaryInternal->total_tl_kerugian_rp ?? 0, 0, ',', '.') }})
            </div>
        </a>
    </section>

    {{-- Baris untuk Chart --}}
    <section class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        {{-- Chart Temuan BPK --}}
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-sm font-medium text-gray-600 mb-3">Grafik Tren Progres Temuan BPK</h3>
            <div class="h-64" id="echart-bpk-trend"></div>
        </div>

        {{-- Chart Temuan Internal --}}
        <div class="bg-white p-5 rounded-lg shadow">
             <h3 class="text-sm font-medium text-gray-600 mb-3">Grafik Tren Progres Temuan Internal</h3>
            <div class="h-64" id="echart-internal-trend"></div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
{{-- Jika ECharts belum di-include secara global di layouts.app, uncomment baris di bawah atau include di sana --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script> --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Chart untuk Tren Temuan BPK
        var bpkChartDom = document.getElementById('echart-bpk-trend');
        if (bpkChartDom) {
            var bpkChart = echarts.init(bpkChartDom);
            var bpkOption = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: { type: 'cross', crossStyle: { color: '#999' } },
                    formatter: function (params) {
                        let tooltipText = params[0].name + '<br/>';
                        params.forEach(function (item) {
                            let value = item.value;
                            if (item.seriesName.includes('(Rp)')) {
                                value = 'Rp ' + parseFloat(value).toLocaleString('id-ID');
                            } else {
                                value = parseFloat(value).toLocaleString('id-ID') + ' Kasus';
                            }
                            tooltipText += item.marker + item.seriesName + ': ' + value + '<br/>';
                        });
                        return tooltipText;
                    }
                },
                legend: {
                    data: ['Temuan Admin (Kasus)', 'TL Admin (Kasus)', 'Temuan Kerugian (Rp)', 'TL Kerugian (Rp)'],
                    textStyle: { fontSize: 10 },
                    bottom: 0
                },
                grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
                xAxis: [{
                    type: 'category',
                    boundaryGap: true,
                    data: @json($bpkChartLabels ?? []),
                    axisLabel: { fontSize: 10 }
                }],
                yAxis: [
                    {
                        type: 'value', name: 'Jumlah Kasus', min: 0,
                        axisLabel: { formatter: '{value}', fontSize: 10 },
                        nameTextStyle: { fontSize: 10, padding: [0, 0, 0, 30] }
                    },
                    {
                        type: 'value', name: 'Nilai (Rp)', min: 0,
                        axisLabel: { formatter: function (value) { return (value/1000000).toFixed(1) + ' Jt'; }, fontSize: 10 },
                        nameTextStyle: { fontSize: 10, padding: [0, 30, 0, 0] }
                    }
                ],
                series: [
                    {
                        name: 'Temuan Admin (Kasus)', type: 'bar', yAxisIndex: 0, barGap: '20%', barMaxWidth: 20,
                        data: @json($bpkChartData['temuan_admin'] ?? []), itemStyle: { color: '#3b82f6' }
                    },
                    {
                        name: 'TL Admin (Kasus)', type: 'bar', yAxisIndex: 0, barMaxWidth: 20,
                        data: @json($bpkChartData['tl_admin'] ?? []), itemStyle: { color: '#10b981' }
                    },
                    {
                        name: 'Temuan Kerugian (Rp)', type: 'line', smooth: true, yAxisIndex: 1,
                        data: @json($bpkChartData['temuan_kerugian'] ?? []), itemStyle: { color: '#f59e0b' },
                        lineStyle: { width: 2 },
                        symbol: 'circle', symbolSize: 6
                    },
                    {
                        name: 'TL Kerugian (Rp)', type: 'line', smooth: true, yAxisIndex: 1,
                        data: @json($bpkChartData['tl_kerugian'] ?? []), itemStyle: { color: '#ef4444' },
                        lineStyle: { width: 2 },
                        symbol: 'circle', symbolSize: 6
                    }
                ]
            };
            bpkChart.setOption(bpkOption);
            window.addEventListener('resize', () => bpkChart.resize());
        }

        // Chart untuk Tren Temuan Internal
        var internalChartDom = document.getElementById('echart-internal-trend');
        if (internalChartDom) {
            var internalChart = echarts.init(internalChartDom);
            var internalOption = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: { type: 'cross', crossStyle: { color: '#999' } },
                     formatter: function (params) {
                        let tooltipText = params[0].name + '<br/>';
                        params.forEach(function (item) {
                            let value = item.value;
                            if (item.seriesName.includes('(Rp)')) {
                                value = 'Rp ' + parseFloat(value).toLocaleString('id-ID');
                            } else {
                                value = parseFloat(value).toLocaleString('id-ID') + ' Kasus';
                            }
                            tooltipText += item.marker + item.seriesName + ': ' + value + '<br/>';
                        });
                        return tooltipText;
                    }
                },
                legend: {
                    data: ['Temuan Admin (Kasus)', 'TL Admin (Kasus)', 'Temuan Kerugian (Rp)', 'TL Kerugian (Rp)'],
                    textStyle: { fontSize: 10 },
                    bottom: 0
                },
                grid: { left: '3%', right: '4%', bottom: '15%', containLabel: true },
                xAxis: [{
                    type: 'category',
                    boundaryGap: true,
                    data: @json($internalChartLabels ?? []),
                    axisLabel: { fontSize: 10 }
                }],
                 yAxis: [
                    {
                        type: 'value', name: 'Jumlah Kasus', min: 0,
                        axisLabel: { formatter: '{value}', fontSize: 10 },
                        nameTextStyle: { fontSize: 10, padding: [0, 0, 0, 30] }
                    },
                    {
                        type: 'value', name: 'Nilai (Rp)', min: 0,
                        axisLabel: { formatter: function (value) { return (value/1000000).toFixed(1) + ' Jt'; }, fontSize: 10 },
                        nameTextStyle: { fontSize: 10, padding: [0, 30, 0, 0] }
                    }
                ],
                series: [
                    {
                        name: 'Temuan Admin (Kasus)', type: 'bar', yAxisIndex: 0, barGap:'20%', barMaxWidth: 20,
                        data: @json($internalChartData['temuan_admin'] ?? []), itemStyle: { color: '#8b5cf6' }
                    },
                    {
                        name: 'TL Admin (Kasus)', type: 'bar', yAxisIndex: 0, barMaxWidth: 20,
                        data: @json($internalChartData['tl_admin'] ?? []), itemStyle: { color: '#6366f1' }
                    },
                     {
                        name: 'Temuan Kerugian (Rp)', type: 'line', smooth: true, yAxisIndex: 1,
                        data: @json($internalChartData['temuan_kerugian'] ?? []), itemStyle: { color: '#d946ef' },
                        lineStyle: { width: 2 },
                        symbol: 'circle', symbolSize: 6
                    },
                    {
                        name: 'TL Kerugian (Rp)', type: 'line', smooth: true, yAxisIndex: 1,
                        data: @json($internalChartData['tl_kerugian'] ?? []), itemStyle: { color: '#ec4899' },
                        lineStyle: { width: 2 },
                        symbol: 'circle', symbolSize: 6
                    }
                ]
            };
            internalChart.setOption(internalOption);
            window.addEventListener('resize', () => internalChart.resize());
        }
    });
</script>
@endpush