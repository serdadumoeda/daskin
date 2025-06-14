@extends('layouts.app')

@section('title', 'Tambah Laporan WLKP Online')
@section('page_title', 'Tambah Data Laporan WLKP Online')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Tambah Laporan WLKP</h2>
        <a href="{{ route('binwasnaker.pelaporan-wlkp-online.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('binwasnaker.pelaporan-wlkp-online.store') }}" method="POST">
        @include('pelaporan_wlkp_online._form', [
            'pelaporanWlkpOnline' => $pelaporanWlkpOnline,
        ])
    </form>
</div>
@endsection
