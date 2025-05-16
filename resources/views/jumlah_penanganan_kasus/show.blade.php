@extends('layouts.app')

{{-- Menyesuaikan judul halaman --}}
@section('title', 'Detail Penanganan Kasus')
@section('page_title', 'Detail Data Jumlah Penanganan Kasus')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        
        <h2 class="text-xl font-semibold text-gray-800">Detail Penanganan Kasus</h2>
        <a href="{{ route('sekretariat-jenderal.jumlah-penanganan-kasus.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    
    <div class="space-y-4 text-sm">
        <div>
            <strong class="text-gray-600 w-48 inline-block">Tahun:</strong> 
            <span class="text-gray-800">{{ $jumlahPenangananKasus->tahun }}</span>
        </div>
        <div>
            <strong class="text-gray-600 w-48 inline-block">Bulan:</strong> 
            <span class="text-gray-800">{{ \Carbon\Carbon::create()->month($jumlahPenangananKasus->bulan)->isoFormat('MMMM') }}</span>
        </div>
        <div class="border-t pt-4 mt-4">
            <strong class="text-gray-600 w-full block mb-1">Substansi:</strong> 
            <p class="text-gray-800">{{ $jumlahPenangananKasus->substansi }}</p>
        </div>
        <div class="border-t pt-4 mt-4">
            <strong class="text-gray-600 w-full block mb-1">Jenis Perkara:</strong> 
            <p class="text-gray-800">{{ $jumlahPenangananKasus->jenis_perkara }}</p>
        </div>
        <div>
            <strong class="text-gray-600 w-48 inline-block">Jumlah Perkara:</strong> 
            <span class="text-gray-800">{{ number_format($jumlahPenangananKasus->jumlah_perkara) }}</span>
        </div>
        
        <div class="border-t pt-4 mt-4"></div>
        <div>
            <strong class="text-gray-600 w-48 inline-block">Dibuat pada:</strong> 
            <span class="text-gray-800">{{ $jumlahPenangananKasus->created_at->isoFormat('D MMMM YYYY, HH:mm') }}</span>
        </div>
        <div>
            <strong class="text-gray-600 w-48 inline-block">Diperbarui pada:</strong> 
            <span class="text-gray-800">{{ $jumlahPenangananKasus->updated_at->isoFormat('D MMMM YYYY, HH:mm') }}</span>
        </div>
    </div>

    
    <div class="mt-8 flex justify-end">
        <a href="{{ route('sekretariat-jenderal.jumlah-penanganan-kasus.edit', $jumlahPenangananKasus->id) }}" 
           class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium flex items-center">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
        {{-- Tombol Delete bisa diletakkan di halaman index saja untuk konsistensi dengan MoU,
             atau jika tetap diinginkan di sini, stylingnya bisa disesuaikan.
             Untuk saat ini, saya hilangkan agar lebih mirip MoU show page.
        <form action="{{ route('sekretariat-jenderal.jumlah-penanganan-kasus.destroy', $jumlahPenangananKasus->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kasus ini?');" class="ml-3">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="px-4 py-2 bg-red-600 text-white rounded-button hover:bg-red-700 text-sm font-medium flex items-center">
                <i class="ri-delete-bin-line mr-1"></i> Hapus
            </button>
        </form>
        --}}
    </div>
</div>
@endsection