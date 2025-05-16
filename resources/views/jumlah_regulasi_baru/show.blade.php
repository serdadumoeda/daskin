@extends('layouts.app')

@section('title', 'Detail Jumlah Regulasi Baru')
@section('page_title')
    Detail Regulasi: {{ $jumlahRegulasiBaru->tahun }} - {{ \Carbon\Carbon::create()->month($jumlahRegulasiBaru->bulan)->isoFormat('MMMM') }}
@endsection

@section('content')
<div class="bg-white p-4 sm:p-6 rounded-lg shadow-md">
    <div class="mb-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Detail Jumlah Regulasi Baru</h3>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">ID</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $jumlahRegulasiBaru->id }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Tahun</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $jumlahRegulasiBaru->tahun }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Bulan</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::create()->month($jumlahRegulasiBaru->bulan)->isoFormat('MMMM') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Substansi</dt> {{-- Diubah dari Satuan Kerja --}}
                <dd class="mt-1 text-sm text-gray-900">{{ $jumlahRegulasiBaru->substansi_text }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Jenis Regulasi</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $jumlahRegulasiBaru->jenis_regulasi_text }}</dd> {{-- Menggunakan accessor yang sudah diupdate --}}
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Jumlah Regulasi</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ number_format($jumlahRegulasiBaru->jumlah_regulasi) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Tanggal Dibuat</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $jumlahRegulasiBaru->created_at ? $jumlahRegulasiBaru->created_at->isoFormat('D MMMM YYYY, HH:mm') : '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Tanggal Diperbarui</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $jumlahRegulasiBaru->updated_at ? $jumlahRegulasiBaru->updated_at->isoFormat('D MMMM YYYY, HH:mm') : '-' }}</dd>
            </div>
        </dl>
    </div>

    <div class="mt-8 flex justify-start space-x-3">
        <a href="{{ route('sekretariat-jenderal.jumlah-regulasi-baru.index') }}"
           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
        <a href="{{ route('sekretariat-jenderal.jumlah-regulasi-baru.edit', $jumlahRegulasiBaru->id) }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-button hover:bg-blue-700 text-sm font-medium">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection