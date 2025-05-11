@extends('layouts.app')

@section('title', 'Detail SDM Mengikuti Pelatihan')
@section('page_title', 'Detail Data SDM Mengikuti Pelatihan')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Data Pelatihan SDM</h2>
        <a href="{{ route('sekretariat-jenderal.sdm-mengikuti-pelatihan.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-3 text-sm">
        <div><strong class="text-gray-600 w-48 inline-block">Tahun:</strong> <span class="text-gray-800">{{ $sdmMengikutiPelatihan->tahun }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Bulan:</strong> <span class="text-gray-800">{{ \Carbon\Carbon::create()->month($sdmMengikutiPelatihan->bulan)->isoFormat('MMMM') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Unit Kerja Eselon I:</strong> <span class="text-gray-800">{{ $sdmMengikutiPelatihan->unitKerjaEselonI->nama_unit_kerja_eselon_i ?? $sdmMengikutiPelatihan->kode_unit_kerja_eselon_i }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Satuan Kerja:</strong> <span class="text-gray-800">{{ $sdmMengikutiPelatihan->satuanKerja->nama_satuan_kerja ?? $sdmMengikutiPelatihan->kode_satuan_kerja }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jenis Pelatihan:</strong> <span class="text-gray-800">{{ $sdmMengikutiPelatihan->jenis_pelatihan_text }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jumlah Peserta:</strong> <span class="text-gray-800">{{ number_format($sdmMengikutiPelatihan->jumlah_peserta) }}</span></div>
        
        <div class="border-t pt-3 mt-3"></div>
        <div><strong class="text-gray-600 w-48 inline-block">Dibuat pada:</strong> <span class="text-gray-800">{{ $sdmMengikutiPelatihan->created_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Diperbarui pada:</strong> <span class="text-gray-800">{{ $sdmMengikutiPelatihan->updated_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route('sekretariat-jenderal.sdm-mengikuti-pelatihan.edit', $sdmMengikutiPelatihan->id) }}" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection
