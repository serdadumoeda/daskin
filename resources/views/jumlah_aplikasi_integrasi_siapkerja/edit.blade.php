@extends('layouts.app')

@section('title', 'Edit Aplikasi Terintegrasi SiapKerja')
@section('page_title', 'Edit Data Aplikasi Terintegrasi SiapKerja')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
     <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Edit Data Aplikasi</h2>
        <a href="{{ route('barenbang.aplikasi-integrasi-siapkerja.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('barenbang.aplikasi-integrasi-siapkerja.update', $aplikasiIntegrasiSiapkerja->id) }}" method="POST">
        @method('PUT')
        @include('jumlah_aplikasi_integrasi_siapkerja._form', [
            'aplikasiIntegrasiSiapkerja' => $aplikasiIntegrasiSiapkerja,
            'jenisInstansiOptions' => $jenisInstansiOptions,
            'statusIntegrasiOptions' => $statusIntegrasiOptions
        ])
    </form>
</div>
@endsection
