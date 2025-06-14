@extends('layouts.app')

{{-- Menyesuaikan judul halaman --}}
@section('title', 'Tambah Data Penanganan Kasus')
@section('page_title', 'Tambah Data Jumlah Penanganan Kasus')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        {{-- Menyamakan judul form --}}
        <h2 class="text-xl font-semibold text-gray-800">Formulir Tambah Data Penanganan Kasus</h2>
        <a href="{{ route('sekretariat-jenderal.jumlah-penanganan-kasus.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('sekretariat-jenderal.jumlah-penanganan-kasus.store') }}" method="POST">
        @include('jumlah_penanganan_kasus._form', ['jumlahPenangananKasus' => $jumlahPenangananKasus])
    </form>
</div>
@endsection