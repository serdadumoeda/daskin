@extends('layouts.app')

@section('title', 'Detail Jumlah Lowongan Pasker')
@section('page_title', 'Detail Data Jumlah Lowongan Pasker')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Lowongan Pasker</h2>
        <a href="{{ route($routeNamePrefix . 'index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-4 text-sm">
        <div>
            <strong class="text-gray-600 w-48 inline-block">Tahun:</strong> 
            <span class="text-gray-800">{{ $jumlahLowonganPasker->tahun }}</span>
        </div>
        <div>
            <strong class="text-gray-600 w-48 inline-block">Bulan:</strong> 
            <span class="text-gray-800">{{ \Carbon\Carbon::create()->month($jumlahLowonganPasker->bulan)->isoFormat('MMMM') }}</span>
        </div>
        <div>
            <strong class="text-gray-600 w-48 inline-block">Jenis Kelamin:</strong> 
            <span class="text-gray-800">{{ $jumlahLowonganPasker->jenis_kelamin_text }}</span>
        </div>
        <div class="border-t pt-4 mt-4">
            <strong class="text-gray-600 w-full block mb-1">Provinsi Penempatan:</strong> 
            <p class="text-gray-800">{{ $jumlahLowonganPasker->provinsi_penempatan }}</p>
        </div>
        <div class="border-t pt-4 mt-4">
            <strong class="text-gray-600 w-full block mb-1">Lapangan Usaha (KBLI):</strong> 
            <p class="text-gray-800">{{ $jumlahLowonganPasker->lapangan_usaha_kbli }}</p>
        </div>
        <div>
            <strong class="text-gray-600 w-48 inline-block">Status Disabilitas:</strong> 
            <span class="text-gray-800">{{ $jumlahLowonganPasker->status_disabilitas_text }}</span>
        </div>
        <div>
            <strong class="text-gray-600 w-48 inline-block">Jumlah Lowongan:</strong> 
            <span class="text-gray-800">{{ number_format($jumlahLowonganPasker->jumlah_lowongan) }}</span>
        </div>
        
        <div class="border-t pt-4 mt-4"></div>
        <div>
            <strong class="text-gray-600 w-48 inline-block">Dibuat pada:</strong> 
            <span class="text-gray-800">{{ $jumlahLowonganPasker->created_at->isoFormat('D MMMM HH:mm', 'Do MMMM GGGG, HH:mm') }}</span>
        </div>
        <div>
            <strong class="text-gray-600 w-48 inline-block">Diperbarui pada:</strong> 
            <span class="text-gray-800">{{ $jumlahLowonganPasker->updated_at->isoFormat('D MMMM HH:mm', 'Do MMMM GGGG, HH:mm') }}</span>
        </div>
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route($routeNamePrefix . 'edit', $jumlahLowonganPasker->id) }}" 
           class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium flex items-center">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection