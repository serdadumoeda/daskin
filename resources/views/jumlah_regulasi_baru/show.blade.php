@extends('layouts.app')

@section('title', 'Detail Jumlah Regulasi Baru')
@section('page_title', 'Detail Data Jumlah Regulasi Baru')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Regulasi Baru</h2>
        <a href="{{ route('sekretariat-jenderal.jumlah-regulasi-baru.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-4 text-sm">
        <div><strong class="text-gray-600 w-48 inline-block">Tahun:</strong> <span class="text-gray-800">{{ $jumlahRegulasiBaru->tahun }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Bulan Data Diinput:</strong> <span class="text-gray-800">{{ \Carbon\Carbon::create()->month($jumlahRegulasiBaru->bulan)->isoFormat('MMMM') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Satuan Kerja:</strong> <span class="text-gray-800">{{ $jumlahRegulasiBaru->satuanKerja->nama_satuan_kerja ?? $jumlahRegulasiBaru->kode_satuan_kerja }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jenis Regulasi:</strong> <span class="text-gray-800">{{ $jumlahRegulasiBaru->jenis_regulasi_text }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jumlah Regulasi:</strong> <span class="text-gray-800">{{ number_format($jumlahRegulasiBaru->jumlah_regulasi) }}</span></div>
        
        <div class="border-t pt-4 mt-4"></div>
        <div><strong class="text-gray-600 w-48 inline-block">Dibuat pada:</strong> <span class="text-gray-800">{{ $jumlahRegulasiBaru->created_at->isoFormat('D MMMM YYYY, HH:mm') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Diperbarui pada:</strong> <span class="text-gray-800">{{ $jumlahRegulasiBaru->updated_at->isoFormat('D MMMM YYYY, HH:mm') }}</span></div>
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route('sekretariat-jenderal.jumlah-regulasi-baru.edit', $jumlahRegulasiBaru->id) }}" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection
