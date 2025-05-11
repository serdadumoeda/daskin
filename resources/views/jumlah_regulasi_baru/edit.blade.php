@extends('layouts.app')

@section('title', 'Edit Jumlah Regulasi Baru')
@section('page_title', 'Edit Data Jumlah Regulasi Baru')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
     <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Edit Regulasi Baru</h2>
        <a href="{{ route('sekretariat-jenderal.jumlah-regulasi-baru.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('sekretariat-jenderal.jumlah-regulasi-baru.update', $jumlahRegulasiBaru->id) }}" method="POST">
        @method('PUT')
        @include('jumlah_regulasi_baru._form', ['jumlahRegulasiBaru' => $jumlahRegulasiBaru, 'satuanKerjas' => $satuanKerjas, 'jenisRegulasiOptions' => $jenisRegulasiOptions])
    </form>
</div>
@endsection
