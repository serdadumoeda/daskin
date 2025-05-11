@extends('layouts.app')

@section('title', 'Tambah Data Mediasi Berhasil')
@section('page_title', 'Tambah Data Mediasi Berhasil')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Tambah Data Mediasi</h2>
        <a href="{{ route('phi.mediasi-berhasil.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('phi.mediasi-berhasil.store') }}" method="POST">
        @include('mediasi_berhasil._form', [
            'mediasiBerhasil' => $mediasiBerhasil,
            'jenisPerselisihanOptions' => $jenisPerselisihanOptions,
            'hasilMediasiOptions' => $hasilMediasiOptions
        ])
    </form>
</div>
@endsection
