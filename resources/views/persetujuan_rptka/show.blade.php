@extends('layouts.app')

@section('title', 'Detail Persetujuan RPTKA')
@section('page_title', 'Detail Data Persetujuan RPTKA')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Persetujuan RPTKA</h2>
        <a href="{{ route($routeNamePrefix . 'index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-4 text-sm">
        <div>
            <strong class="text-gray-600 w-48 inline-block">Tahun:</strong> 
            <span class="text-gray-800">{{ $persetujuanRptka->tahun }}</span>
        </div>
        <div>
            <strong class="text-gray-600 w-48 inline-block">Bulan:</strong> 
            <span class="text-gray-800">{{ \Carbon\Carbon::create()->month($persetujuanRptka->bulan)->isoFormat('MMMM') }}</span>
        </div>
        <div>
            <strong class="text-gray-600 w-48 inline-block">Jenis Kelamin:</strong> 
            <span class="text-gray-800">{{ $persetujuanRptka->jenis_kelamin_text }}</span>
        </div>
        <div class="border-t pt-4 mt-4">
            <strong class="text-gray-600 w-full block mb-1">Negara Asal:</strong> 
            <p class="text-gray-800">{{ $persetujuanRptka->negara_asal }}</p>
        </div>
        <div class="border-t pt-4 mt-4">
            <strong class="text-gray-600 w-full block mb-1">Jabatan:</strong> 
            <p class="text-gray-800">{{ $persetujuanRptka->jabatan_text }}</p>
        </div>
        {{-- Menampilkan lapangan_usaha_kbli sebagai string --}}
        <div class="border-t pt-4 mt-4">
            <strong class="text-gray-600 w-full block mb-1">Lapangan Usaha (KBLI):</strong> 
            <p class="text-gray-800">{{ $persetujuanRptka->lapangan_usaha_kbli }}</p>
        </div>
        <div class="border-t pt-4 mt-4">
            <strong class="text-gray-600 w-full block mb-1">Provinsi Penempatan:</strong> 
            <p class="text-gray-800">{{ $persetujuanRptka->provinsi_penempatan }}</p>
        </div>
        <div>
            <strong class="text-gray-600 w-48 inline-block">Status Pengajuan RPTKA:</strong> 
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $persetujuanRptka->status_pengajuan == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $persetujuanRptka->status_pengajuan_text }}
            </span>
        </div>
        <div>
            <strong class="text-gray-600 w-48 inline-block">Jumlah:</strong> 
            <span class="text-gray-800">{{ number_format($persetujuanRptka->jumlah) }}</span>
        </div>
        
        <div class="border-t pt-4 mt-4"></div>
        <div>
            <strong class="text-gray-600 w-48 inline-block">Dibuat pada:</strong> 
            <span class="text-gray-800">{{ $persetujuanRptka->created_at->isoFormat('D MMMM HH:mm', 'Do MMMM GGGG, HH:mm') }}</span>
        </div>
        <div>
            <strong class="text-gray-600 w-48 inline-block">Diperbarui pada:</strong> 
            <span class="text-gray-800">{{ $persetujuanRptka->updated_at->isoFormat('D MMMM HH:mm', 'Do MMMM GGGG, HH:mm') }}</span>
        </div>
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route($routeNamePrefix . 'edit', $persetujuanRptka->id) }}" 
           class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium flex items-center">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection