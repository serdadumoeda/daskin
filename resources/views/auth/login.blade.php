<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h1 class="text-3xl font-bold text-gray-800 mb-2">Selamat Datang!</h1>
    <p class="text-gray-500 mb-8">Masukkan email dan password anda!</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Email')" class="font-semibold text-base"/>
            {{-- Komponen text-input sekarang sudah otomatis hijau saat focus --}}
            <x-text-input id="email" class="block mt-2 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Enter your email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div class="mt-6">
            <x-input-label for="password" :value="__('Password')" class="font-semibold text-base"/>
            {{-- Komponen text-input sekarang sudah otomatis hijau saat focus --}}
            <x-text-input id="password" class="block mt-2 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" 
                            placeholder="••••••••"/>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center justify-between mt-6">
            <label for="remember_me" class="inline-flex items-center">
                {{-- DIUBAH: Warna checkbox menjadi hijau tema --}}
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-theme-primary shadow-sm focus:ring-theme-primary-dark" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        {{-- Tombol Sign In --}}
        <div class="flex items-center justify-end mt-8">
            {{-- Komponen primary-button sekarang sudah otomatis hijau. Kelas tambahan hanya untuk ukuran & teks. --}}
            <x-primary-button class="w-full justify-center text-base font-bold py-3">
                {{ __('Sign In') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>