@extends('layouts.app')

@section('title', 'Detail Penyelesaian BMN')
@section('page_title', 'Detail Data Penyelesaian BMN')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Data BMN</h2>
        <a href="{{ route('sekretariat-jenderal.penyelesaian-bmn.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-3 text-sm">
        <div><strong class="text-gray-600 w-48 inline-block">Tahun:</strong> <span class="text-gray-800">{{ $penyelesaianBmn->tahun }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Bulan Data Diinput:</strong> <span class="text-gray-800">{{ \Carbon\Carbon::create()->month($penyelesaianBmn->bulan)->isoFormat('MMMM') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Satuan Kerja:</strong> <span class="text-gray-800">{{ $penyelesaianBmn->satuanKerja->nama_satuan_kerja ?? $penyelesaianBmn->kode_satuan_kerja }}</span></div>
        
        <div class="border-t pt-3 mt-3"><strong class="text-gray-600 w-full block mb-1">Detail Aset:</strong></div>
        <div><strong class="text-gray-600 w-48 inline-block ml-4">Status Penggunaan Aset:</strong> <span class="text-gray-800">{{ $penyelesaianBmn->status_penggunaan_aset_text }}</span></div>
        @if($penyelesaianBmn->status_penggunaan_aset == 1)
            <div><strong class="text-gray-600 w-48 inline-block ml-4">Status Aset Digunakan:</strong> <span class="text-gray-800">{{ $penyelesaianBmn->status_aset_digunakan_text ?? '-' }}</span></div>
            @if($penyelesaianBmn->status_aset_digunakan == 2 || !empty($penyelesaianBmn->nup)) {{-- Tampilkan NUP jika Belum PSP atau jika NUP ada isinya --}}
                 <div><strong class="text-gray-600 w-48 inline-block ml-4">NUP:</strong> <span class="text-gray-800">{{ $penyelesaianBmn->nup ?? '-' }}</span></div>
            @endif
        @endif
        <div><strong class="text-gray-600 w-48 inline-block ml-4">Kuantitas:</strong> <span class="text-gray-800">{{ number_format($penyelesaianBmn->kuantitas) }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block ml-4">Nilai Aset (Rp):</strong> <span class="text-gray-800">{{ number_format($penyelesaianBmn->nilai_aset_rp, 2, ',', '.') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block ml-4">Total Aset (Rp):</strong> <span class="text-gray-800">{{ number_format($penyelesaianBmn->total_aset_rp, 2, ',', '.') }}</span></div>
        
        <div class="border-t pt-3 mt-3"></div>
        <div><strong class="text-gray-600 w-48 inline-block">Dibuat pada:</strong> <span class="text-gray-800">{{ $penyelesaianBmn->created_at->isoFormat('D MMMM YYYY, HH:mm') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Diperbarui pada:</strong> <span class="text-gray-800">{{ $penyelesaianBmn->updated_at->isoFormat('D MMMM YYYY, HH:mm') }}</span></div>
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route('sekretariat-jenderal.penyelesaian-bmn.edit', $penyelesaianBmn->id) }}" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection
