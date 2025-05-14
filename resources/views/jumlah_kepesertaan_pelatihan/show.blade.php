@extends('layouts.app')

@section('title', 'Detail Jumlah Kepesertaan Pelatihan')
@section('page_title', 'Detail Data Jumlah Kepesertaan Pelatihan')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Data Kepesertaan</h2>
        <a href="{{ route('binalavotas.jumlah-kepesertaan-pelatihan.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-3 text-sm">
        <div><strong class="text-gray-600 w-48 inline-block">Tahun:</strong> <span class="text-gray-800">{{ $jumlahKepesertaanPelatihan->tahun }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Bulan:</strong> <span class="text-gray-800">{{ \Carbon\Carbon::create()->month($jumlahKepesertaanPelatihan->bulan)->isoFormat('MMMM') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Penyelenggara Pelatihan:</strong> <span class="text-gray-800">{{ $jumlahKepesertaanPelatihan->penyelenggara_pelatihan_text }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Tipe Lembaga:</strong> <span class="text-gray-800">{{ $jumlahKepesertaanPelatihan->tipe_lembaga_text }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jenis Kelamin Peserta:</strong> <span class="text-gray-800">{{ $jumlahKepesertaanPelatihan->jenis_kelamin_text }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Provinsi Tempat Pelatihan:</strong> <span class="text-gray-800">{{ $jumlahKepesertaanPelatihan->provinsi_tempat_pelatihan }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Kejuruan:</strong> <span class="text-gray-800">{{ $jumlahKepesertaanPelatihan->kejuruan }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Status Kelulusan:</strong> <span class="text-gray-800">{{ $jumlahKepesertaanPelatihan->status_kelulusan_text }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jumlah Peserta:</strong> <span class="text-gray-800">{{ number_format($jumlahKepesertaanPelatihan->jumlah) }}</span></div>
        
        <div class="border-t pt-3 mt-3"></div>
        <div><strong class="text-gray-600 w-48 inline-block">Dibuat pada:</strong> <span class="text-gray-800">{{ $jumlahKepesertaanPelatihan->created_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Diperbarui pada:</strong> <span class="text-gray-800">{{ $jumlahKepesertaanPelatihan->updated_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route('binalavotas.jumlah-kepesertaan-pelatihan.edit', $jumlahKepesertaanPelatihan->id) }}" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection
