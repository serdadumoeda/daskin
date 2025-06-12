@extends('layouts.app')

@section('title', 'Detail Persentase Kehadiran')
@section('page_title', 'Detail Data Persentase Kehadiran')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Data Kehadiran</h2>
        <a href="{{ route('sekretariat-jenderal.persentase-kehadiran.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-3 text-sm">
        <div><strong class="text-gray-600 w-48 inline-block">Tahun:</strong> <span class="text-gray-800">{{ $persentaseKehadiran->tahun }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Bulan:</strong> <span class="text-gray-800">{{ \Carbon\Carbon::create()->month($persentaseKehadiran->bulan)->isoFormat('MMMM') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Unit Kerja Eselon I:</strong> <span class="text-gray-800">{{ $persentaseKehadiran->unitKerjaEselonI->nama_unit_kerja_eselon_i ?? $persentaseKehadiran->kode_unit_kerja_eselon_i }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Status ASN:</strong> <span class="text-gray-800">{{ $persentaseKehadiran->status_asn_text }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Status Kehadiran:</strong> <span class="text-gray-800">{{ $persentaseKehadiran->status_kehadiran_text }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jumlah Orang:</strong> <span class="text-gray-800">{{ number_format($persentaseKehadiran->jumlah_orang) }}</span></div>

        <div class="border-t pt-3 mt-3"></div>
        <div><strong class="text-gray-600 w-48 inline-block">Dibuat pada:</strong> <span class="text-gray-800">{{ $persentaseKehadiran->created_at->isoFormat('D MMMM YYYY, HH:mm') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Diperbarui pada:</strong> <span class="text-gray-800">{{ $persentaseKehadiran->updated_at->isoFormat('D MMMM YYYY, HH:mm') }}</span></div>
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route('sekretariat-jenderal.persentase-kehadiran.edit', $persentaseKehadiran->id) }}" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection
