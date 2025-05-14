@extends('layouts.app')

@section('title', 'Edit Jumlah Lowongan Pasker')
@section('page_title', 'Edit Data Jumlah Lowongan Pasker')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
     <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Edit Lowongan Pasker</h2>
        <a href="{{ route('binapenta.jumlah-lowongan-pasker.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('binapenta.jumlah-lowongan-pasker.update', $jumlahLowonganPasker->id) }}" method="POST">
        @method('PUT')
        @include('jumlah_lowongan_pasker._form', [
            'jumlahLowonganPasker' => $jumlahLowonganPasker,
            'jenisKelaminDibutuhkanOptions' => $jenisKelaminDibutuhkanOptions,
            'statusDisabilitasDibutuhkanOptions' => $statusDisabilitasDibutuhkanOptions
        ])
    </form>
</div>
@endsection
