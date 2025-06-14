@extends('layouts.app')

@section('title', 'Edit Data Ketenagakerjaan')
@section('page_title', 'Edit Data Ketenagakerjaan')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
     <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Edit Data Ketenagakerjaan</h2>
        <a href="{{ route('barenbang.data-ketenagakerjaan.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('barenbang.data-ketenagakerjaan.update', $dataKetenagakerjaan->id) }}" method="POST">
        @method('PUT')
        @include('data_ketenagakerjaan._form', ['dataKetenagakerjaan' => $dataKetenagakerjaan])
    </form>
</div>
@endsection
