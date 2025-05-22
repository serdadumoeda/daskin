@extends('layouts.app')

@section('title', 'Tambah Data Indikator Kinerja Pelaksanaan Anggaran')
@section('page_title', 'Tambah Data Indikator Kinerja Pelaksanaan Anggaran')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Tambah Data Indikator Kinerja Pelaksanaan Anggaran</h2>
        <a href="{{ route('sekretariat-jenderal.ikpa.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('sekretariat-jenderal.ikpa.store') }}" method="POST">
        @include('ikpa._form', [
            'indikatorKinerjaPelaksanaanAnggaran' => $indikatorKinerjaPelaksanaanAnggaran,
            'unitKerjaEselonIs' => $unitKerjaEselonIs,
            'aspekPelaksanaanAnggaranOptions' => $aspekPelaksanaanAnggaranOptions
        ])
    </form>
</div>
@endsection
