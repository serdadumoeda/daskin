@extends('layouts.app')

@section('title', 'Tambah Jumlah Regulasi Baru')
@section('page_title', 'Tambah Data Jumlah Regulasi Baru')

@section('content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-md">
    <h3 class="text-xl font-semibold text-gray-800 mb-6">Formulir Penambahan Regulasi Baru</h3>
    
    <form action="{{ route('sekretariat-jenderal.jumlah-regulasi-baru.store') }}" method="POST">
        @include('jumlah_regulasi_baru._form')
    </form>
</div>
@endsection