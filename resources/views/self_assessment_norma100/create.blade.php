@extends('layouts.app')

@section('title', 'Tambah Self Assessment Norma 100')
@section('page_title', 'Tambah Data Self Assessment Norma 100')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Tambah Assessment</h2>
        <a href="{{ route('binwasnaker.self-assessment-norma100.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('binwasnaker.self-assessment-norma100.store') }}" method="POST">
        @include('self_assessment_norma100._form', [
            'selfAssessmentNorma100' => $selfAssessmentNorma100,
            'skalaPerusahaanOptions' => $skalaPerusahaanOptions,
            'hasilAssessmentOptions' => $hasilAssessmentOptions
        ])
    </form>
</div>
@endsection
