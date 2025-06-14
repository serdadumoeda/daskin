@extends('layouts.app')

@section('title', 'Edit Data Jumlah PHK')
@section('page_title', 'Edit Data Jumlah PHK')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
     <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Edit Data PHK</h2>
        <a href="{{ route('phi.jumlah-phk.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('phi.jumlah-phk.update', $jumlahPhk->id) }}" method="POST">
        @method('PUT')
        @include('jumlah_phk._form', ['jumlahPhk' => $jumlahPhk])
    </form>
</div>
@endsection
