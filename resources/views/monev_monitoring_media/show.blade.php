@extends('layouts.app')

@section('title', 'Detail Monev Monitoring Media')
@section('page_title', 'Detail Data Monev Monitoring Media')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Detail Data Monev Media</h2>
        <a href="{{ route('sekretariat-jenderal.monev-monitoring-media.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="space-y-3 text-sm">
        <div><strong class="text-gray-600 w-48 inline-block">Tahun:</strong> <span class="text-gray-800">{{ $monevMonitoringMedia->tahun }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Bulan:</strong> <span class="text-gray-800">{{ \Carbon\Carbon::create()->month($monevMonitoringMedia->bulan)->isoFormat('MMMM') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Jenis Media:</strong> <span class="text-gray-800">{{ $monevMonitoringMedia->jenis_media_text }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Sentimen Publik:</strong> 
            @if($monevMonitoringMedia->sentimen_publik == 1)
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    {{ $monevMonitoringMedia->sentimen_publik_text }}
                </span>
            @elseif($monevMonitoringMedia->sentimen_publik == 2)
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                    {{ $monevMonitoringMedia->sentimen_publik_text }}
                </span>
            @else
                <span class="text-gray-800">{{ $monevMonitoringMedia->sentimen_publik_text }}</span>
            @endif
        </div>
        <div><strong class="text-gray-600 w-48 inline-block">Jumlah Berita:</strong> <span class="text-gray-800">{{ number_format($monevMonitoringMedia->jumlah_berita) }}</span></div>
        
        <div class="border-t pt-3 mt-3"></div>
        <div><strong class="text-gray-600 w-48 inline-block">Dibuat pada:</strong> <span class="text-gray-800">{{ $monevMonitoringMedia->created_at->isoFormat('D MMMM YYYY, HH:mm') }}</span></div>
        <div><strong class="text-gray-600 w-48 inline-block">Diperbarui pada:</strong> <span class="text-gray-800">{{ $monevMonitoringMedia->updated_at->isoFormat('D MMMM YYYY, HH:mm') }}</span></div>
    </div>

    <div class="mt-8 flex justify-end">
        <a href="{{ route('sekretariat-jenderal.monev-monitoring-media.edit', $monevMonitoringMedia->id) }}" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
            <i class="ri-pencil-line mr-1"></i> Edit
        </a>
    </div>
</div>
@endsection
