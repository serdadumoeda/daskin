@extends('layouts.app')

@section('title', 'Detail Jumlah PHK')
@section('page_title', 'Detail Data Jumlah PHK')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Data PHK</h2>
        <a href="{{ route('phi.jumlah-phk.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-3 text-sm">
        <div><strong class="text-gray-600 w-48 inline-block">Tahun:</strong> <span class="text-gray-800">{{ $jumlahPhk->tahun }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Bulan:</strong> <span class="text-gray-800">{{ \Carbon\Carbon::create()->month($jumlahPhk->bulan)->isoFormat('MMMM') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Provinsi:</strong> <span class="text-gray-800">{{ $jumlahPhk->provinsi }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jumlah Perusahaan PHK:</strong> <span class="text-gray-800">{{ number_format($jumlahPhk->jumlah_perusahaan_phk) }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jumlah Tenaga Kerja di PHK:</strong> <span class="text-gray-800">{{ number_format($jumlahPhk->jumlah_tk_phk) }}</span></div>

        <div class="border-t pt-3 mt-3"></div>
        <div><strong class="text-gray-600 w-48 inline-block">Dibuat pada:</strong> <span class="text-gray-800">{{ $jumlahPhk->created_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Diperbarui pada:</strong> <span class="text-gray-800">{{ $jumlahPhk->updated_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route('phi.jumlah-phk.edit', $jumlahPhk->id) }}" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection
