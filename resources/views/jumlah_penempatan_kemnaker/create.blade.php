@extends('layouts.app')

@section('title', 'Tambah Jumlah Penempatan oleh Kemnaker')
@section('page_title', 'Tambah Data Jumlah Penempatan')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Tambah Data Penempatan</h2>
        <a href="{{ route('binapenta.jumlah-penempatan-kemnaker.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('binapenta.jumlah-penempatan-kemnaker.store') }}" method="POST">
        @include('jumlah_penempatan_kemnaker._form', [
            'jumlahPenempatanKemnaker' => $jumlahPenempatanKemnaker,
            'jenisKelaminOptions' => $jenisKelaminOptions,
            'statusDisabilitasOptions' => $statusDisabilitasOptions,
            'ragamDisabilitasOptions' => $ragamDisabilitasOptions
        ])
    </form>
</div>
@endsection
