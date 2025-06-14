@extends('layouts.app')

@section('title', 'Tambah Data Ketenagakerjaan')
@section('page_title', 'Tambah Data Ketenagakerjaan')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Tambah Data Ketenagakerjaan</h2>
        <a href="{{ route('barenbang.data-ketenagakerjaan.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('barenbang.data-ketenagakerjaan.store') }}" method="POST">
        @include('data_ketenagakerjaan._form', ['dataKetenagakerjaan' => $dataKetenagakerjaan])
    </form>
</div>
@endsection
