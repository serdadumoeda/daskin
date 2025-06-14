@extends('layouts.app')

@section('title', 'Daftar Jumlah Lowongan Pasker')
@section('page_title', 'Manajemen Jumlah Lowongan Pekerjaan Baru di Pasker')

@php
if (!function_exists('sortableLinkLowongan')) {
    function sortableLinkLowongan(string $column, string $label, string $currentSortBy, string $currentSortDirection, array $requestFilters, string $routeNamePrefixFunc) {
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
        return '<a href="' . route($routeNamePrefixFunc . 'index', $queryParams) . '" class="flex items-center hover:text-primary">' . e($label) . $iconHtml . '</a>';
    }
}
$requestFilters = request()->only(['tahun_filter', 'bulan_filter', 'jenis_kelamin_filter', 'provinsi_penempatan_filter', 'lapangan_usaha_kbli_filter', 'status_disabilitas_filter']);
@endphp

@section('header_filters')
    <form method="GET" action="{{ route($routeNamePrefix . 'index') }}" class="w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 items-end">
            <div>
                <label for="tahun_filter_lowongan" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                <select name="tahun_filter" id="tahun_filter_lowongan" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ request('tahun_filter') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="bulan_filter_lowongan" class="text-sm text-gray-600 whitespace-nowrap">Bulan:</label>
                <select name="bulan_filter" id="bulan_filter_lowongan" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan_filter') == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="jenis_kelamin_filter_lowongan" class="text-sm text-gray-600 whitespace-nowrap">Jenis Kelamin:</label>
                <select name="jenis_kelamin_filter" id="jenis_kelamin_filter_lowongan" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @foreach($options['jenisKelaminOptions'] as $key => $value)
                        <option value="{{ $key }}" {{ request('jenis_kelamin_filter') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="provinsi_penempatan_filter_lowongan" class="text-sm text-gray-600 whitespace-nowrap">Prov. Penempatan:</label>
                <input type="text" name="provinsi_penempatan_filter" id="provinsi_penempatan_filter_lowongan" 
                       value="{{ request('provinsi_penempatan_filter') }}" placeholder="Cari provinsi..." 
                       class="form-input mt-1 w-full bg-white">
            </div>
            <div class="lg:col-span-1">
                <label for="lapangan_usaha_kbli_filter_lowongan" class="text-sm text-gray-600 whitespace-nowrap">Lap. Usaha (KBLI):</label>
                <input type="text" name="lapangan_usaha_kbli_filter" id="lapangan_usaha_kbli_filter_lowongan" 
                       value="{{ request('lapangan_usaha_kbli_filter') }}" placeholder="Cari KBLI..." 
                       class="form-input mt-1 w-full bg-white">
            </div>
            <div class="lg:col-span-1">
                <label for="status_disabilitas_filter_lowongan" class="text-sm text-gray-600 whitespace-nowrap">Status Disabilitas:</label>
                <select name="status_disabilitas_filter" id="status_disabilitas_filter_lowongan" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @foreach($options['statusDisabilitasOptions'] as $key => $value)
                        <option value="{{ $key }}" {{ request('status_disabilitas_filter') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center space-x-2 self-end col-span-full sm:col-span-auto mt-3">
                @if(request()->filled('sort_by')) <input type="hidden" name="sort_by" value="{{ request('sort_by') }}"> @endif
                @if(request()->filled('sort_direction')) <input type="hidden" name="sort_direction" value="{{ request('sort_direction') }}"> @endif
                <button type="submit" class="btn-primary">
                    <i class="ri-filter-3-line mr-1"></i> Filter
                </button>
                <a href="{{ route($routeNamePrefix . 'index') }}" class="w-full sm:w-auto px-4 py-1.5 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
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
            <form action="{{ route($routeNamePrefix . 'import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                @csrf
                <div class="flex-grow">
                    <input type="file" name="excel_file" id="excel_file_lowongan" required
                           class="block w-full text-sm text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-button file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 form-input p-0.5 h-full border border-gray-300">
                </div>
                <button type="submit" class="btn-primary">
                    <i class="ri-upload-2-line mr-1"></i> Impor Data
                </button>
            </form>
             <a href="MASUKKAN_LINK_FORMAT_EXCEL_LOWONGAN_DISINI" target="_blank"
               class="btn-primary">
                <i class="ri-download-2-line mr-1"></i> Unduh Format
            </a>
            <a href="{{ route($routeNamePrefix . 'create') }}" class="btn-primary">
                <i class="ri-add-line mr-1"></i> Tambah Data Lowongan
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

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkLowongan('tahun', 'Tahun', $sortBy, $sortDirection, $requestFilters, $routeNamePrefix) !!}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkLowongan('bulan', 'Bulan', $sortBy, $sortDirection, $requestFilters, $routeNamePrefix) !!}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkLowongan('jenis_kelamin', 'Jenis Kelamin', $sortBy, $sortDirection, $requestFilters, $routeNamePrefix) !!}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkLowongan('provinsi_penempatan', 'Prov. Penempatan', $sortBy, $sortDirection, $requestFilters, $routeNamePrefix) !!}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkLowongan('lapangan_usaha_kbli', 'Lap. Usaha (KBLI)', $sortBy, $sortDirection, $requestFilters, $routeNamePrefix) !!}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkLowongan('status_disabilitas', 'Status Disabilitas', $sortBy, $sortDirection, $requestFilters, $routeNamePrefix) !!}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkLowongan('jumlah_lowongan', 'Jumlah', $sortBy, $sortDirection, $requestFilters, $routeNamePrefix) !!}</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($jumlahLowonganPaskers as $index => $item)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $jumlahLowonganPaskers->firstItem() + $index }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->tahun }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ \Carbon\Carbon::create()->month($item->bulan)->isoFormat('MMMM') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->jenis_kelamin_text }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 max-w-[150px] truncate" title="{{ $item->provinsi_penempatan }}">{{ Str::limit($item->provinsi_penempatan, 20) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 max-w-[200px] truncate" title="{{ $item->lapangan_usaha_kbli }}">{{ Str::limit($item->lapangan_usaha_kbli, 30) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->status_disabilitas_text }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 text-right">{{ number_format($item->jumlah_lowongan) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex items-center justify-center space-x-1">
                                <!-- <a href="{{ route($routeNamePrefix . 'show', $item->id) }}" class="text-gray-500 hover:text-gray-700 p-1" title="Lihat">
                                    <i class="ri-eye-line text-base"></i>
                                </a> -->
                                <a href="{{ route($routeNamePrefix . 'edit', $item->id) }}" class="text-blue-600 hover:text-blue-800 p-1" title="Edit">
                                    <i class="ri-pencil-line text-base"></i>
                                </a>
                                <form action="{{ route($routeNamePrefix . 'destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 p-1" title="Hapus">
                                        <i class="ri-delete-bin-line text-base"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-10 text-center text-sm text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="ri-inbox-2-line text-4xl text-gray-400 mb-2"></i>
                                Tidak ada data lowongan Pasker ditemukan.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">
        {{ $jumlahLowonganPaskers->appends(request()->except('page'))->links('vendor.pagination.tailwind') }}
    </div>
</div>
@endsection