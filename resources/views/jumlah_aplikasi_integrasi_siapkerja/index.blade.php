@extends('layouts.app')

@section('title', 'Aplikasi Terintegrasi SiapKerja')
@section('page_title', 'Manajemen Aplikasi Terintegrasi SiapKerja')

{{-- TIDAK DIUBAH: Blok PHP dan fungsi sortableLink asli Anda dipertahankan --}}
@php
if (!function_exists('sortableLinkAplikasiIntegrasi')) {
    function sortableLinkAplikasiIntegrasi(string $column, string $label, string $currentSortBy, string $currentSortDirection, array $requestFilters) {
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
        return '<a href="' . route('barenbang.aplikasi-integrasi-siapkerja.index', $queryParams) . '" class="flex items-center hover:text-primary">' . e($label) . $iconHtml . '</a>';
    }
}
$sortBy = $currentSortBy ?? request('sort_by', 'id');
$sortDirection = $currentSortDirection ?? request('sort_direction', 'desc');
$requestFilters = request()->only(['tahun_filter', 'bulan_filter', 'jenis_instansi_filter', 'nama_instansi_filter', 'status_integrasi_filter']);
@endphp

@section('header_filters')
    {{-- TIDAK DIUBAH: Filter Section asli Anda dipertahankan --}}
    <form method="GET" action="{{ route('barenbang.aplikasi-integrasi-siapkerja.index') }}" class="w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 items-end">
            <div class="flex-grow">
                <label for="tahun_filter_integrasi" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                <select name="tahun_filter" id="tahun_filter_integrasi" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ request('tahun_filter') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow">
                <label for="bulan_filter_integrasi" class="text-sm text-gray-600 whitespace-nowrap">Bulan:</label>
                <select name="bulan_filter" id="bulan_filter_integrasi" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan_filter') == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex-grow">
                <label for="jenis_instansi_filter_integrasi" class="text-sm text-gray-600 whitespace-nowrap">Jenis Instansi:</label>
                 <select name="jenis_instansi_filter" id="jenis_instansi_filter_integrasi" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @foreach($jenisInstansiOptions as $key => $value)
                        <option value="{{ $key }}" {{ request('jenis_instansi_filter') == $key ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow">
                <label for="nama_instansi_filter_integrasi" class="text-sm text-gray-600 whitespace-nowrap">Nama Instansi:</label>
                <input type="text" name="nama_instansi_filter" id="nama_instansi_filter_integrasi" value="{{ request('nama_instansi_filter') }}" placeholder="Cari nama instansi..." class="form-input mt-1 w-full bg-white">
            </div>
            <div class="flex-grow">
                <label for="status_integrasi_filter_integrasi" class="text-sm text-gray-600 whitespace-nowrap">Status Integrasi:</label>
                 <select name="status_integrasi_filter" id="status_integrasi_filter_integrasi" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @foreach($statusIntegrasiOptions as $key => $value)
                        <option value="{{ $key }}" {{ request('status_integrasi_filter') == $key ? 'selected' : '' }}>
                            {{ $value }}
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
                <a href="{{ route('barenbang.aplikasi-integrasi-siapkerja.index') }}" class="btn-secondary-outline">
                    Clear Filter
                </a>
            </div>
        </div>
    </form>
@endsection


@section('content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-md">
    
    @if (Auth::user()->role === 'superadmin' || Auth::user()->role === 'barenbang')
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <div class="w-full sm:w-auto sm:ml-auto flex flex-col sm:flex-row items-start sm:items-center gap-2">
                <form action="{{ route('barenbang.aplikasi-integrasi-siapkerja.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                    @csrf
                    <div class="flex-grow">
                        <input type="file" name="excel_file" id="excel_file_integrasi" required 
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
                 <a href="unduh"
                   target="_blank"
                   class="btn-primary">
                    <i class="ri-download-2-line mr-1"></i> Unduh Format
                </a>
                <a href="{{ route('barenbang.aplikasi-integrasi-siapkerja.create') }}" class="btn-primary">
                    <i class="ri-add-line mr-1"></i> Tambah Data
                </a>
            </div>
        </div>
    @endif

    {{-- Pesan Error & Sukses (Tidak Diubah) --}}
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
    
    {{-- DITERAPKAN: Gaya tabel modern ke struktur tabel asli --}}
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                {{-- TIDAK DIUBAH: Header tabel asli yang berfungsi --}}
                <tr>
                    
                    <th scope="col">{!! sortableLinkAplikasiIntegrasi('tahun', 'Tahun', $sortBy, $sortDirection, $requestFilters) !!}</th>
                    <th scope="col">{!! sortableLinkAplikasiIntegrasi('bulan', 'Bulan', $sortBy, $sortDirection, $requestFilters) !!}</th>
                    <th scope="col">{!! sortableLinkAplikasiIntegrasi('jenis_instansi', 'Jenis Instansi', $sortBy, $sortDirection, $requestFilters) !!}</th>
                    <th scope="col">{!! sortableLinkAplikasiIntegrasi('nama_instansi', 'Nama Instansi', $sortBy, $sortDirection, $requestFilters) !!}</th>
                    <th scope="col">{!! sortableLinkAplikasiIntegrasi('nama_aplikasi_website', 'Nama Aplikasi/Website', $sortBy, $sortDirection, $requestFilters) !!}</th>
                    <th scope="col">{!! sortableLinkAplikasiIntegrasi('status_integrasi', 'Status Integrasi', $sortBy, $sortDirection, $requestFilters) !!}</th>
                    <th scope="col" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                {{-- TIDAK DIUBAH: Isi tabel asli agar data tidak kosong --}}
                @forelse ($aplikasiIntegrasiSiapkerjas as $index => $item)
                    <tr>
                        
                        <td>{{ $item->tahun }}</td>
                        <td>{{ \Carbon\Carbon::create()->month($item->bulan)->isoFormat('MMMM') }}</td>
                        <td>{{ $item->jenis_instansi_text }}</td>
                        <td>{{ $item->nama_instansi }}</td>
                        <td>{{ $item->nama_aplikasi_website }}</td>
                        <td>
                             @if($item->status_integrasi == 1)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $item->status_integrasi_text }}
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    {{ $item->status_integrasi_text }}
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{-- DITERAPKAN: Gaya terpusat untuk grup aksi --}}
                            <div class="table-actions justify-center">
                                <a href="{{ route('barenbang.aplikasi-integrasi-siapkerja.edit', $item->id) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                                    <i class="ri-pencil-line text-base"></i>
                                </a>
                                <form action="{{ route('barenbang.aplikasi-integrasi-siapkerja.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                        <i class="ri-delete-bin-line text-base"></i>
                                    </button>
                                </form>
                 
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-10">
                            <div class="flex flex-col items-center text-gray-500">
                                <i class="ri-inbox-2-line text-4xl mb-2"></i>
                                <span>Tidak ada data aplikasi terintegrasi ditemukan.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Asli (Tidak Diubah) --}}
    <div class="mt-6">
        {{ $aplikasiIntegrasiSiapkerjas->appends(request()->except('page'))->links('vendor.pagination.tailwind') }}
    </div>
</div>
@endsection