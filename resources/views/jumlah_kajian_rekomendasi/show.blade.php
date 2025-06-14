@extends('layouts.app')

@section('title', 'Detail Jumlah Kajian dan Rekomendasi')
@section('page_title', 'Detail Data Jumlah Kajian dan Rekomendasi')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Data</h2>
        <a href="{{ route('barenbang.jumlah-kajian-rekomendasi.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-3 text-sm">
        <div><strong class="text-gray-600 w-48 inline-block">Tahun:</strong> <span class="text-gray-800">{{ $jumlahKajianRekomendasi->tahun }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Bulan:</strong> <span class="text-gray-800">{{ \Carbon\Carbon::create()->month($jumlahKajianRekomendasi->bulan)->isoFormat('MMMM') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Substansi:</strong> <span class="text-gray-800">{{ $jumlahKajianRekomendasi->substansi_text }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jenis Output:</strong> <span class="text-gray-800">{{ $jumlahKajianRekomendasi->jenis_output_text }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jumlah:</strong> <span class="text-gray-800">{{ number_format($jumlahKajianRekomendasi->jumlah) }}</span></div>
        
        <div class="border-t pt-3 mt-3"></div>
        <div><strong class="text-gray-600 w-48 inline-block">Dibuat pada:</strong> <span class="text-gray-800">{{ $jumlahKajianRekomendasi->created_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Diperbarui pada:</strong> <span class="text-gray-800">{{ $jumlahKajianRekomendasi->updated_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route('barenbang.jumlah-kajian-rekomendasi.edit', $jumlahKajianRekomendasi->id) }}" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection
