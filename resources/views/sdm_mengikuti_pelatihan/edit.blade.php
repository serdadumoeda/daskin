@extends('layouts.app')

@section('title', 'Edit Data SDM Mengikuti Pelatihan')
@section('page_title', 'Edit Data SDM Mengikuti Pelatihan')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
     <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Edit Data Pelatihan SDM</h2>
        <a href="{{ route('sekretariat-jenderal.sdm-mengikuti-pelatihan.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('sekretariat-jenderal.sdm-mengikuti-pelatihan.update', $sdmMengikutiPelatihan->id) }}" method="POST">
        @method('PUT')
        @include('sdm_mengikuti_pelatihan._form', [
            'sdmMengikutiPelatihan' => $sdmMengikutiPelatihan, 
            'unitKerjaEselonIs' => $unitKerjaEselonIs, 
            'satuanKerjas' => $satuanKerjas,
            'jenisPelatihanOptions' => $jenisPelatihanOptions
        ])
    </form>
</div>
@endsection
