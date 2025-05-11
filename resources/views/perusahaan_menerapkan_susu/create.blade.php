@extends('layouts.app')

@section('title', 'Tambah Data Perusahaan Menerapkan SUSU')
@section('page_title', 'Tambah Data Perusahaan Menerapkan SUSU')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Tambah Data SUSU</h2>
        <a href="{{ route('phi.perusahaan-menerapkan-susu.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('phi.perusahaan-menerapkan-susu.store') }}" method="POST">
        @include('perusahaan_menerapkan_susu._form', ['perusahaanMenerapkanSusu' => $perusahaanMenerapkanSusu])
    </form>
</div>
@endsection
