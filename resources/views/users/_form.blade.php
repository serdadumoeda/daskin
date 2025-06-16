
<div class="space-y-6">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
        <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" required autocomplete="name"
               class="block w-full form-input px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" required autocomplete="username"
               class="block w-full form-input px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Peran (Bisa pilih lebih dari satu)</label>
        <div class="mt-2 space-y-2 p-4 border border-gray-200 rounded-md">
            @foreach($roles as $role)
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="role-{{ $role }}" name="roles[]" type="checkbox" value="{{ $role }}"
                               @checked( in_array($role, old('roles', isset($user) ? $user->getRoleNames()->all() : [])) )
                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="role-{{ $role }}" class="font-medium text-gray-700">{{ ucfirst($role) }}</label>
                    </div>
                </div>
            @endforeach
        </div>
        @error('roles')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input type="password" name="password" id="password" autocomplete="new-password"
               class="block w-full form-input px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        @if(isset($user))
            <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengubah password.</p>
        @else
            {{-- Menambahkan atribut 'required' via JS agar tidak error saat edit --}}
            <script>document.getElementById('password').setAttribute('required', 'required');</script>
        @endif
        @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
               class="block w-full form-input px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
    </div>
</div>