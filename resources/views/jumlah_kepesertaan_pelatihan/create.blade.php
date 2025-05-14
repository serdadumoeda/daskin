@extends('layouts.app')

@section('title', 'Tambah Jumlah Kepesertaan Pelatihan')
@section('page_title', 'Tambah Data Jumlah Kepesertaan Pelatihan')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Tambah Data Kepesertaan</h2>
        <a href="{{ route('binalavotas.jumlah-kepesertaan-pelatihan.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('binalavotas.jumlah-kepesertaan-pelatihan.store') }}" method="POST">
        @include('jumlah_kepesertaan_pelatihan._form', [
            'jumlahKepesertaanPelatihan' => $jumlahKepesertaanPelatihan,
            'penyelenggaraOptions' => $penyelenggaraOptions,
            'tipeLembagaOptions' => $tipeLembagaOptions,
            'jenisKelaminOptions' => $jenisKelaminOptions,
            'statusKelulusanOptions' => $statusKelulusanOptions
        ])
    </form>
</div>
@endsection
