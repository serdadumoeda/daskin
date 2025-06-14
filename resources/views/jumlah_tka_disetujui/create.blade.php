@extends('layouts.app')

@section('title', 'Tambah Jumlah TKA Disetujui')
@section('page_title', 'Tambah Data Jumlah TKA Disetujui')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Tambah Data TKA Disetujui</h2>
        <a href="{{ route('binapenta.jumlah-tka-disetujui.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('binapenta.jumlah-tka-disetujui.store') }}" method="POST">
        @include('jumlah_tka_disetujui._form', [
            'jumlahTkaDisetujui' => $jumlahTkaDisetujui,
            'jenisKelaminOptions' => $jenisKelaminOptions
        ])
    </form>
</div>
@endsection
