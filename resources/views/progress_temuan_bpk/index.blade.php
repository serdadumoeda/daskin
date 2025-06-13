@extends('layouts.app')

@section('title', 'Progres Tindak Lanjut Temuan BPK')
@section('page_title', 'Progres Tindak Lanjut Temuan BPK')

@php
// Helper function untuk link sorting (spesifik untuk modul ini)
if (!function_exists('sortableLinkBpk')) {
    function sortableLinkBpk(string $column, string $label, string $currentSortBy, string $currentSortDirection, array $requestFilters) {
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
        // Gunakan nama route yang benar dan lengkap
        return '<a href="' . route('inspektorat.progress-temuan-bpk.index', $queryParams) . '" class="flex items-center hover:text-primary">' . e($label) . $iconHtml . '</a>';
    }
}
$requestFilters = request()->only(['tahun_filter', 'bulan_filter', 'unit_kerja_filter']);
@endphp

@section('header_filters')
    <form method="GET" action="{{ route('inspektorat.progress-temuan-bpk.index') }}" class="w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 items-end">
            <div class="flex-grow">
                <label for="tahun_filter_bpk" class="text-sm text-gray-600 whitespace-nowrap">Tahun:</label>
                <select name="tahun_filter" id="tahun_filter_bpk" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ request('tahun_filter') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow">
                <label for="bulan_filter_bpk" class="text-sm text-gray-600 whitespace-nowrap">Bulan:</label>
                <select name="bulan_filter" id="bulan_filter_bpk" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan_filter') == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex-grow">
                <label for="unit_kerja_filter_bpk" class="text-sm text-gray-600 whitespace-nowrap">Unit Kerja:</label>
                 <select name="unit_kerja_filter" id="unit_kerja_filter_bpk" class="form-input mt-1 w-full bg-white">
                    <option value="">Semua</option>
                    @foreach($unitKerjaEselonIs as $unit)
                        <option value="{{ $unit->kode_uke1 }}" {{ request('unit_kerja_filter') == $unit->kode_uke1 ? 'selected' : '' }}>
                            {{ $unit->nama_unit_kerja_eselon_i }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center space-x-2">
                @if(request()->filled('sort_by')) <input type="hidden" name="sort_by" value="{{ request('sort_by') }}"> @endif
                @if(request()->filled('sort_direction')) <input type="hidden" name="sort_direction" value="{{ request('sort_direction') }}"> @endif
                <button type="submit" class="w-full sm:w-auto px-4 py-1.5 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
                    <i class="ri-filter-3-line mr-1"></i> Filter
                </button>
                <a href="{{ route('inspektorat.progress-temuan-bpk.index') }}" class="w-full sm:w-auto px-4 py-1.5 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
                    Reset
                </a>
            </div>
        </div>
    </form>
@endsection

@section('content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-md">
    @if (Auth::user()->role === 'superadmin' || Auth::user()->role === 'itjen')
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            {{-- Judul sudah di header utama, jadi ini bisa dihilangkan atau disesuaikan --}}
            {{-- <h2 class="text-xl font-semibold text-gray-800 whitespace-nowrap">Daftar Progres Temuan BPK</h2> --}}
            <div class="w-full sm:w-auto sm:ml-auto flex flex-col sm:flex-row items-start sm:items-center gap-2">
                <form action="{{ route('inspektorat.progress-temuan-bpk.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                    @csrf
                    <div class="flex-grow">
                        <input type="file" name="excel_file" id="excel_file_bpk" required
                               class="block w-full text-sm text-gray-500
                                      file:mr-2 file:py-1.5 file:px-3 file:rounded-button
                                      file:border-0 file:text-sm file:font-semibold
                                      file:bg-green-50 file:text-green-700
                                      hover:file:bg-green-100 form-input p-0.5 h-full border border-gray-300">
                    </div>
                    <button type="submit" class="px-3 py-2 bg-green-600 text-white rounded-button hover:bg-green-700 text-sm font-medium flex items-center justify-center whitespace-nowrap">
                        <i class="ri-upload-2-line mr-1"></i> Impor Data
                    </button>
                </form>
                 <a href="{{route('inspektorat.progress-temuan-bpk.downloadTemplate')}}"
                   target="_blank"
                   class="px-3 py-2 bg-blue-500 text-white rounded-button hover:bg-blue-600 text-sm font-medium flex items-center justify-center whitespace-nowrap w-full sm:w-auto mt-2 sm:mt-0">
                    <i class="ri-download-2-line mr-1"></i> Unduh Format
                </a>
                <a href="{{ route('inspektorat.progress-temuan-bpk.create') }}" class="w-full sm:w-auto flex items-center justify-center px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium whitespace-nowrap mt-2 sm:mt-0">
                    <i class="ri-add-line mr-1"></i> Tambah Manual
                </a>
            </div>
        </div>
    @endif

    @if (session('import_errors'))
        <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-md text-sm">
            <strong class="font-bold">Beberapa data gagal diimpor karena kesalahan validasi:</strong>
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
                        {!! sortableLinkBpk('tahun', 'Tahun', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkBpk('bulan', 'Bulan', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Kerja</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan Kerja</th>
                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkBpk('temuan_administratif_kasus', 'Temuan Adm. (Kasus)', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkBpk('temuan_kerugian_negara_rp', 'Temuan Kerugian (Rp)', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkBpk('tindak_lanjut_administratif_kasus', 'TL Adm. (Kasus)', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkBpk('tindak_lanjut_kerugian_negara_rp', 'TL Kerugian (Rp)', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkBpk('persentase_tindak_lanjut_administratif', '% TL Adm.', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {!! sortableLinkBpk('persentase_tindak_lanjut_kerugian_negara', '% TL Kerugian', $sortBy, $sortDirection, $requestFilters) !!}
                    </th>
                    <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($progressTemuanBpks as $index => $item)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $progressTemuanBpks->firstItem() + $index }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->tahun }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ \Carbon\Carbon::create()->month($item->bulan)->isoFormat('MMMM') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->unitKerjaEselonI->nama_unit_kerja_eselon_i ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item->satuanKerja->nama_satuan_kerja ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 text-right">{{ number_format($item->temuan_administratif_kasus, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 text-right">{{ number_format($item->temuan_kerugian_negara_rp, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 text-right">{{ number_format($item->tindak_lanjut_administratif_kasus, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 text-right">{{ number_format($item->tindak_lanjut_kerugian_negara_rp, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 text-right">{{ number_format($item->persentase_tindak_lanjut_administratif, 2, ',', '.') }}%</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 text-right">{{ number_format($item->persentase_tindak_lanjut_kerugian_negara, 2, ',', '.') }}%</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('inspektorat.progress-temuan-bpk.edit', $item->id) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                                    <i class="ri-pencil-line text-base"></i>
                                </a>
                                <form action="{{ route('inspektorat.progress-temuan-bpk.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                        <i class="ri-delete-bin-line text-base"></i>
                                    </button>
                                </form>
                                 <a href="{{ route('inspektorat.progress-temuan-bpk.show', $item->id) }}" class="text-gray-500 hover:text-gray-700" title="Lihat Detail">
                                    <i class="ri-eye-line text-base"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="px-4 py-10 text-center text-sm text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="ri-inbox-2-line text-4xl text-gray-400 mb-2"></i>
                                Tidak ada data ditemukan.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">
        {{ $progressTemuanBpks->appends(request()->except('page'))->links('vendor.pagination.tailwind') }}
    </div>
</div>
@endsection
