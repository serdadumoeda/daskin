@extends('layouts.app')

@section('title', 'Detail Data Ketenagakerjaan')
@section('page_title', 'Detail Data Ketenagakerjaan')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Data</h2>
        <a href="{{ route('barenbang.data-ketenagakerjaan.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-3 text-sm">
        <div><strong class="text-gray-600 w-60 inline-block">Tahun:</strong> <span class="text-gray-800">{{ $dataKetenagakerjaan->tahun }}</span></div>
        <div><strong class="text-gray-600 w-60 inline-block">Bulan:</strong> <span class="text-gray-800">{{ \Carbon\Carbon::create()->month($dataKetenagakerjaan->bulan)->isoFormat('MMMM') }}</span></div>
        
        <div class="pt-2 font-medium">Data Utama (dalam Ribu Jiwa):</div>
        <div><strong class="text-gray-600 w-60 inline-block ml-4">Penduduk 15 Thn Ke Atas:</strong> <span class="text-gray-800">{{ number_format($dataKetenagakerjaan->penduduk_15_atas, 3, ',', '.') }}</span></div>
        <div><strong class="text-gray-600 w-60 inline-block ml-4">Angkatan Kerja:</strong> <span class="text-gray-800">{{ number_format($dataKetenagakerjaan->angkatan_kerja, 3, ',', '.') }}</span></div>
        <div><strong class="text-gray-600 w-60 inline-block ml-4">Bukan Angkatan Kerja:</strong> <span class="text-gray-800">{{ number_format($dataKetenagakerjaan->bukan_angkatan_kerja, 3, ',', '.') }}</span></div>
        
        <div class="pt-2 font-medium">Detail Bukan Angkatan Kerja (dalam Ribu Jiwa):</div>
        <div><strong class="text-gray-600 w-60 inline-block ml-4">Sekolah:</strong> <span class="text-gray-800">{{ number_format($dataKetenagakerjaan->sekolah, 3, ',', '.') }}</span></div>
        <div><strong class="text-gray-600 w-60 inline-block ml-4">Mengurus Rumah Tangga:</strong> <span class="text-gray-800">{{ number_format($dataKetenagakerjaan->mengurus_rumah_tangga, 3, ',', '.') }}</span></div>
        <div><strong class="text-gray-600 w-60 inline-block ml-4">Lainnya (Bukan AK):</strong> <span class="text-gray-800">{{ number_format($dataKetenagakerjaan->lainnya_bak, 3, ',', '.') }}</span></div>

        <div class="pt-2 font-medium">Indikator Ketenagakerjaan:</div>
        <div><strong class="text-gray-600 w-60 inline-block ml-4">TPAK (%):</strong> <span class="text-gray-800">{{ number_format($dataKetenagakerjaan->tpak, 2, ',', '.') }}%</span></div>
        <div><strong class="text-gray-600 w-60 inline-block ml-4">Bekerja (Ribu Jiwa):</strong> <span class="text-gray-800">{{ number_format($dataKetenagakerjaan->bekerja, 3, ',', '.') }}</span></div>
        <div><strong class="text-gray-600 w-60 inline-block ml-4">Pengangguran Terbuka (Ribu Jiwa):</strong> <span class="text-gray-800">{{ number_format($dataKetenagakerjaan->pengangguran_terbuka, 3, ',', '.') }}</span></div>
        <div><strong class="text-gray-600 w-60 inline-block ml-4">TPT (%):</strong> <span class="text-gray-800">{{ number_format($dataKetenagakerjaan->tpt, 2, ',', '.') }}%</span></div>
        <div><strong class="text-gray-600 w-60 inline-block ml-4">Tingkat Kesempatan Kerja (%):</strong> <span class="text-gray-800">{{ number_format($dataKetenagakerjaan->tingkat_kesempatan_kerja, 2, ',', '.') }}%</span></div>
        
        <div class="border-t pt-3 mt-3"></div>
        <div><strong class="text-gray-600 w-48 inline-block">Dibuat pada:</strong> <span class="text-gray-800">{{ $dataKetenagakerjaan->created_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Diperbarui pada:</strong> <span class="text-gray-800">{{ $dataKetenagakerjaan->updated_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route('barenbang.data-ketenagakerjaan.edit', $dataKetenagakerjaan->id) }}" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection
