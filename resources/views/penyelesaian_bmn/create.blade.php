@extends('layouts.app')

@section('title', 'Tambah Data Penyelesaian BMN')
@section('page_title', 'Tambah Data Penyelesaian BMN')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Tambah Data BMN</h2>
        <a href="{{ route('sekretariat-jenderal.penyelesaian-bmn.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('sekretariat-jenderal.penyelesaian-bmn.store') }}" method="POST">
        @include('penyelesaian_bmn._form', [
            'penyelesaianBmn' => $penyelesaianBmn, 
            'satuanKerjas' => $satuanKerjas,
            'statusPenggunaanOptions' => $statusPenggunaanOptions,
            'statusAsetDigunakanOptions' => $statusAsetDigunakanOptions
        ])
    </form>
</div>
@endsection
