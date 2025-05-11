@extends('layouts.app')

@section('title', 'Detail Perselisihan Ditindaklanjuti')
@section('page_title', 'Detail Data Perselisihan Ditindaklanjuti')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Data Perselisihan</h2>
        <a href="{{ route('phi.perselisihan-ditindaklanjuti.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-3 text-sm">
        <div><strong class="text-gray-600 w-48 inline-block">Tahun:</strong> <span class="text-gray-800">{{ $perselisihanDitindaklanjuti->tahun }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Bulan:</strong> <span class="text-gray-800">{{ \Carbon\Carbon::create()->month($perselisihanDitindaklanjuti->bulan)->isoFormat('MMMM') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Provinsi:</strong> <span class="text-gray-800">{{ $perselisihanDitindaklanjuti->provinsi }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">KBLI:</strong> <span class="text-gray-800">{{ $perselisihanDitindaklanjuti->kbli }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jenis Perselisihan:</strong> <span class="text-gray-800">{{ $perselisihanDitindaklanjuti->jenis_perselisihan }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Cara Penyelesaian:</strong> <span class="text-gray-800">{{ $perselisihanDitindaklanjuti->cara_penyelesaian }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jumlah Perselisihan:</strong> <span class="text-gray-800">{{ number_format($perselisihanDitindaklanjuti->jumlah_perselisihan) }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jumlah Ditindaklanjuti:</strong> <span class="text-gray-800">{{ number_format($perselisihanDitindaklanjuti->jumlah_ditindaklanjuti) }}</span></div>
        
        <div class="border-t pt-3 mt-3"></div>
        <div><strong class="text-gray-600 w-48 inline-block">Dibuat pada:</strong> <span class="text-gray-800">{{ $perselisihanDitindaklanjuti->created_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Diperbarui pada:</strong> <span class="text-gray-800">{{ $perselisihanDitindaklanjuti->updated_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route('phi.perselisihan-ditindaklanjuti.edit', $perselisihanDitindaklanjuti->id) }}" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection
