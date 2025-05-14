@extends('layouts.app')

@section('title', 'Dashboard Utama Kinerja Kemnaker')
@section('page_title', 'Dashboard Utama Kinerja')

@section('header_filters')
    <form method="GET" action="{{ route('dashboard') }}" class="w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 items-end">
            <div class="flex-grow">
                <label for="year_filter_main" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                <select name="year_filter" id="year_filter_main" class="form-input mt-1 w-full bg-white">
                    @if($availableYears->isEmpty() && !$selectedYear)
                         <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                    @elseif($availableYears->isEmpty() && $selectedYear)
                        <option value="{{ $selectedYear }}" selected>{{ $selectedYear }}</option>
                    @else
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="flex-grow">
                <label for="month_filter_main" class="text-sm text-gray-600 whitespace-nowrap">Bulan:</label>
                <select name="month_filter" id="month_filter_main" class="form-input mt-1 w-full bg-white">
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
                 <a href="{{ route('dashboard') }}" class="w-full sm:w-auto px-4 py-1.5 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
                    Reset
                </a>
            </div>
        </div>
    </form>
@endsection

@section('content')
<div class="space-y-8">

    {{-- Ringkasan Eksekutif (Contoh) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-5 rounded-lg shadow-md">
            <h4 class="text-sm font-medium text-gray-500">Total Penempatan Kerja</h4>
            <p class="text-3xl font-semibold text-gray-800">{{ number_format($data['binapenta']['total_penempatan'] ?? 0) }}</p>
            <p class="text-xs text-gray-400">Periode: {{ $selectedMonth ? \Carbon\Carbon::create()->month($selectedMonth)->isoFormat('MMMM') : 'Tahun' }} {{ $selectedYear }}</p>
        </div>
        <div class="bg-white p-5 rounded-lg shadow-md">
            <h4 class="text-sm font-medium text-gray-500">Tingkat Pengangguran Terbuka (TPT)</h4>
            <p class="text-3xl font-semibold text-gray-800">{{ number_format($data['barenbang']['latest_tpt'] ?? 0, 2) }}%</p>
            <p class="text-xs text-gray-400">Data Terakhir {{ $selectedYear }}</p>
        </div>
        <div class="bg-white p-5 rounded-lg shadow-md">
            <h4 class="text-sm font-medium text-gray-500">Total Lulus Pelatihan</h4>
            <p class="text-3xl font-semibold text-gray-800">{{ number_format($data['binalavotas']['total_lulus_pelatihan'] ?? 0) }}</p>
             <p class="text-xs text-gray-400">Periode: {{ $selectedMonth ? \Carbon\Carbon::create()->month($selectedMonth)->isoFormat('MMMM') : 'Tahun' }} {{ $selectedYear }}</p>
        </div>
        <div class="bg-white p-5 rounded-lg shadow-md">
            <h4 class="text-sm font-medium text-gray-500">Total Aplikasi Terintegrasi</h4>
            <p class="text-3xl font-semibold text-gray-800">{{ number_format($data['barenbang']['total_aplikasi_integrasi'] ?? 0) }}</p>
             <p class="text-xs text-gray-400">Periode: {{ $selectedMonth ? \Carbon\Carbon::create()->month($selectedMonth)->isoFormat('MMMM') : 'Tahun' }} {{ $selectedYear }}</p>
        </div>
    </div>

    {{-- Per Unit Kerja --}}
    @foreach($data as $unitKerja => $kpis)
        @if(!empty($kpis)) {{-- Hanya tampilkan jika ada data untuk unit kerja ini --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 capitalize">{{ str_replace('_', ' ', $unitKerja) }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                @if($unitKerja == 'itjen')
                    <div class="p-4 border rounded-lg">
                        <h5 class="text-sm text-gray-500">Temuan BPK</h5>
                        <p class="text-2xl font-bold">{{ number_format($kpis['total_temuan_bpk'] ?? 0) }}</p>
                        <a href="{{ route('inspektorat.progress-temuan-bpk.index') }}" class="text-xs text-primary hover:underline">Detail</a>
                    </div>
                    <div class="p-4 border rounded-lg">
                        <h5 class="text-sm text-gray-500">Temuan Internal</h5>
                        <p class="text-2xl font-bold">{{ number_format($kpis['total_temuan_internal'] ?? 0) }}</p>
                        <a href="{{ route('inspektorat.progress-temuan-internal.index') }}" class="text-xs text-primary hover:underline">Detail</a>
                    </div>
                @elseif($unitKerja == 'sekjen')
                    <div class="p-4 border rounded-lg">
                        <h5 class="text-sm text-gray-500">MoU</h5>
                        <p class="text-2xl font-bold">{{ number_format($kpis['total_mou'] ?? 0) }}</p>
                         <a href="{{ route('sekretariat-jenderal.progress-mou.index') }}" class="text-xs text-primary hover:underline">Detail</a>
                    </div>
                    <div class="p-4 border rounded-lg">
                        <h5 class="text-sm text-gray-500">Regulasi Baru</h5>
                        <p class="text-2xl font-bold">{{ number_format($kpis['total_regulasi'] ?? 0) }}</p>
                        <a href="{{ route('sekretariat-jenderal.jumlah-regulasi-baru.index') }}" class="text-xs text-primary hover:underline">Detail</a>
                    </div>
                    <div class="p-4 border rounded-lg">
                        <h5 class="text-sm text-gray-500">Kasus Ditangani</h5>
                        <p class="text-2xl font-bold">{{ number_format($kpis['total_kasus'] ?? 0) }}</p>
                        <a href="{{ route('sekretariat-jenderal.jumlah-penanganan-kasus.index') }}" class="text-xs text-primary hover:underline">Detail</a>
                    </div>
                     <div class="p-4 border rounded-lg">
                        <h5 class="text-sm text-gray-500">Lulusan Polteknaker Bekerja</h5>
                        <p class="text-2xl font-bold">{{ number_format($kpis['total_lulusan_bekerja'] ?? 0) }}</p>
                        <a href="{{ route('sekretariat-jenderal.lulusan-polteknaker-bekerja.index') }}" class="text-xs text-primary hover:underline">Detail</a>
                    </div>
                @elseif($unitKerja == 'binapenta')
                    <div class="p-4 border rounded-lg">
                        <h5 class="text-sm text-gray-500">Penempatan Kemnaker</h5>
                        <p class="text-2xl font-bold">{{ number_format($kpis['total_penempatan'] ?? 0) }}</p>
                        <a href="{{ route('binapenta.jumlah-penempatan-kemnaker.index') }}" class="text-xs text-primary hover:underline">Detail</a>
                    </div>
                    <div class="p-4 border rounded-lg">
                        <h5 class="text-sm text-gray-500">Lowongan Pasker</h5>
                        <p class="text-2xl font-bold">{{ number_format($kpis['total_lowongan_pasker'] ?? 0) }}</p>
                        <a href="{{ route('binapenta.jumlah-lowongan-pasker.index') }}" class="text-xs text-primary hover:underline">Detail</a>
                    </div>
                    <div class="p-4 border rounded-lg">
                        <h5 class="text-sm text-gray-500">TKA Disetujui</h5>
                        <p class="text-2xl font-bold">{{ number_format($kpis['total_tka_disetujui'] ?? 0) }}</p>
                        <a href="{{ route('binapenta.jumlah-tka-disetujui.index') }}" class="text-xs text-primary hover:underline">Detail</a>
                    </div>
                @elseif($unitKerja == 'binalavotas')
                     <div class="p-4 border rounded-lg">
                        <h5 class="text-sm text-gray-500">Lulus Pelatihan</h5>
                        <p class="text-2xl font-bold">{{ number_format($kpis['total_lulus_pelatihan'] ?? 0) }}</p>
                        <a href="{{ route('binalavotas.jumlah-kepesertaan-pelatihan.index') }}" class="text-xs text-primary hover:underline">Detail</a>
                    </div>
                    <div class="p-4 border rounded-lg">
                        <h5 class="text-sm text-gray-500">Sertifikasi Kompetensi</h5>
                        <p class="text-2xl font-bold">{{ number_format($kpis['total_sertifikasi'] ?? 0) }}</p>
                        <a href="{{ route('binalavotas.jumlah-sertifikasi-kompetensi.index') }}" class="text-xs text-primary hover:underline">Detail</a>
                    </div>
                @elseif($unitKerja == 'binwasnaker')
                    <div class="p-4 border rounded-lg">
                        <h5 class="text-sm text-gray-500">Laporan WLKP</h5>
                        <p class="text-2xl font-bold">{{ number_format($kpis['total_wlkp'] ?? 0) }}</p>
                        <a href="{{ route('binwasnaker.pelaporan-wlkp-online.index') }}" class="text-xs text-primary hover:underline">Detail</a>
                    </div>
                    <div class="p-4 border rounded-lg">
                        <h5 class="text-sm text-gray-500">Pengaduan Norma (TL)</h5>
                        <p class="text-2xl font-bold">{{ number_format($kpis['total_pengaduan_norma'] ?? 0) }}</p>
                        <a href="{{ route('binwasnaker.pengaduan-pelanggaran-norma.index') }}" class="text-xs text-primary hover:underline">Detail</a>
                    </div>
                    <div class="p-4 border rounded-lg">
                        <h5 class="text-sm text-gray-500">Penerapan SMK3</h5>
                        <p class="text-2xl font-bold">{{ number_format($kpis['total_smk3'] ?? 0) }}</p>
                        <a href="{{ route('binwasnaker.penerapan-smk3.index') }}" class="text-xs text-primary hover:underline">Detail</a>
                    </div>
                @elseif($unitKerja == 'phi')
                    <div class="p-4 border rounded-lg">
                        <h5 class="text-sm text-gray-500">Jumlah PHK (TK)</h5>
                        <p class="text-2xl font-bold">{{ number_format($kpis['total_phk'] ?? 0) }}</p>
                        <a href="{{ route('phi.jumlah-phk.index') }}" class="text-xs text-primary hover:underline">Detail</a>
                    </div>
                    <div class="p-4 border rounded-lg">
                        <h5 class="text-sm text-gray-500">Mediasi Berhasil</h5>
                        <p class="text-2xl font-bold">{{ number_format($kpis['total_mediasi_berhasil'] ?? 0) }}</p>
                        <a href="{{ route('phi.mediasi-berhasil.index') }}" class="text-xs text-primary hover:underline">Detail</a>
                    </div>
                    <div class="p-4 border rounded-lg">
                        <h5 class="text-sm text-gray-500">Perusahaan Penerap SUSU</h5>
                        <p class="text-2xl font-bold">{{ number_format($kpis['total_susu'] ?? 0) }}</p>
                        <a href="{{ route('phi.perusahaan-menerapkan-susu.index') }}" class="text-xs text-primary hover:underline">Detail</a>
                    </div>
                @elseif($unitKerja == 'barenbang')
                     <div class="p-4 border rounded-lg">
                        <h5 class="text-sm text-gray-500">Kajian</h5>
                        <p class="text-2xl font-bold">{{ number_format($kpis['total_kajian'] ?? 0) }}</p>
                        <a href="{{ route('barenbang.jumlah-kajian-rekomendasi.index', ['jenis_output_filter' => 1]) }}" class="text-xs text-primary hover:underline">Detail</a>
                    </div>
                    <div class="p-4 border rounded-lg">
                        <h5 class="text-sm text-gray-500">TPT (Bulan Terakhir)</h5>
                        <p class="text-2xl font-bold">{{ number_format($kpis['latest_tpt'] ?? 0, 2) }}%</p>
                        <a href="{{ route('barenbang.data-ketenagakerjaan.index') }}" class="text-xs text-primary hover:underline">Detail</a>
                    </div>
                    <div class="p-4 border rounded-lg">
                        <h5 class="text-sm text-gray-500">Aplikasi Terintegrasi</h5>
                        <p class="text-2xl font-bold">{{ number_format($kpis['total_aplikasi_integrasi'] ?? 0) }}</p>
                        <a href="{{ route('barenbang.aplikasi-integrasi-siapkerja.index') }}" class="text-xs text-primary hover:underline">Detail</a>
                    </div>
                @endif
            </div>

            {{-- Contoh Chart untuk Unit Kerja (misalnya Binapenta) --}}
            @if($unitKerja == 'binapenta' && isset($kpis['charts']['penempatan_tren']))
            <div class="mt-6 bg-gray-50 p-4 rounded-md">
                <h4 class="text-md font-semibold text-gray-700 mb-3">Tren Penempatan oleh Kemnaker (Tahun {{ $selectedYear }})</h4>
                <div id="chart-binapenta-penempatan-trend" style="width: 100%; height: 250px;"></div>
            </div>
            @endif
            {{-- Tambahkan chart lain untuk unit kerja lain di sini --}}

        </div>
        @endif
    @endforeach
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Contoh Inisialisasi Chart untuk Binapenta (Tren Penempatan)
        var penempatanChartDom = document.getElementById('chart-binapenta-penempatan-trend');
        if (penempatanChartDom && typeof @json($data['binapenta']['charts']['penempatan_tren'] ?? null) !== 'undefined' && @json($data['binapenta']['charts']['penempatan_tren'] ?? null) !== null) {
            var penempatanChart = echarts.init(penempatanChartDom);
            var penempatanData = @json($data['binapenta']['charts']['penempatan_tren']);
            var penempatanOption = {
                tooltip: { trigger: 'axis' },
                legend: { data: ['Jumlah Penempatan'] },
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                xAxis: { type: 'category', boundaryGap: false, data: penempatanData.labels },
                yAxis: { type: 'value', name: 'Jumlah Orang', min: 0 },
                series: [{
                    name: 'Jumlah Penempatan', type: 'line', smooth: true,
                    data: penempatanData.values,
                    itemStyle: { color: '#3b82f6' }, 
                    areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: 'rgba(59, 130, 246, 0.5)'}, {offset: 1, color: 'rgba(59, 130, 246, 0.1)'}])}
                }]
            };
            penempatanChart.setOption(penempatanOption);
            window.addEventListener('resize', () => penempatanChart.resize());
        } else if (penempatanChartDom) {
            penempatanChartDom.innerHTML = '<p class="text-center text-gray-400 text-xs">Data tren penempatan tidak tersedia untuk periode ini.</p>';
        }

        // Anda perlu menambahkan inisialisasi chart serupa untuk KPI lain dari unit kerja lain
        // Contoh:
        // var kajianChartDom = document.getElementById('echart-itjen-kajian-trend');
        // if(kajianChartDom && typeof @json($data['itjen']['charts']['kajian_tren'] ?? null) !== 'undefined') { ... }
    });
</script>
@endpush
