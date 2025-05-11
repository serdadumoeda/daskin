@extends('layouts.app')

@section('title', 'Detail Lulusan Polteknaker Bekerja')
@section('page_title', 'Detail Data Lulusan Polteknaker Bekerja')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Data Lulusan</h2>
        <a href="{{ route('sekretariat-jenderal.lulusan-polteknaker-bekerja.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-3 text-sm">
        <div><strong class="text-gray-600 w-48 inline-block">Tahun:</strong> <span class="text-gray-800">{{ $lulusanPolteknakerBekerja->tahun }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Bulan (Periode Data):</strong> <span class="text-gray-800">{{ \Carbon\Carbon::create()->month($lulusanPolteknakerBekerja->bulan)->isoFormat('MMMM') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Program Studi:</strong> <span class="text-gray-800">{{ $lulusanPolteknakerBekerja->program_studi_text }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jumlah Lulusan:</strong> <span class="text-gray-800">{{ number_format($lulusanPolteknakerBekerja->jumlah_lulusan) }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jumlah Lulusan Bekerja:</strong> <span class="text-gray-800">{{ number_format($lulusanPolteknakerBekerja->jumlah_lulusan_bekerja) }}</span></div>
        
        <div class="border-t pt-3 mt-3"></div>
        <div><strong class="text-gray-600 w-48 inline-block">Dibuat pada:</strong> <span class="text-gray-800">{{ $lulusanPolteknakerBekerja->created_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Diperbarui pada:</strong> <span class="text-gray-800">{{ $lulusanPolteknakerBekerja->updated_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route('sekretariat-jenderal.lulusan-polteknaker-bekerja.edit', $lulusanPolteknakerBekerja->id) }}" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection
