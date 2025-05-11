@extends('layouts.app')

@section('title', 'Edit Data Monev Monitoring Media')
@section('page_title', 'Edit Data Monev Monitoring Media')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
     <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Edit Data Monev Media</h2>
        <a href="{{ route('sekretariat-jenderal.monev-monitoring-media.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('sekretariat-jenderal.monev-monitoring-media.update', $monevMonitoringMedia->id) }}" method="POST">
        @method('PUT')
        @include('monev_monitoring_media._form', [
            'monevMonitoringMedia' => $monevMonitoringMedia,
            'jenisMediaOptions' => $jenisMediaOptions,
            'sentimenPublikOptions' => $sentimenPublikOptions
            ])
    </form>
</div>
@endsection
