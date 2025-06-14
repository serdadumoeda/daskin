@extends('layouts.app')

{{-- Menyesuaikan judul halaman --}}
@section('title', 'Daftar Regulasi Baru')
@section('page_title', 'Manajemen Jumlah Regulasi Baru')

@php
// Helper function untuk link sorting
if (!function_exists('sortableLinkRegulasi')) {
    function sortableLinkRegulasi(string $column, string $label, string $currentSortBy, string $currentSortDirection, array $requestFilters) {
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
        return '<a href="' . route('sekretariat-jenderal.jumlah-regulasi-baru.index', $queryParams) . '" class="flex items-center hover:text-primary">' . e($label) . $iconHtml . '</a>';
    }
}
$requestFilters = request()->only(['tahun_filter', 'bulan_filter', 'substansi_filter', 'jenis_regulasi_filter']);
@endphp

@section('header_filters')
    <form method="GET" action="{{ route('sekretariat-jenderal.jumlah-regulasi-baru.index') }}" class="w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 items-end">
            <div class="flex-grow">
                <label for="tahun_filter_regulasi" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                <select name="tahun_filter" id="tahun_filter_regulasi" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ request('tahun_filter') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow">
                <label for="bulan_filter_regulasi" class="text-sm text-gray-600 whitespace-nowrap">Bulan:</label>
                <select name="bulan_filter" id="bulan_filter_regulasi" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan_filter') == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex-grow">
                <label for="substansi_filter_regulasi" class="text-sm text-gray-600 whitespace-nowrap">Substansi:</label>
                 <select name="substansi_filter" id="substansi_filter_regulasi" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua Substansi</option>
                    @foreach($substansiOptions as $key => $value)
                        <option value="{{ $key }}" {{ request('substansi_filter') == $key ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow">
                <label for="jenis_regulasi_filter_regulasi" class="text-sm text-gray-600 whitespace-nowrap">Jenis Regulasi:</label>
                 <select name="jenis_regulasi_filter" id="jenis_regulasi_filter_regulasi" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua Jenis</option>
                    @foreach($jenisRegulasiOptions as $key => $value)
                        <option value="{{ $key }}" {{ request('jenis_regulasi_filter') == $key ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center space-x-2">
                @if(request()->filled('sort_by')) <input type="hidden" name="sort_by" value="{{ request('sort_by') }}"> @endif
                @if(request()->filled('sort_direction')) <input type="hidden" name="sort_direction" value="{{ request('sort_direction') }}"> @endif
                <button type="submit" class="btn-primary">
                    <i class="ri-filter-3-line mr-1"></i> Filter
                </button>
                <a href="{{ route('sekretariat-jenderal.jumlah-regulasi-baru.index') }}" class="w-full sm:w-auto px-4 py-1.5 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
                    Reset
                </a>
            </div>
        </div>
    </form>
@endsection

@section('content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-md">
    {{-- Menyamakan tata letak tombol dengan modul lain --}}
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <div></div> {{-- Untuk alignment tombol ke kanan --}}
        <div class="w-full sm:w-auto sm:ml-auto flex flex-col sm:flex-row items-start sm:items-center gap-2">
            <form action="{{ route('sekretariat-jenderal.jumlah-regulasi-baru.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                @csrf
                <div class="flex-grow">
                    <input type="file" name="excel_file" id="excel_file_regulasi" required
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
             <a href="MASUKKAN_LINK_ONEDRIVE_FORMAT_REGULASI_DISINI" {{-- Sesuaikan link format Excel --}}
               target="_blank"
               class="btn-primary">
                <i class="ri-download-2-line mr-1"></i> Unduh Format
            </a>
            <a href="{{ route('sekretariat-jenderal.jumlah-regulasi-baru.create') }}" class="btn-primary">
                <i class="ri-add-line mr-1"></i> Tambah Regulasi
            </a>
        </div>
    </div>

    {{-- Notifikasi session('success') dan session('error') ditangani oleh layouts.app.blade.php --}}
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
    {{-- Jika notifikasi global di layouts/app.blade.php sudah ada, baris di bawah ini bisa dihapus --}}
    {{-- @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-700 rounded-md text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error') && !session('import_errors'))
        <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-md text-sm">
            {{ session('error') }}
        </div>
    @endif --}}

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkRegulasi('tahun', 'Tahun', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkRegulasi('bulan', 'Bulan', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkRegulasi('substansi', 'Substansi', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkRegulasi('jenis_regulasi', 'Jenis Regulasi', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkRegulasi('jumlah_regulasi', 'Jumlah', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($jumlahRegulasiBarus as $index => $item)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $jumlahRegulasiBarus->firstItem() + $index }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->tahun }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ \Carbon\Carbon::create()->month($item->bulan)->isoFormat('MMMM') }}</td>
                        {{-- Menampilkan teks deskriptif menggunakan accessor dari model --}}
                        <td class="px-4 py-3 text-sm text-gray-700 max-w-xs truncate" title="{{ $item->substansi_text }}">{{ Str::limit($item->substansi_text, 50) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 max-w-xs truncate" title="{{ $item->jenis_regulasi_text }}">{{ Str::limit($item->jenis_regulasi_text, 50) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 text-right">{{ number_format($item->jumlah_regulasi) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex items-center justify-center space-x-2">
                                {{-- Tombol Lihat ditambahkan --}}
                           
                                <a href="{{ route('sekretariat-jenderal.jumlah-regulasi-baru.edit', $item->id) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                                    <i class="ri-pencil-line text-base"></i>
                                </a>
                                <form action="{{ route('sekretariat-jenderal.jumlah-regulasi-baru.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data regulasi ini?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                        <i class="ri-delete-bin-line text-base"></i>
                                    </button>
                                </form>
                                <!-- <a href="{{ route('sekretariat-jenderal.jumlah-regulasi-baru.show', $item->id) }}" class="text-gray-500 hover:text-gray-700" title="Lihat">
                                    <i class="ri-eye-line text-base"></i>
                                </a> -->
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-500"> {{-- Colspan disesuaikan --}}
                            <div class="flex flex-col items-center">
                                <i class="ri-inbox-2-line text-4xl text-gray-400 mb-2"></i>
                                Tidak ada data regulasi baru ditemukan.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">
        {{ $jumlahRegulasiBarus->appends(request()->except('page'))->links('vendor.pagination.tailwind') }}
    </div>
</div>
@endsection