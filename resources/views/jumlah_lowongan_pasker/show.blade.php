@extends('layouts.app')

@section('title', 'Detail Jumlah Lowongan Pasker')
@section('page_title', 'Detail Data Jumlah Lowongan Pasker')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Data Lowongan Pasker</h2>
        <a href="{{ route('binapenta.jumlah-lowongan-pasker.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-3 text-sm">
        <div><strong class="text-gray-600 w-48 inline-block">Tahun:</strong> <span class="text-gray-800">{{ $jumlahLowonganPasker->tahun }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Bulan:</strong> <span class="text-gray-800">{{ \Carbon\Carbon::create()->month($jumlahLowonganPasker->bulan)->isoFormat('MMMM') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Provinsi Perusahaan:</strong> <span class="text-gray-800">{{ $jumlahLowonganPasker->provinsi_perusahaan }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Lapangan Usaha (KBLI):</strong> <span class="text-gray-800">{{ $jumlahLowonganPasker->lapangan_usaha_kbli }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jabatan:</strong> <span class="text-gray-800">{{ $jumlahLowonganPasker->jabatan }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jenis Kelamin Dibutuhkan:</strong> <span class="text-gray-800">{{ $jumlahLowonganPasker->jenis_kelamin_dibutuhkan_text }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Status Disabilitas Dibutuhkan:</strong> <span class="text-gray-800">{{ $jumlahLowonganPasker->status_disabilitas_dibutuhkan_text }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jumlah Lowongan:</strong> <span class="text-gray-800">{{ number_format($jumlahLowonganPasker->jumlah_lowongan) }}</span></div>
        
        <div class="border-t pt-3 mt-3"></div>
        <div><strong class="text-gray-600 w-48 inline-block">Dibuat pada:</strong> <span class="text-gray-800">{{ $jumlahLowonganPasker->created_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Diperbarui pada:</strong> <span class="text-gray-800">{{ $jumlahLowonganPasker->updated_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route('binapenta.jumlah-lowongan-pasker.edit', $jumlahLowonganPasker->id) }}" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection
