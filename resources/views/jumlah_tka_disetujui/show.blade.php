@extends('layouts.app')

@section('title', 'Detail Jumlah TKA Disetujui')
@section('page_title', 'Detail Data Jumlah TKA Disetujui')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Data TKA Disetujui</h2>
        <a href="{{ route('binapenta.jumlah-tka-disetujui.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-3 text-sm">
        <div><strong class="text-gray-600 w-48 inline-block">Tahun:</strong> <span class="text-gray-800">{{ $jumlahTkaDisetujui->tahun }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Bulan:</strong> <span class="text-gray-800">{{ \Carbon\Carbon::create()->month($jumlahTkaDisetujui->bulan)->isoFormat('MMMM') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jenis Kelamin:</strong> <span class="text-gray-800">{{ $jumlahTkaDisetujui->jenis_kelamin_text }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Negara Asal:</strong> <span class="text-gray-800">{{ $jumlahTkaDisetujui->negara_asal }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jabatan:</strong> <span class="text-gray-800">{{ $jumlahTkaDisetujui->jabatan }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Lapangan Usaha (KBLI):</strong> <span class="text-gray-800">{{ $jumlahTkaDisetujui->lapangan_usaha_kbli }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Provinsi Penempatan:</strong> <span class="text-gray-800">{{ $jumlahTkaDisetujui->provinsi_penempatan }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jumlah TKA Disetujui:</strong> <span class="text-gray-800">{{ number_format($jumlahTkaDisetujui->jumlah_tka) }}</span></div>
        
        <div class="border-t pt-3 mt-3"></div>
        <div><strong class="text-gray-600 w-48 inline-block">Dibuat pada:</strong> <span class="text-gray-800">{{ $jumlahTkaDisetujui->created_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Diperbarui pada:</strong> <span class="text-gray-800">{{ $jumlahTkaDisetujui->updated_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route('binapenta.jumlah-tka-disetujui.edit', $jumlahTkaDisetujui->id) }}" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection
