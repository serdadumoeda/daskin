@extends('layouts.app')

@section('title', 'Tambah Data Persetujuan RPTKA')
@section('page_title', 'Tambah Data Persetujuan RPTKA')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Tambah Data Persetujuan RPTKA</h2>
        {{-- $routeNamePrefix dilewatkan dari controller --}}
        <a href="{{ route($routeNamePrefix . 'index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route($routeNamePrefix . 'store') }}" method="POST">
        {{-- 
            Variabel $options yang dilewatkan dari PersetujuanRptkaController 
            sudah tidak mengandung 'lapanganUsahaKbliOptions' dan 'provinsiPenempatanOptions'.
            File _form.blade.php juga sudah diubah untuk menggunakan input teks untuk kedua field tersebut.
        --}}
        @include('persetujuan_rptka._form', [
            'persetujuanRptka' => $persetujuanRptka,
            'options' => $options, 
            'routeNamePrefix' => $routeNamePrefix
        ])
    </form>
</div>
@endsection