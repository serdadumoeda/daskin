@extends('layouts.app')

@section('title', 'Edit Jumlah Kajian dan Rekomendasi')
@section('page_title', 'Edit Data Jumlah Kajian dan Rekomendasi')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
     <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Edit Data</h2>
        <a href="{{ route('barenbang.jumlah-kajian-rekomendasi.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('barenbang.jumlah-kajian-rekomendasi.update', $jumlahKajianRekomendasi->id) }}" method="POST">
        @method('PUT')
        @include('jumlah_kajian_rekomendasi._form', [
            'jumlahKajianRekomendasi' => $jumlahKajianRekomendasi,
            'substansiOptions' => $substansiOptions,
            'jenisOutputOptions' => $jenisOutputOptions
        ])
    </form>
</div>
@endsection
