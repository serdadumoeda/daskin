@extends('layouts.app')

@section('page_title', 'User Management')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Daftar Pengguna</h2>
        {{-- Tombol Tambah Pengguna Baru --}}
        <a href="{{ route('users.create') }}" class="btn-primary">
            <i class="ri-add-line mr-1"></i>
            Tambah Pengguna
        </a>
    </div>

    <form method="GET" action="{{ route('users.index') }}" class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700">Cari Pengguna</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari berdasarkan nama atau email..."
                       class="mt-1 block w-full form-input px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Filter Berdasarkan Peran</label>
                <select name="role" id="role" class="mt-1 block w-full form-input px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">Semua Peran</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}" @selected(request('role') == $role)>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="btn-primary">Filter</button>
                <a href="{{ route('users.index') }}" class="btn-secondary-outline">Reset</a>
            </div>
        </div>
    </form>
    
    <div class="mt-4 relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-200 uppercase" style="background-color: #3E8785;">
                <tr>
                    <th scope="col" class="px-6 py-3">Nama</th>
                    <th scope="col" class="px-6 py-3">Email</th>
                    <th scope="col" class="px-6 py-3">Peran (Role)</th>
                    <th scope="col" class="px-6 py-3">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $user->name }}</td>
                    <td class="px-6 py-4">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        @foreach($user->getRoleNames() as $roleName)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 mr-1">{{ Str::ucfirst($roleName) }}</span>
                        @endforeach
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('users.edit', $user) }}" class="font-medium text-indigo-600 hover:text-indigo-900">Edit</a>
                        @if (!$user->hasRole('superadmin'))
                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Anda yakin ingin menghapus pengguna ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="font-medium text-red-600 hover:text-red-900">Hapus</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr class="bg-white border-b"><td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada pengguna yang cocok dengan kriteria pencarian Anda.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->appends(request()->query())->links() }}</div>
</div>
@endsection