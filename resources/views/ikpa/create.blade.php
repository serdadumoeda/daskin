<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Data IKPA Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                     @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-200 rounded-md">
                            <div class="font-medium">{{ __('Whoops! Ada yang salah dengan input Anda.') }}</div>
                            <ul class="mt-1 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('ikpa.store') }}">
                        {{-- $ikpa di sini adalah instance baru dari model Ikpa yang dikirim dari controller --}}
                        @include('ikpa._form', ['ikpa' => $ikpa, 'bulanOptions' => $bulanOptions, 'aspekOptions' => $aspekOptions, 'unitKerjaEselonIs' => $unitKerjaEselonIs])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>