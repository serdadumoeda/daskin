@extends('layouts.app')

@section('title', 'Tambah Pengaduan Pelanggaran Norma')
@section('page_title', 'Tambah Data Pengaduan Pelanggaran Norma')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Tambah Pengaduan</h2>
        <a href="{{ route('binwasnaker.pengaduan-pelanggaran-norma.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('binwasnaker.pengaduan-pelanggaran-norma.store') }}" method="POST">
        @include('pengaduan_pelanggaran_norma._form', ['pengaduanPelanggaranNorma' => $pengaduanPelanggaranNorma])
    </form>
</div>
@endsection
