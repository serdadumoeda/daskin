@extends('layouts.app')

@section('title', 'Tambah Jumlah Sertifikasi Kompetensi')
@section('page_title', 'Tambah Data Jumlah Sertifikasi Kompetensi')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Tambah Data Sertifikasi</h2>
        <a href="{{ route('binalavotas.jumlah-sertifikasi-kompetensi.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('binalavotas.jumlah-sertifikasi-kompetensi.store') }}" method="POST">
        @include('jumlah_sertifikasi_kompetensi._form', [
            'jumlahSertifikasiKompetensi' => $jumlahSertifikasiKompetensi,
            'jenisLspOptions' => $jenisLspOptions,
            'jenisKelaminOptions' => $jenisKelaminOptions
        ])
    </form>
</div>
@endsection
