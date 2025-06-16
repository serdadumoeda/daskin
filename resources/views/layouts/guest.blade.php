<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" href="{{ asset('image/logo/logo_kemnaker.svg') }}" type="image/svg+xml">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex items-center justify-center bg-gray-100 p-4">
            <div class="flex w-full max-w-5xl bg-white shadow-lg rounded-xl overflow-hidden">
                <div class="hidden md:flex w-1/2 text-white p-12 flex-col justify-center items-center" style="background-color: #3E8785;">
                    <a href="/">
                        <img src="{{ asset('image/logo/Kemnaker_Logo_White.png') }}" alt="Logo" class="w-auto h-20 mb-6" />
                    </a>
                    <h2 class="text-3xl font-bold mb-2 text-center">Dashboard Kinerja</h2>
                    <p class="text-center text-base">Kementerian Ketenagakerjaan</p>
                </div>
                <div class="w-full md:w-1/2 p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>