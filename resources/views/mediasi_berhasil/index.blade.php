@extends('layouts.app')

@section('title', 'Jumlah Mediasi Berhasil')
@section('page_title', 'Manajemen Jumlah Mediasi Berhasil')

{{-- TIDAK DIUBAH: Blok PHP dan fungsi sortableLink asli Anda dipertahankan --}}
@php
if (!function_exists('sortableLinkMediasi')) {
    function sortableLinkMediasi(string $column, string $label, string $currentSortBy, string $currentSortDirection, array $requestFilters) {
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
        return '<a href="' . route('phi.mediasi-berhasil.index', $queryParams) . '" class="flex items-center hover:text-primary">' . e($label) . $iconHtml . '</a>';
    }
}
$sortBy = $currentSortBy ?? request('sort_by', 'id');
$sortDirection = $currentSortDirection ?? request('sort_direction', 'desc');
$requestFilters = request()->only(['tahun_filter', 'bulan_filter', 'provinsi_filter', 'kbli_filter', 'jenis_perselisihan_filter', 'hasil_mediasi_filter']);
@endphp


@section('header_filters')
    {{-- TIDAK DIUBAH: Filter Section asli Anda dipertahankan --}}
    <form method="GET" action="{{ route('phi.mediasi-berhasil.index') }}" class="w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-3 items-end">
            <div class="flex-grow">
                <label for="tahun_filter_mediasi" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                <select name="tahun_filter" id="tahun_filter_mediasi" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ request('tahun_filter') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow">
                <label for="bulan_filter_mediasi" class="text-sm text-gray-600 whitespace-nowrap">Bulan:</label>
                <select name="bulan_filter" id="bulan_filter_mediasi" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan_filter') == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex-grow">
                <label for="provinsi_filter_mediasi" class="text-sm text-gray-600 whitespace-nowrap">Provinsi:</label>
                <input type="text" name="provinsi_filter" id="provinsi_filter_mediasi" value="{{ request('provinsi_filter') }}" placeholder="Cari provinsi..." class="form-input mt-1 w-full bg-white">
            </div>
            <div class="flex-grow">
                <label for="kbli_filter_mediasi" class="text-sm text-gray-600 whitespace-nowrap">KBLI:</label>
                <input type="text" name="kbli_filter" id="kbli_filter_mediasi" value="{{ request('kbli_filter') }}" placeholder="Cari KBLI..." class="form-input mt-1 w-full bg-white">
            </div>
            <div class="flex-grow">
                <label for="jenis_perselisihan_filter_mediasi" class="text-sm text-gray-600 whitespace-nowrap">Jenis Perselisihan:</label>
                 <select name="jenis_perselisihan_filter" id="jenis_perselisihan_filter_mediasi" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua Jenis</option>
                    @foreach($jenisPerselisihanOptions as $key => $value)
                        <option value="{{ $key }}" {{ request('jenis_perselisihan_filter') == $key ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow">
                <label for="hasil_mediasi_filter_mediasi" class="text-sm text-gray-600 whitespace-nowrap">Hasil Mediasi:</label>
                 <select name="hasil_mediasi_filter" id="hasil_mediasi_filter_mediasi" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua Hasil</option>
                    @foreach($hasilMediasiOptions as $key => $value)
                        <option value="{{ $key }}" {{ request('hasil_mediasi_filter') == $key ? 'selected' : '' }}>
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
                <a href="{{ route('phi.mediasi-berhasil.index') }}" class="btn-secondary-outline">
                    Clear Filter
                </a>
            </div>
        </div>
    </form>
@endsection


@section('content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-md">
    
    @if (Auth::user()->role === 'superadmin' || Auth::user()->role === 'phi')
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <div class="w-full sm:w-auto sm:ml-auto flex flex-col sm:flex-row items-start sm:items-center gap-2">
                <form action="{{ route('phi.mediasi-berhasil.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                    @csrf
                    <div class="flex-grow">
                        <input type="file" name="excel_file" id="excel_file_mediasi" required 
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
                 <a href=""
                   target="_blank"
                   class="btn-primary">
                    <i class="ri-download-2-line mr-1"></i> Unduh Format
                </a>
                <a href="{{ route('phi.mediasi-berhasil.create') }}" class="btn-primary">
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
                    
                    <th scope="col">{!! sortableLinkMediasi('tahun', 'Tahun', $sortBy, $sortDirection, $requestFilters) !!}</th>
                    <th scope="col">{!! sortableLinkMediasi('bulan', 'Bulan', $sortBy, $sortDirection, $requestFilters) !!}</th>
                    <th scope="col">{!! sortableLinkMediasi('provinsi', 'Provinsi', $sortBy, $sortDirection, $requestFilters) !!}</th>
                    <th scope="col">{!! sortableLinkMediasi('kbli', 'KBLI', $sortBy, $sortDirection, $requestFilters) !!}</th>
                    <th scope="col">{!! sortableLinkMediasi('jenis_perselisihan', 'Jenis Perselisihan', $sortBy, $sortDirection, $requestFilters) !!}</th>
                    <th scope="col">{!! sortableLinkMediasi('hasil_mediasi', 'Hasil Mediasi', $sortBy, $sortDirection, $requestFilters) !!}</th>
                    <th scope="col" class="text-right">{!! sortableLinkMediasi('jumlah_mediasi', 'Jml Mediasi', $sortBy, $sortDirection, $requestFilters) !!}</th>
                    <th scope="col" class="text-right">{!! sortableLinkMediasi('jumlah_mediasi_berhasil', 'Jml Berhasil', $sortBy, $sortDirection, $requestFilters) !!}</th>
                    <th scope="col" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                {{-- TIDAK DIUBAH: Isi tabel asli agar data tidak kosong --}}
                @forelse ($mediasiBerhasils as $index => $item)
                    <tr>
                        
                        <td>{{ $item->tahun }}</td>
                        <td>{{ \Carbon\Carbon::create()->month($item->bulan)->isoFormat('MMMM') }}</td>
                        <td>{{ $item->provinsi }}</td>
                        <td>{{ $item->kbli }}</td>
                        <td>{{ $item->jenis_perselisihan }}</td>
                        <td>{{ $item->hasil_mediasi }}</td>
                        <td class="text-right">{{ number_format($item->jumlah_mediasi) }}</td>
                        <td class="text-right">{{ number_format($item->jumlah_mediasi_berhasil) }}</td>
                        <td class="text-center">
                            {{-- DITERAPKAN: Gaya terpusat untuk grup aksi --}}
                            <div class="table-actions justify-center">
                                <a href="{{ route('phi.mediasi-berhasil.edit', $item->id) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                                    <i class="ri-pencil-line text-base"></i>
                                </a>
                                <form action="{{ route('phi.mediasi-berhasil.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data mediasi ini?');" style="display: inline;">
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
                        <td colspan="10" class="text-center py-10">
                            <div class="flex flex-col items-center text-gray-500">
                                <i class="ri-inbox-2-line text-4xl mb-2"></i>
                                <span>Tidak ada data mediasi berhasil ditemukan.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination Asli (Tidak Diubah) --}}
    <div class="mt-6">
        {{ $mediasiBerhasils->appends(request()->except('page'))->links('vendor.pagination.tailwind') }}
    </div>
</div>
@endsection