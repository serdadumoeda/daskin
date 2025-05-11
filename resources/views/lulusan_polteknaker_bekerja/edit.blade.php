@extends('layouts.app')

@section('title', 'Edit Data Lulusan Polteknaker Bekerja')
@section('page_title', 'Edit Data Lulusan Polteknaker Bekerja')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
     <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Edit Data Lulusan</h2>
        <a href="{{ route('sekretariat-jenderal.lulusan-polteknaker-bekerja.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('sekretariat-jenderal.lulusan-polteknaker-bekerja.update', $lulusanPolteknakerBekerja->id) }}" method="POST">
        @method('PUT')
        @include('lulusan_polteknaker_bekerja._form', [
            'lulusanPolteknakerBekerja' => $lulusanPolteknakerBekerja,
            'programStudiOptions' => $programStudiOptions
            ])
    </form>
</div>
@endsection
