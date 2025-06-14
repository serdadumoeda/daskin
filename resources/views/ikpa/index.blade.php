@extends('layouts.app')

@section('title', 'Indikator Kinerja Pelaksanaan Anggaran')
@section('page_title', 'Indikator Kinerja Pelaksanaan Anggaran')

@php
    if (!function_exists('sortableLinkIkpa')) {
        function sortableLinkIkpa(string $column, string $label, string $currentSortBy, string $currentSortDirection, array $requestFilters)
        {
            $newDirection = ($currentSortBy == $column && $currentSortDirection == 'asc') ? 'desc' : 'asc';
            $iconHtml = '';
            if ($currentSortBy == $column) {
                $iconClass = $currentSortDirection = 'asc' ? 'ri-arrow-up-s-fill' : 'ri-arrow-down-s-fill';
                $iconHtml = '<i class="' . $iconClass . ' ml-1"></i>';
            }
            $queryParams = array_merge(
                $requestFilters,
                ['sort_by' => $column, 'sort_direction' => $newDirection]
            );
            return '<a href="' . route('sekretariat-jenderal.ikpa.index', $queryParams) . '" class="flex items-center hover:text-primary">' . e($label) . $iconHtml . '</a>';
        }
    }
    $requestFilters = request()->only(['tahun_filter', 'bulan_filter', 'unit_kerja_filter', 'aspek_pelaksanaan_anggaran']);
@endphp

@section('header_filters')
    <form method="GET" action="{{ route('sekretariat-jenderal.ikpa.index') }}" class="w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3 items-end">
            <div class="flex-grow">
                <label for="tahun_filter_ikpa" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                <select name="tahun_filter" id="tahun_filter_ikpa" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ request('tahun_filter') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow">
                <label for="bulan_filter_ikpa" class="text-sm text-gray-600 whitespace-nowrap">Bulan:</label>
                <select name="bulan_filter" id="bulan_filter_ikpa" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan_filter') == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex-grow">
                <label for="unit_kerja_filter_ikpa" class="text-sm text-gray-600 whitespace-nowrap">Unit Kerja:</label>
                <select name="unit_kerja_filter" id="unit_kerja_filter_ikpa" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua Unit Kerja</option>
                    @foreach($unitKerjaEselonIs as $unit)
                        <option value="{{ $unit->kode_uke1 }}" {{ request('unit_kerja_filter') == $unit->kode_uke1 ? 'selected' : '' }}>
                            {{ Str::limit($unit->nama_unit_kerja_eselon_i, 30) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow">
                <label for="aspek_pelaksanaan_anggaran_ikpa" class="text-sm text-gray-600 whitespace-nowrap">Aspek Pelaksanaan Anggaran:</label>
                <select name="aspek_pelaksanaan_anggaran_filter" id="aspek_pelaksanaan_anggaran_ikpa" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua Aspek</option>
                    @foreach($aspekPelaksanaanAnggaranOptions as $aspek)
                        <option value="{{ $aspek }}" {{ request('aspek_pelaksanaan_anggaran_filter') == $aspek ? 'selected' : '' }}>
                            {{ Str::limit($aspek, 30) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center space-x-2 pt-5">
                @if(request()->filled('sort_by')) <input type="hidden" name="sort_by" value="{{ request('sort_by') }}"> @endif
                @if(request()->filled('sort_direction')) <input type="hidden" name="sort_direction" value="{{ request('sort_direction') }}"> @endif
                <button type="submit" class="btn-primary">
                    <i class="ri-filter-3-line mr-1"></i> Filter
                </button>
                <a href="{{ route('sekretariat-jenderal.ikpa.index') }}" class="w-full sm:w-auto px-4 py-1.5 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
                    Reset
                </a>
            </div>
        </div>
    </form>
@endsection

@section('content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-md">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <div class="w-full sm:w-auto sm:ml-auto flex flex-col sm:flex-row items-start sm:items-center gap-2">
            <form action="" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                @csrf
                <div class="flex-grow">
                    <input type="file" name="excel_file" id="excel_file_sdm" required
                           class="block w-full text-sm text-gray-500
                                  file:mr-2 file:py-1.5 file:px-3 file:rounded-button
                                  file:border-0 file:text-sm file:font-semibold
                                  file:bg-green-50 file:text-green-700
                                  hover:file:bg-green-100 form-input p-0.5 h-full border border-gray-300">
                </div>
                <button type="submit" class="btn-primary">
                    <i class="ri-upload-2-line mr-1"></i> Impor Data
                </button>
            </form>
             <a href="MASUKKAN_LINK_ONEDRIVE_FORMAT_SDM_PELATIHAN_DISINI"
               target="_blank"
               class="btn-primary">
                <i class="ri-download-2-line mr-1"></i> Unduh Format
            </a>
            <a href="{{ route('sekretariat-jenderal.ikpa.create') }}" class="btn-primary">
                <i class="ri-add-line mr-1"></i> Tambah Data Pelatihan
            </a>
        </div>
    </div>

    @if (session('import_errors'))
        <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-md text-sm">
            <strong class="font-bold">Beberapa data gagal diimpor:</strong>
            <ul class="mt-1 list-disc list-inside text-xs">
                @foreach (session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('error') && !session('import_errors'))
        <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-md text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkIkpa('tahun', 'Tahun', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkIkpa('bulan', 'Bulan', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkIkpa('kode_unit_kerja_eselon_i', 'Unit Kerja', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkIkpa('aspek_pelaksanaan_anggaran', 'Aspek Pelaksanaan Anggaran', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkIkpa('nilai_aspek', 'Nilai Aspek', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkIkpa('konversi_bobot', 'Koversi Bobot', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkIkpa('dispensasi_spm', 'Dispensasi SPM', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkIkpa('nilai_akhir', 'Nilai Akhir', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($indikatorKinerjaPelaksanaanAnggarans as $index => $item)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $indikatorKinerjaPelaksanaanAnggarans->firstItem() + $index }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->tahun }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ \Carbon\Carbon::create()->month($item->bulan)->isoFormat('MMMM') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->unitKerjaEselonI->nama_unit_kerja_eselon_i ?? $item->kode_unit_kerja_eselon_i }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->aspek_pelaksanaan_anggaran}}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->nilai_aspek}}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->konversi_bobot}}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->dispensasi_spm}}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->nilai_akhir}}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('sekretariat-jenderal.ikpa.edit', $item->id) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                                    <i class="ri-pencil-line text-base"></i>
                                </a>
                                <form action="{{ route('sekretariat-jenderal.ikpa.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data indek kinerja pelaksanaan anggaran ini?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                        <i class="ri-delete-bin-line text-base"></i>
                                    </button>
                                </form>
                                {{-- <a href="{{ route('sekretariat-jenderal.sdm-mengikuti-pelatihan.show', $item->id) }}" class="text-gray-500 hover:text-gray-700" title="Lihat Detail">
                                    <i class="ri-eye-line text-base"></i>
                                </a> --}}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-sm text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="ri-inbox-2-line text-4xl text-gray-400 mb-2"></i>
                                Tidak ada data indikator kinerja pelaksanaan anggaran ditemukan.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">
        {{ $indikatorKinerjaPelaksanaanAnggarans->appends(request()->except('page'))->links('vendor.pagination.tailwind') }}
    </div>
</div>
@endsection

@push('scripts')
<script>

</script>
@endpush
