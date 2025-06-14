@extends('layouts.app')

@section('title', 'Detail MoU')
@section('page_title', 'Detail Data MoU')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail MoU</h2>
        <a href="{{ route('sekretariat-jenderal.progress-mou.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-4 text-sm">
        <div><strong class="text-gray-600 w-48 inline-block">Tahun:</strong> <span class="text-gray-800">{{ $progressMou->tahun }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Bulan Data Diinput:</strong> <span class="text-gray-800">{{ \Carbon\Carbon::create()->month($progressMou->bulan)->isoFormat('MMMM') }}</span></div>
        <div class="border-t pt-4 mt-4"><strong class="text-gray-600 w-full block mb-1">Judul MoU:</strong> <p class="text-gray-800">{{ $progressMou->judul_mou }}</p></div>
        <div><strong class="text-gray-600 w-48 inline-block">Tanggal Mulai Perjanjian:</strong> <span class="text-gray-800">{{ $progressMou->tanggal_mulai_perjanjian ? \Carbon\Carbon::parse($progressMou->tanggal_mulai_perjanjian)->isoFormat('D MMMM YYYY') : '-' }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Tanggal Selesai Perjanjian:</strong> <span class="text-gray-800">{{ $progressMou->tanggal_selesai_perjanjian ? \Carbon\Carbon::parse($progressMou->tanggal_selesai_perjanjian)->isoFormat('D MMMM YYYY') : '-' }}</span></div>
        <div class="border-t pt-4 mt-4"><strong class="text-gray-600 w-full block mb-1">Pihak Terlibat:</strong> <p class="text-gray-800">{{ $progressMou->pihak_terlibat ?? '-' }}</p></div>
        
        <div class="border-t pt-4 mt-4"></div>
        <div><strong class="text-gray-600 w-48 inline-block">Dibuat pada:</strong> <span class="text-gray-800">{{ $progressMou->created_at->isoFormat('D MMMM YYYY, HH:mm') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Diperbarui pada:</strong> <span class="text-gray-800">{{ $progressMou->updated_at->isoFormat('D MMMM YYYY, HH:mm') }}</span></div>
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route('sekretariat-jenderal.progress-mou.edit', $progressMou->id) }}" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection
