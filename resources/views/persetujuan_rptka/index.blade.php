@extends('layouts.app')

@section('title', 'Daftar Persetujuan RPTKA')
@section('page_title', 'Manajemen Persetujuan RPTKA')

@php
if (!function_exists('sortableLinkRptka')) {
    function sortableLinkRptka(string $column, string $label, string $currentSortBy, string $currentSortDirection, array $requestFilters, string $routeNamePrefixFunc) {
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
$requestFilters = request()->only(['tahun_filter', 'bulan_filter', 'jenis_kelamin_filter', 'negara_asal_filter', 'jabatan_filter', 'lapangan_usaha_kbli_filter', 'provinsi_penempatan_filter', 'status_pengajuan_filter']);
@endphp

@section('header_filters')
    <form method="GET" action="{{ route($routeNamePrefix . 'index') }}" class="w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 items-end">
            <div>
                <label for="tahun_filter_rptka" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                <select name="tahun_filter" id="tahun_filter_rptka" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ request('tahun_filter') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="bulan_filter_rptka" class="text-sm text-gray-600 whitespace-nowrap">Bulan:</label>
                <select name="bulan_filter" id="bulan_filter_rptka" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan_filter') == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="jenis_kelamin_filter_rptka" class="text-sm text-gray-600 whitespace-nowrap">Jenis Kelamin:</label>
                <select name="jenis_kelamin_filter" id="jenis_kelamin_filter_rptka" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @foreach($options['jenisKelaminOptions'] as $key => $value)
                        <option value="{{ $key }}" {{ request('jenis_kelamin_filter') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="negara_asal_filter_rptka" class="text-sm text-gray-600 whitespace-nowrap">Negara Asal:</label>
                <input type="text" name="negara_asal_filter" id="negara_asal_filter_rptka" value="{{ request('negara_asal_filter') }}" placeholder="Cari negara..." class="form-input mt-1 w-full bg-white">
            </div>
            <div>
                <label for="jabatan_filter_rptka" class="text-sm text-gray-600 whitespace-nowrap">Jabatan:</label>
                <select name="jabatan_filter" id="jabatan_filter_rptka" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @foreach($options['jabatanOptions'] as $key => $value)
                        <option value="{{ $key }}" {{ request('jabatan_filter') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Filter Lapangan Usaha (KBLI) menjadi input teks --}}
            <div class="lg:col-span-1">
                <label for="lapangan_usaha_kbli_filter_rptka" class="text-sm text-gray-600 whitespace-nowrap">Lap. Usaha (KBLI):</label>
                <input type="text" name="lapangan_usaha_kbli_filter" id="lapangan_usaha_kbli_filter_rptka" 
                       value="{{ request('lapangan_usaha_kbli_filter') }}" placeholder="Cari KBLI..." 
                       class="form-input mt-1 w-full bg-white">
            </div>
            <div class="lg:col-span-1">
                <label for="provinsi_penempatan_filter_rptka" class="text-sm text-gray-600 whitespace-nowrap">Prov. Penempatan:</label>
                <input type="text" name="provinsi_penempatan_filter" id="provinsi_penempatan_filter_rptka" 
                       value="{{ request('provinsi_penempatan_filter') }}" placeholder="Cari provinsi..." 
                       class="form-input mt-1 w-full bg-white">
            </div>
            <div class="lg:col-span-1">
                <label for="status_pengajuan_filter_rptka" class="text-sm text-gray-600 whitespace-nowrap">Status Pengajuan:</label>
                <select name="status_pengajuan_filter" id="status_pengajuan_filter_rptka" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @foreach($options['statusPengajuanOptions'] as $key => $value)
                        <option value="{{ $key }}" {{ request('status_pengajuan_filter') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
             <div class="flex items-center space-x-2 self-end col-span-full sm:col-span-auto lg:col-start-5 xl:col-start-auto">
                @if(request()->filled('sort_by')) <input type="hidden" name="sort_by" value="{{ request('sort_by') }}"> @endif
                @if(request()->filled('sort_direction')) <input type="hidden" name="sort_direction" value="{{ request('sort_direction') }}"> @endif
                <button type="submit" class="btn-primary">
                    <i class="ri-filter-3-line mr-1"></i> Filter
                </button>
                <a href="{{ route($routeNamePrefix . 'index') }}" class="btn-secondary-outline">
                    Clear Filter
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
                    <input type="file" name="excel_file" id="excel_file_rptka" required
                           class="block w-full text-sm text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-button file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 form-input p-0.5 h-full border border-gray-300">
                </div>
                <button type="submit" class="btn-primary">
                    <i class="ri-upload-2-line mr-1"></i> Impor Data
                </button>
            </form>
             <a href="MASUKKAN_LINK_FORMAT_EXCEL_RPTKA_DISINI" target="_blank"
               class="btn-primary">
                <i class="ri-download-2-line mr-1"></i> Unduh Format
            </a>
            <a href="{{ route($routeNamePrefix . 'create') }}" class="btn-primary">
                <i class="ri-add-line mr-1"></i> Tambah Data RPTKA
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
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkRptka('tahun', 'Tahun', $sortBy, $sortDirection, $requestFilters, $routeNamePrefix) !!}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkRptka('bulan', 'Bulan', $sortBy, $sortDirection, $requestFilters, $routeNamePrefix) !!}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkRptka('jenis_kelamin', 'Jenis Kelamin', $sortBy, $sortDirection, $requestFilters, $routeNamePrefix) !!}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkRptka('negara_asal', 'Negara Asal', $sortBy, $sortDirection, $requestFilters, $routeNamePrefix) !!}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkRptka('jabatan', 'Jabatan', $sortBy, $sortDirection, $requestFilters, $routeNamePrefix) !!}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkRptka('lapangan_usaha_kbli', 'Lap. Usaha (KBLI)', $sortBy, $sortDirection, $requestFilters, $routeNamePrefix) !!}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkRptka('provinsi_penempatan', 'Prov. Penempatan', $sortBy, $sortDirection, $requestFilters, $routeNamePrefix) !!}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkRptka('status_pengajuan', 'Status', $sortBy, $sortDirection, $requestFilters, $routeNamePrefix) !!}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkRptka('jumlah', 'Jumlah', $sortBy, $sortDirection, $requestFilters, $routeNamePrefix) !!}</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($persetujuanRptkas as $index => $item)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $persetujuanRptkas->firstItem() + $index }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->tahun }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ \Carbon\Carbon::create()->month($item->bulan)->isoFormat('MMMM') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->jenis_kelamin_text }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->negara_asal }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 max-w-[150px] truncate" title="{{ $item->jabatan_text }}">{{ Str::limit($item->jabatan_text, 20) }}</td>
                        {{-- Menampilkan lapangan_usaha_kbli sebagai string --}}
                        <td class="px-4 py-3 text-sm text-gray-700 max-w-[150px] truncate" title="{{ $item->lapangan_usaha_kbli }}">{{ Str::limit($item->lapangan_usaha_kbli, 20) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 max-w-[150px] truncate" title="{{ $item->provinsi_penempatan }}">{{ Str::limit($item->provinsi_penempatan, 20) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->status_pengajuan == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $item->status_pengajuan_text }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 text-right">{{ number_format($item->jumlah) }}</td>
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
                        <td colspan="11" class="px-4 py-10 text-center text-sm text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="ri-inbox-2-line text-4xl text-gray-400 mb-2"></i>
                                Tidak ada data persetujuan RPTKA ditemukan.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">
        {{ $persetujuanRptkas->appends(request()->except('page'))->links('vendor.pagination.tailwind') }}
    </div>
</div>
@endsection