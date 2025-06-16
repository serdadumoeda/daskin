
@extends('layouts.app')

@section('page_title', 'Tambah Pengguna Baru')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-md max-w-2xl mx-auto">
    <h2 class="text-xl font-semibold text-gray-800 mb-6">Formulir Pengguna Baru</h2>
    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        
        {{-- Memanggil form partial yang akan kita buat/perbarui --}}
        @include('users._form')

        <div class="flex items-center justify-end mt-6 border-t pt-5">
            <a href="{{ route('users.index') }}" class="btn-secondary-outline">Batal</a>
            <button type="submit" class="btn-primary">
                <i class="ri-save-line mr-1"></i>
                Simpan Pengguna
            </button>
        </div>
    </form>
</div>
@endsection