@extends('layouts.app')

@section('title', 'Tambah Jumlah Lowongan Pasker')
@section('page_title', 'Tambah Data Jumlah Lowongan Pasker')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Tambah Data Lowongan Pasker</h2>
        <a href="{{ route($routeNamePrefix . 'index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route($routeNamePrefix . 'store') }}" method="POST">
        @include('jumlah_lowongan_pasker._form', [
            'jumlahLowonganPasker' => $jumlahLowonganPasker,
            'options' => $options, 
            'routeNamePrefix' => $routeNamePrefix
        ])
    </form>
</div>
@endsection