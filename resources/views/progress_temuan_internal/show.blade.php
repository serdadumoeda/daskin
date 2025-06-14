@extends('layouts.app')

@section('title', 'Detail Progres Temuan Internal')
@section('page_title', 'Detail Data Progres Temuan Internal')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Data</h2>
        <a href="{{ route('inspektorat.progress-temuan-internal.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-sm">
        <div><strong class="text-gray-600 w-52 inline-block">Tahun:</strong> <span class="text-gray-800">{{ $progressTemuanInternal->tahun }}</span></div>
        <div><strong class="text-gray-600 w-52 inline-block">Bulan:</strong> <span class="text-gray-800">{{ \Carbon\Carbon::create()->month($progressTemuanInternal->bulan)->isoFormat('MMMM') }}</span></div>
        <div><strong class="text-gray-600 w-52 inline-block">Unit Kerja Eselon I:</strong> <span class="text-gray-800">{{ $progressTemuanInternal->unitKerjaEselonI->nama_unit_kerja_eselon_i ?? '-' }}</span></div>
        <div><strong class="text-gray-600 w-52 inline-block">Satuan Kerja:</strong> <span class="text-gray-800">{{ $progressTemuanInternal->satuanKerja->nama_satuan_kerja ?? '-' }}</span></div>
        
        <div class="md:col-span-2 mt-4 pt-4 border-t border-gray-200">
            <h3 class="text-md font-semibold text-gray-700 mb-2">Detail Temuan</h3>
        </div>
        <div><strong class="text-gray-600 w-52 inline-block">Temuan Administratif (Kasus):</strong> <span class="text-gray-800">{{ number_format($progressTemuanInternal->temuan_administratif_kasus) }}</span></div>
        <div><strong class="text-gray-600 w-52 inline-block">Temuan Kerugian Negara (Rp):</strong> <span class="text-gray-800">{{ number_format($progressTemuanInternal->temuan_kerugian_negara_rp, 2, ',', '.') }}</span></div>
        
        <div class="md:col-span-2 mt-4 pt-4 border-t border-gray-200">
            <h3 class="text-md font-semibold text-gray-700 mb-2">Detail Tindak Lanjut</h3>
        </div>
        <div><strong class="text-gray-600 w-52 inline-block">Tindak Lanjut Administratif (Kasus):</strong> <span class="text-gray-800">{{ number_format($progressTemuanInternal->tindak_lanjut_administratif_kasus) }}</span></div>
        <div><strong class="text-gray-600 w-52 inline-block">Tindak Lanjut Kerugian Negara (Rp):</strong> <span class="text-gray-800">{{ number_format($progressTemuanInternal->tindak_lanjut_kerugian_negara_rp, 2, ',', '.') }}</span></div>

        <div class="md:col-span-2 mt-4 pt-4 border-t border-gray-200">
            <h3 class="text-md font-semibold text-gray-700 mb-2">Persentase Penyelesaian</h3>
        </div>
        <div><strong class="text-gray-600 w-52 inline-block">Persentase Tindak Lanjut Administratif:</strong> <span class="text-gray-800">{{ number_format($progressTemuanInternal->persentase_tindak_lanjut_administratif, 2) }}%</span></div>
        <div><strong class="text-gray-600 w-52 inline-block">Persentase Tindak Lanjut Kerugian Negara:</strong> <span class="text-gray-800">{{ number_format($progressTemuanInternal->persentase_tindak_lanjut_kerugian_negara, 2) }}%</span></div>

        <div class="md:col-span-2 mt-4 pt-4 border-t border-gray-200"></div>
        <div><strong class="text-gray-600 w-52 inline-block">Dibuat pada:</strong> <span class="text-gray-800">{{ $progressTemuanInternal->created_at->isoFormat('D MMMM YYYY, HH:mm') }}</span></div>
        <div><strong class="text-gray-600 w-52 inline-block">Diperbarui pada:</strong> <span class="text-gray-800">{{ $progressTemuanInternal->updated_at->isoFormat('D MMMM YYYY, HH:mm') }}</span></div>
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route('inspektorat.progress-temuan-internal.edit', $progressTemuanInternal->id) }}" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection
