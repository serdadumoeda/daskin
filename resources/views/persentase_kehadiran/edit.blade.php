@extends('layouts.app')

@section('title', 'Edit Data Persentase Kehadiran')
@section('page_title', 'Edit Data Persentase Kehadiran')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
     <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Edit Data Kehadiran</h2>
        <a href="{{ route('sekretariat-jenderal.persentase-kehadiran.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('sekretariat-jenderal.persentase-kehadiran.update', $persentaseKehadiran->id) }}" method="POST">
        @method('PUT')
        @include('persentase_kehadiran._form', [
            'persentaseKehadiran' => $persentaseKehadiran, 
            'unitKerjaEselonIs' => $unitKerjaEselonIs, 
            'satuanKerjas' => $satuanKerjas,
            'statusAsnOptions' => $statusAsnOptions,
            'statusKehadiranOptions' => $statusKehadiranOptions
        ])
    </form>
</div>
@endsection
