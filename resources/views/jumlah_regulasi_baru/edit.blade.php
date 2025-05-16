@extends('layouts.app')

@section('title', 'Edit Jumlah Regulasi Baru')
@section('page_title')
    Edit Data Jumlah Regulasi Baru: {{ $jumlahRegulasiBaru->tahun }} - {{ \Carbon\Carbon::create()->month($jumlahRegulasiBaru->bulan)->isoFormat('MMMM') }} - {{ $jumlahRegulasiBaru->substansi_text }}
@endsection

@section('content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-md">
    <h3 class="text-xl font-semibold text-gray-800 mb-6">Formulir Perubahan Regulasi Baru</h3>

    <form action="{{ route('sekretariat-jenderal.jumlah-regulasi-baru.update', $jumlahRegulasiBaru->id) }}" method="POST">
        @method('PUT')
        @include('jumlah_regulasi_baru._form')
    </form>
</div>
@endsection