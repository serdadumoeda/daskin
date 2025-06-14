@extends('layouts.app')

@section('title', 'Detail Penyelesaian BMN')
@section('page_title', 'Detail Data Penyelesaian BMN')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Penyelesaian BMN</h2>
        <a href="{{ route('sekretariat-jenderal.penyelesaian-bmn.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-4 text-sm">
        <div>
            <strong class="text-gray-600 w-48 inline-block">Tahun:</strong> 
            <span class="text-gray-800">{{ $penyelesaianBmn->tahun }}</span>
        </div>
        <div>
            <strong class="text-gray-600 w-48 inline-block">Bulan:</strong> 
            <span class="text-gray-800">{{ \Carbon\Carbon::create()->month($penyelesaianBmn->bulan)->isoFormat('MMMM') }}</span>
        </div>
        {{-- Menampilkan nama satuan kerja dari relasi --}}
        <div class="border-t pt-4 mt-4">
            <strong class="text-gray-600 w-full block mb-1">Unit/Satuan Kerja:</strong> 
            <p class="text-gray-800">{{ $penyelesaianBmn->satuanKerja->nama_satuan_kerja ?? $penyelesaianBmn->kode_satuan_kerja }}</p>
        </div>
        <div class="border-t pt-4 mt-4">
            <strong class="text-gray-600 w-full block mb-1">Jenis BMN:</strong> 
            <p class="text-gray-800">{{ $penyelesaianBmn->jenis_bmn_text }}</p>
        </div>
        <div>
            <strong class="text-gray-600 w-48 inline-block">Henti Guna:</strong> 
            <span class="text-gray-800">{{ $penyelesaianBmn->henti_guna_text }}</span>
        </div>
        <div class="border-t pt-4 mt-4">
            <strong class="text-gray-600 w-full block mb-1">Status Penggunaan:</strong> 
            <p class="text-gray-800">{{ $penyelesaianBmn->status_penggunaan_text }}</p>
        </div>
        <div class="border-t pt-4 mt-4">
            <strong class="text-gray-600 w-full block mb-1">Penetapan Status Penggunaan:</strong> 
            <p class="text-gray-800">{{ $penyelesaianBmn->penetapan_status_penggunaan ?? '-' }}</p>
        </div>
        <div>
            <strong class="text-gray-600 w-48 inline-block">Kuantitas:</strong> 
            <span class="text-gray-800">{{ number_format($penyelesaianBmn->kuantitas) }}</span>
        </div>
        <div>
            <strong class="text-gray-600 w-48 inline-block">Nilai Aset (Rp):</strong> 
            <span class="text-gray-800">{{ number_format($penyelesaianBmn->nilai_aset, 2, ',', '.') }}</span>
        </div>
        
        <div class="border-t pt-4 mt-4"></div>
        <div>
            <strong class="text-gray-600 w-48 inline-block">Dibuat pada:</strong> 
            <span class="text-gray-800">{{ $penyelesaianBmn->created_at->isoFormat('D MMMM YYYY, HH:mm') }}</span>
        </div>
        <div>
            <strong class="text-gray-600 w-48 inline-block">Diperbarui pada:</strong> 
            <span class="text-gray-800">{{ $penyelesaianBmn->updated_at->isoFormat('D MMMM YYYY, HH:mm') }}</span>
        </div>
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route('sekretariat-jenderal.penyelesaian-bmn.edit', $penyelesaianBmn->id) }}" 
           class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium flex items-center">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection