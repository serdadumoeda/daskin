@extends('layouts.app')

@section('title', 'Detail Jumlah Penempatan oleh Kemnaker')
@section('page_title', 'Detail Data Jumlah Penempatan')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Data Penempatan</h2>
        <a href="{{ route('binapenta.jumlah-penempatan-kemnaker.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-3 text-sm">
        <div><strong class="text-gray-600 w-48 inline-block">Tahun:</strong> <span class="text-gray-800">{{ $jumlahPenempatanKemnaker->tahun }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Bulan:</strong> <span class="text-gray-800">{{ \Carbon\Carbon::create()->month($jumlahPenempatanKemnaker->bulan)->isoFormat('MMMM') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jenis Kelamin:</strong> <span class="text-gray-800">{{ $jumlahPenempatanKemnaker->jenis_kelamin_text }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Provinsi Domisili:</strong> <span class="text-gray-800">{{ $jumlahPenempatanKemnaker->provinsi_domisili }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Lapangan Usaha (KBLI):</strong> <span class="text-gray-800">{{ $jumlahPenempatanKemnaker->lapangan_usaha_kbli }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Status Disabilitas:</strong> <span class="text-gray-800">{{ $jumlahPenempatanKemnaker->status_disabilitas_text }}</span></div>
        @if($jumlahPenempatanKemnaker->status_disabilitas == 1)
        <div><strong class="text-gray-600 w-48 inline-block">Ragam Disabilitas:</strong> <span class="text-gray-800">{{ $jumlahPenempatanKemnaker->ragam_disabilitas ?? '-' }}</span></div>
        @endif
        <div><strong class="text-gray-600 w-48 inline-block">Jumlah Ditempatkan:</strong> <span class="text-gray-800">{{ number_format($jumlahPenempatanKemnaker->jumlah) }}</span></div>
        
        <div class="border-t pt-3 mt-3"></div>
        <div><strong class="text-gray-600 w-48 inline-block">Dibuat pada:</strong> <span class="text-gray-800">{{ $jumlahPenempatanKemnaker->created_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Diperbarui pada:</strong> <span class="text-gray-800">{{ $jumlahPenempatanKemnaker->updated_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route('binapenta.jumlah-penempatan-kemnaker.edit', $jumlahPenempatanKemnaker->id) }}" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection
