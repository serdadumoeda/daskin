@extends('layouts.app')

@section('title', 'Detail Pengaduan Pelanggaran Norma')
@section('page_title', 'Detail Data Pengaduan Pelanggaran Norma')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Pengaduan</h2>
        <a href="{{ route('binwasnaker.pengaduan-pelanggaran-norma.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-3 text-sm">
        <div><strong class="text-gray-600 w-48 inline-block">Tahun Tindak Lanjut:</strong> <span class="text-gray-800">{{ $pengaduanPelanggaranNorma->tahun_tindak_lanjut ?? '-' }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Bulan Tindak Lanjut:</strong> <span class="text-gray-800">{{ $pengaduanPelanggaranNorma->bulan_tindak_lanjut ? \Carbon\Carbon::create()->month($pengaduanPelanggaranNorma->bulan_tindak_lanjut)->isoFormat('MMMM') : '-' }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jenis Tindak Lanjut:</strong> <span class="text-gray-800">{{ $pengaduanPelanggaranNorma->jenis_tindak_lanjut }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jumlah Pengaduan Yang Ditindak Lanjut:</strong> <span class="text-gray-800">{{ number_format($pengaduanPelanggaranNorma->jumlah_pengaduan_tindak_lanjut) }}</span></div>

        <div class="border-t pt-3 mt-3"></div>
        <div><strong class="text-gray-600 w-48 inline-block">Dibuat pada:</strong> <span class="text-gray-800">{{ $pengaduanPelanggaranNorma->created_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Diperbarui pada:</strong> <span class="text-gray-800">{{ $pengaduanPelanggaranNorma->updated_at->isoFormat('D MMMM finalList, HH:mm') }}</span></div>
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route('binwasnaker.pengaduan-pelanggaran-norma.edit', $pengaduanPelanggaranNorma->id) }}" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection
