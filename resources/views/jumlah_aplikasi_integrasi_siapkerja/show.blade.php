@extends('layouts.app')

@section('title', 'Detail Aplikasi Terintegrasi SiapKerja')
@section('page_title', 'Detail Data Aplikasi Terintegrasi SiapKerja')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Data Aplikasi</h2>
        <a href="{{ route('barenbang.aplikasi-integrasi-siapkerja.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-3 text-sm">
        <div><strong class="text-gray-600 w-48 inline-block">Tahun:</strong> <span class="text-gray-800">{{ $aplikasiIntegrasiSiapkerja->tahun }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Bulan:</strong> <span class="text-gray-800">{{ \Carbon\Carbon::create()->month($aplikasiIntegrasiSiapkerja->bulan)->isoFormat('MMMM') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jenis Instansi:</strong> <span class="text-gray-800">{{ $aplikasiIntegrasiSiapkerja->jenis_instansi_text }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Nama Instansi:</strong> <span class="text-gray-800">{{ $aplikasiIntegrasiSiapkerja->nama_instansi }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Nama Aplikasi/Website:</strong> <span class="text-gray-800">{{ $aplikasiIntegrasiSiapkerja->nama_aplikasi_website }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Status Integrasi:</strong> <span class="text-gray-800">{{ $aplikasiIntegrasiSiapkerja->status_integrasi_text }}</span></div>
        
        <div class="border-t pt-3 mt-3"></div>
        <div><strong class="text-gray-600 w-48 inline-block">Dibuat pada:</strong> <span class="text-gray-800">{{ $aplikasiIntegrasiSiapkerja->created_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Diperbarui pada:</strong> <span class="text-gray-800">{{ $aplikasiIntegrasiSiapkerja->updated_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route('barenbang.aplikasi-integrasi-siapkerja.edit', $aplikasiIntegrasiSiapkerja->id) }}" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection
