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
        <div class="mt-6" x-data="{ show: false }">
            <x-input-label for="password" :value="__('Password')" class="font-semibold text-base" />
        
            <div class="relative">
                <!-- Password Input -->
                <x-text-input id="password"
                              name="password"
                              class="block mt-2 w-full pr-10"
                              x-bind:type="show ? 'text' : 'password'"
                              required autocomplete="current-password"
                              placeholder="••••••••" />
        
                <!-- Show/Hide Button inside the input -->
                <button type="button"
                        @click="show = !show"
                        class="absolute inset-y-0 right-0 flex items-center px-3 mt-2 text-gray-600"
                        tabindex="-1" x-text="show ? 'Hide' : 'Show'">
                    {{-- <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
        
                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a10.05 10.05 0 011.332-2.592M9.88 9.88a3 3 0 104.243 4.243M6.1 6.1l11.8 11.8" />
                    </svg> --}}
                </button>
            </div>
        
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