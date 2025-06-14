@extends('layouts.app')

@section('title', 'Tambah Progres Temuan BPK')
@section('page_title', 'Tambah Data Progres Temuan BPK')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Tambah Data</h2>
        <a href="{{ route('inspektorat.progress-temuan-bpk.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('inspektorat.progress-temuan-bpk.store') }}" method="POST">
        {{-- Variabel $progressTemuanBpk dan lainnya dikirim dari controller create() method --}}
        @include('progress_temuan_bpk._form', ['progressTemuanBpk' => $progressTemuanBpk, 'unitKerjaEselonIs' => $unitKerjaEselonIs, 'satuanKerjas' => $satuanKerjas])
    </form>
</div>
@endsection
