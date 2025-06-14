@extends('layouts.app')

@section('title', 'Jumlah TKA Disetujui')
@section('page_title', 'Manajemen Jumlah TKA Disetujui')

@php
// Helper function untuk link sorting (spesifik untuk modul ini)
if (!function_exists('sortableLinkTkaDisetujui')) {
    function sortableLinkTkaDisetujui(string $column, string $label, string $currentSortBy, string $currentSortDirection, array $requestFilters) {
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
        return '<a href="' . route('binapenta.jumlah-tka-disetujui.index', $queryParams) . '" class="flex items-center hover:text-primary">' . e($label) . $iconHtml . '</a>';
    }
}
$requestFilters = request()->only(['tahun_filter', 'bulan_filter', 'provinsi_penempatan_filter', 'kbli_filter', 'jabatan_filter', 'negara_asal_filter', 'jenis_kelamin_filter']);
@endphp

@section('header_filters')
    <form method="GET" action="{{ route('binapenta.jumlah-tka-disetujui.index') }}" class="w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3 items-end">
            <div class="flex-grow">
                <label for="tahun_filter_tka_disetujui" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                <select name="tahun_filter" id="tahun_filter_tka_disetujui" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ request('tahun_filter') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow">
                <label for="bulan_filter_tka_disetujui" class="text-sm text-gray-600 whitespace-nowrap">Bulan:</label>
                <select name="bulan_filter" id="bulan_filter_tka_disetujui" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan_filter') == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex-grow">
                <label for="provinsi_penempatan_filter_tka" class="text-sm text-gray-600 whitespace-nowrap">Provinsi Penempatan:</label>
                <input type="text" name="provinsi_penempatan_filter" id="provinsi_penempatan_filter_tka" value="{{ request('provinsi_penempatan_filter') }}" placeholder="Cari provinsi..." class="form-input mt-1 w-full bg-white">
            </div>
            <div class="flex-grow">
                <label for="kbli_filter_tka" class="text-sm text-gray-600 whitespace-nowrap">Lap. Usaha (KBLI):</label>
                <input type="text" name="kbli_filter" id="kbli_filter_tka" value="{{ request('kbli_filter') }}" placeholder="Cari KBLI..." class="form-input mt-1 w-full bg-white">
            </div>
            <div class="flex-grow">
                <label for="jabatan_filter_tka" class="text-sm text-gray-600 whitespace-nowrap">Jabatan:</label>
                <input type="text" name="jabatan_filter" id="jabatan_filter_tka" value="{{ request('jabatan_filter') }}" placeholder="Cari jabatan..." class="form-input mt-1 w-full bg-white">
            </div>
             <div class="flex-grow">
                <label for="negara_asal_filter_tka" class="text-sm text-gray-600 whitespace-nowrap">Negara Asal:</label>
                <input type="text" name="negara_asal_filter" id="negara_asal_filter_tka" value="{{ request('negara_asal_filter') }}" placeholder="Cari negara..." class="form-input mt-1 w-full bg-white">
            </div>
             <div class="flex-grow">
                <label for="jenis_kelamin_filter_tka" class="text-sm text-gray-600 whitespace-nowrap">Jenis Kelamin:</label>
                 <select name="jenis_kelamin_filter" id="jenis_kelamin_filter_tka" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @foreach($jenisKelaminOptions as $key => $value)
                        <option value="{{ $key }}" {{ request('jenis_kelamin_filter') == $key ? 'selected' : '' }}>
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
                <a href="{{ route('binapenta.jumlah-tka-disetujui.index') }}" class="btn-secondary-outline">
                    Clear Filter
                </a>
            </div>
        </div>
    </form>
@endsection

@section('content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-md">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <div class="w-full sm:w-auto sm:ml-auto flex flex-col sm:flex-row items-start sm:items-center gap-2">
            <form action="{{ route('binapenta.jumlah-tka-disetujui.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                @csrf
                <div class="flex-grow">
                    <input type="file" name="excel_file" id="excel_file_tka_disetujui" required 
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
             <a href="MASUKKAN_LINK_ONEDRIVE_FORMAT_TKA_DISETUJUI_DISINI" 
               target="_blank"
               class="btn-primary">
                <i class="ri-download-2-line mr-1"></i> Unduh Format
            </a>
            <a href="{{ route('binapenta.jumlah-tka-disetujui.create') }}" class="btn-primary">
                <i class="ri-add-line mr-1"></i> Tambah Data TKA
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
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkTkaDisetujui('tahun', 'Tahun', $sortBy, $sortDirection, $requestFilters) !!}</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkTkaDisetujui('bulan', 'Bulan', $sortBy, $sortDirection, $requestFilters) !!}</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkTkaDisetujui('jenis_kelamin', 'Jenis Kelamin', $sortBy, $sortDirection, $requestFilters) !!}</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkTkaDisetujui('negara_asal', 'Negara Asal', $sortBy, $sortDirection, $requestFilters) !!}</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkTkaDisetujui('jabatan', 'Jabatan', $sortBy, $sortDirection, $requestFilters) !!}</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkTkaDisetujui('lapangan_usaha_kbli', 'Lap. Usaha', $sortBy, $sortDirection, $requestFilters) !!}</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkTkaDisetujui('provinsi_penempatan', 'Provinsi Penempatan', $sortBy, $sortDirection, $requestFilters) !!}</th>
                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortableLinkTkaDisetujui('jumlah_tka', 'Jumlah TKA', $sortBy, $sortDirection, $requestFilters) !!}</th>
                    <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($jumlahTkaDisetujuis as $index => $item)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $jumlahTkaDisetujuis->firstItem() + $index }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->tahun }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ \Carbon\Carbon::create()->month($item->bulan)->isoFormat('MMMM') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->jenis_kelamin_text }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->negara_asal }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->jabatan }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->lapangan_usaha_kbli }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->provinsi_penempatan }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 text-right">{{ number_format($item->jumlah_tka) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('binapenta.jumlah-tka-disetujui.edit', $item->id) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                                    <i class="ri-pencil-line text-base"></i>
                                </a>
                                <form action="{{ route('binapenta.jumlah-tka-disetujui.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data TKA disetujui ini?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                        <i class="ri-delete-bin-line text-base"></i>
                                    </button>
                                </form>
                                {{-- <a href="{{ route('binapenta.jumlah-tka-disetujui.show', $item->id) }}" class="text-gray-500 hover:text-gray-700" title="Lihat Detail">
                                    <i class="ri-eye-line text-base"></i>
                                </a> --}}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-4 py-10 text-center text-sm text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="ri-inbox-2-line text-4xl text-gray-400 mb-2"></i>
                                Tidak ada data TKA disetujui ditemukan.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">
        {{ $jumlahTkaDisetujuis->appends(request()->except('page'))->links('vendor.pagination.tailwind') }}
    </div>
</div>
@endsection
