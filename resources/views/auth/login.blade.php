<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h1 class="text-3xl font-bold text-gray-800 mb-2">Selamat Datang!</h1>
    <p class="text-gray-500 mb-8">Masukkan email dan password anda!</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" class="font-semibold text-base"/>
            <x-text-input id="email" class="block mt-2 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Enter your email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-input-label for="password" :value="__('Password')" class="font-semibold text-base"/>
            <x-text-input id="password" class="block mt-2 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" 
                            placeholder="••••••••"/>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-800 shadow-sm focus:ring-blue-700" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>

            <!-- @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif -->
        </div>

        <div class="flex items-center justify-end mt-8">
            <x-primary-button class="w-full justify-center text-base font-bold py-3 bg-blue-800 hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900">
                {{ __('Sign In') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>