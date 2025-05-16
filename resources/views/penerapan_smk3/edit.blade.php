@extends('layouts.app')

@section('title', 'Edit Data Penerapan SMK3')
@section('page_title', 'Edit Data Perusahaan yang menerapkan SMK3')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
     <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Edit Penerapan SMK3</h2>
        <a href="{{ route('binwasnaker.penerapan-smk3.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('binwasnaker.penerapan-smk3.update', $penerapanSmk3->id) }}" method="POST">
        @method('PUT')
        @include('penerapan_smk3._form', [
            'penerapanSmk3' => $penerapanSmk3,
            'kategoriPenilaianOptions' => $kategoriPenilaianOptions,
            'tingkatPencapaianOptions' => $tingkatPencapaianOptions,
            'jenisPenghargaanOptions' => $jenisPenghargaanOptions
        ])
    </form>
</div>
@endsection
