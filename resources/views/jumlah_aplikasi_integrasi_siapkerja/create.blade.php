@extends('layouts.app')

@section('title', 'Tambah Aplikasi Terintegrasi SiapKerja')
@section('page_title', 'Tambah Data Aplikasi Terintegrasi SiapKerja')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Tambah Data Aplikasi</h2>
        <a href="{{ route('barenbang.aplikasi-integrasi-siapkerja.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('barenbang.aplikasi-integrasi-siapkerja.store') }}" method="POST">
        @include('jumlah_aplikasi_integrasi_siapkerja._form', [
            'aplikasiIntegrasiSiapkerja' => $aplikasiIntegrasiSiapkerja,
            'jenisInstansiOptions' => $jenisInstansiOptions,
            'statusIntegrasiOptions' => $statusIntegrasiOptions
        ])
    </form>
</div>
@endsection
