@extends('layouts.app')

@section('title', 'Edit Data MoU')
@section('page_title', 'Edit Data MoU')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
     <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Formulir Edit Data MoU</h2>
        <a href="{{ route('sekretariat-jenderal.progress-mou.index') }}" class="text-sm text-primary hover:text-primary/80 flex items-center">
            <i class="ri-arrow-left-line mr-1"></i> Kembali ke Daftar
        </a>
    </div>
    <form action="{{ route('sekretariat-jenderal.progress-mou.update', $progressMou->id) }}" method="POST">
        @method('PUT')
        {{-- Variabel $progressMou dikirim dari controller edit() method --}}
        @include('progress_mou._form', ['progressMou' => $progressMou])
    </form>
</div>
@endsection
