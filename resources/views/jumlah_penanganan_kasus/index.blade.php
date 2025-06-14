@extends('layouts.app')

@section('title', 'Daftar Penanganan Kasus')
@section('page_title', 'Manajemen Jumlah Penanganan Kasus')

@php
if (!function_exists('sortableLinkKasus')) {
    function sortableLinkKasus(string $column, string $label, string $currentSortBy, string $currentSortDirection, array $requestFilters) {
        $newDirection = ($currentSortBy == $column && $currentSortDirection == 'asc') ? 'desc' : 'asc';
        $iconHtml = '';
        if ($currentSortBy == $column) {
            $iconClass = $currentSortDirection == 'asc' ? 'ri-arrow-up-s-fill' : 'ri-arrow-down-s-fill';
            $iconHtml = '<i class="' . $iconClass . ' ml-1"></i>';
        }
        $queryParams = array_merge(
            $requestFilters,
            ['sort_by' => $column, 'sort_direction' => $newDirection]
        );
        return '<a href="' . route('sekretariat-jenderal.jumlah-penanganan-kasus.index', $queryParams) . '" class="flex items-center hover:text-primary">' . e($label) . $iconHtml . '</a>';
    }
}
$requestFilters = request()->only(['tahun_filter', 'bulan_filter', 'substansi_filter', 'jenis_perkara_filter']);
@endphp

@section('header_filters')
    <form method="GET" action="{{ route('sekretariat-jenderal.jumlah-penanganan-kasus.index') }}" class="w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 items-end">
            <div class="flex-grow">
                <label for="tahun_filter_kasus" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                <select name="tahun_filter" id="tahun_filter_kasus" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ request('tahun_filter') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow">
                <label for="bulan_filter_kasus" class="text-sm text-gray-600 whitespace-nowrap">Bulan:</label>
                <select name="bulan_filter" id="bulan_filter_kasus" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan_filter') == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex-grow">
                <label for="substansi_filter_kasus" class="text-sm text-gray-600 whitespace-nowrap">Substansi:</label>
                <input type="text" name="substansi_filter" id="substansi_filter_kasus" value="{{ request('substansi_filter') }}" placeholder="Cari substansi..." class="form-input mt-1 w-full bg-white">
            </div>
            <div class="flex-grow">
                <label for="jenis_perkara_filter_kasus" class="text-sm text-gray-600 whitespace-nowrap">Jenis Perkara:</label>
                <input type="text" name="jenis_perkara_filter" id="jenis_perkara_filter_kasus" value="{{ request('jenis_perkara_filter') }}" placeholder="Cari jenis perkara..." class="form-input mt-1 w-full bg-white">
            </div>
            <div class="flex items-center space-x-2">
                @if(request()->filled('sort_by')) <input type="hidden" name="sort_by" value="{{ request('sort_by') }}"> @endif
                @if(request()->filled('sort_direction')) <input type="hidden" name="sort_direction" value="{{ request('sort_direction') }}"> @endif
                <button type="submit" class="btn-primary">
                    <i class="ri-filter-3-line mr-1"></i> Filter
                </button>
                <a href="{{ route('sekretariat-jenderal.jumlah-penanganan-kasus.index') }}" class="w-full sm:w-auto px-4 py-1.5 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
                    Reset
                </a>
            </div>
        </div>
    </form>
@endsection

@section('content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-md">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <div></div>
        <div class="w-full sm:w-auto sm:ml-auto flex flex-col sm:flex-row items-start sm:items-center gap-2">
            <form action="{{ route('sekretariat-jenderal.jumlah-penanganan-kasus.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                @csrf
                <div class="flex-grow">
                    <input type="file" name="excel_file" id="excel_file_kasus" required 
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
             <a href="MASUKKAN_LINK_ONEDRIVE_FORMAT_KASUS_DISINI"
               target="_blank"
               class="btn-primary">
                <i class="ri-download-2-line mr-1"></i> Unduh Format
            </a>
            <a href="{{ route('sekretariat-jenderal.jumlah-penanganan-kasus.create') }}" class="btn-primary">
                <i class="ri-add-line mr-1"></i> Tambah Data Kasus
            </a>
        </div>
    </div>

    {{-- Notifikasi session('success') dan session('error') sekarang ditangani oleh layouts.app.blade.php --}}
    {{-- Hanya 'import_errors' yang spesifik mungkin perlu dipertahankan di sini jika formatnya berbeda --}}
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
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkKasus('tahun', 'Tahun', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkKasus('bulan', 'Bulan', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkKasus('substansi', 'Substansi', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkKasus('jenis_perkara', 'Jenis Perkara', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkKasus('jumlah_perkara', 'Jumlah', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($jumlahPenangananKasuses as $index => $item)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $jumlahPenangananKasuses->firstItem() + $index }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->tahun }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ \Carbon\Carbon::create()->month($item->bulan)->isoFormat('MMMM') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 max-w-xs truncate" title="{{ $item->substansi }}">{{ Str::limit($item->substansi, 50) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 max-w-xs truncate" title="{{ $item->jenis_perkara }}">{{ Str::limit($item->jenis_perkara, 50) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 text-right">{{ number_format($item->jumlah_perkara) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex items-center justify-center space-x-2">
                               
                                <a href="{{ route('sekretariat-jenderal.jumlah-penanganan-kasus.edit', $item->id) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                                    <i class="ri-pencil-line text-base"></i>
                                </a>
                                <form action="{{ route('sekretariat-jenderal.jumlah-penanganan-kasus.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kasus ini?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                        <i class="ri-delete-bin-line text-base"></i>
                                    </button>
                                </form>
                                <!-- <a href="{{ route('sekretariat-jenderal.jumlah-penanganan-kasus.show', $item->id) }}" class="text-gray-500 hover:text-gray-700" title="Lihat">
                                    <i class="ri-eye-line text-base"></i>
                                </a> -->
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="ri-inbox-2-line text-4xl text-gray-400 mb-2"></i>
                                Tidak ada data penanganan kasus ditemukan.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">
        {{ $jumlahPenangananKasuses->appends(request()->except('page'))->links('vendor.pagination.tailwind') }}
    </div>
</div>
@endsection