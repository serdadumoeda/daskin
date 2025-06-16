@extends('layouts.app')

@section('page_title', 'Edit Pengguna')

@section('content')
<div class="bg-white p-5 rounded-xl shadow-md max-w-2xl mx-auto">
    <h2 class="text-xl font-semibold text-gray-800 mb-6">Form Edit Pengguna</h2>
    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Memanggil form partial --}}
        @include('users._form', ['user' => $user])

        <div class="flex items-center justify-end mt-6 border-t pt-5">
            <a href="{{ route('users.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 mr-4">Batal</a>
            <button type="submit" class="text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md px-4 py-2 shadow-sm">
                Perbarui Pengguna
            </button>
        </div>
    </form>
</div>
@endsection