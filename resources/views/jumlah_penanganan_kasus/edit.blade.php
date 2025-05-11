@extends('layouts.app')

@section('title', 'Edit Jumlah Penanganan Kasus')
@section('page_title', 'Edit Data Jumlah Penanganan Kasus')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
     <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Edit Data Kasus</h2>
        <a href="{{ route('sekretariat-jenderal.jumlah-penanganan-kasus.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('sekretariat-jenderal.jumlah-penanganan-kasus.update', $jumlahPenangananKasus->id) }}" method="POST">
        @method('PUT')
        @include('jumlah_penanganan_kasus._form', ['jumlahPenangananKasus' => $jumlahPenangananKasus, 'satuanKerjas' => $satuanKerjas])
    </form>
</div>
@endsection
