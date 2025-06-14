@extends('layouts.app')

@section('title', 'Edit Data Penyelesaian BMN')
@section('page_title', 'Edit Data Penyelesaian BMN')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
     <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Edit Data Penyelesaian BMN</h2>
        <a href="{{ route('sekretariat-jenderal.penyelesaian-bmn.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('sekretariat-jenderal.penyelesaian-bmn.update', $penyelesaianBmn->id) }}" method="POST">
        @method('PUT')
        @include('penyelesaian_bmn._form', [
            'penyelesaianBmn' => $penyelesaianBmn,
            'jenisBmnOptions' => $jenisBmnOptions,
            'hentiGunaOptions' => $hentiGunaOptions,
            'statusPenggunaanOptions' => $statusPenggunaanOptions,
            'satuanKerjas' => $satuanKerjas, // Pastikan ini ada
            'routeNamePrefix' => 'sekretariat-jenderal.penyelesaian-bmn.'
        ])
    </form>
</div>
@endsection