@extends('layouts.app')

@section('title', 'Tambah Data Persentase Kehadiran')
@section('page_title', 'Tambah Data Persentase Kehadiran')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Tambah Data Kehadiran</h2>
        <a href="{{ route('sekretariat-jenderal.persentase-kehadiran.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('sekretariat-jenderal.persentase-kehadiran.store') }}" method="POST">
        @include('persentase_kehadiran._form', [
            'persentaseKehadiran' => $persentaseKehadiran,
            'unitKerjaEselonIs' => $unitKerjaEselonIs, 
            'statusAsnOptions' => $statusAsnOptions,
            'statusKehadiranOptions' => $statusKehadiranOptions
        ])
    </form>
</div>
@endsection
