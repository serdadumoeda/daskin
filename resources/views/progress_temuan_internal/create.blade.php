@extends('layouts.app')

@section('title', 'Tambah Progres Temuan Internal')
@section('page_title', 'Tambah Data Progres Temuan Internal')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Tambah Data</h2>
        <a href="{{ route('inspektorat.progress-temuan-internal.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('inspektorat.progress-temuan-internal.store') }}" method="POST">
        @include('progress_temuan_internal._form', ['progressItem' => $progressItem, 'unitKerjaEselonIs' => $unitKerjaEselonIs, 'satuanKerjas' => $satuanKerjas])
    </form>
</div>
@endsection
