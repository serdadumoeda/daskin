@extends('layouts.app')

@section('title', 'Detail Penerapan SMK3')
@section('page_title', 'Detail Data Penerapan SMK3')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Penerapan SMK3</h2>
        <a href="{{ route('binwasnaker.penerapan-smk3.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-3 text-sm">
        <div><strong class="text-gray-600 w-48 inline-block">Tahun:</strong> <span class="text-gray-800">{{ $penerapanSmk3->tahun }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Bulan:</strong> <span class="text-gray-800">{{ \Carbon\Carbon::create()->month($penerapanSmk3->bulan)->isoFormat('MMMM') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Provinsi:</strong> <span class="text-gray-800">{{ $penerapanSmk3->provinsi }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">KBLI:</strong> <span class="text-gray-800">{{ $penerapanSmk3->kbli }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Kategori Penilaian:</strong> <span class="text-gray-800">{{ $penerapanSmk3->kategori_penilaian }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Tingkat Pencapaian:</strong> <span class="text-gray-800">{{ $penerapanSmk3->tingkat_pencapaian }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jenis Penghargaan:</strong> <span class="text-gray-800">{{ $penerapanSmk3->jenis_penghargaan }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jumlah Perusahaan:</strong> <span class="text-gray-800">{{ number_format($penerapanSmk3->jumlah_perusahaan) }}</span></div>
        
        <div class="border-t pt-3 mt-3"></div>
        <div><strong class="text-gray-600 w-48 inline-block">Dibuat pada:</strong> <span class="text-gray-800">{{ $penerapanSmk3->created_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Diperbarui pada:</strong> <span class="text-gray-800">{{ $penerapanSmk3->updated_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route('binwasnaker.penerapan-smk3.edit', $penerapanSmk3->id) }}" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection
