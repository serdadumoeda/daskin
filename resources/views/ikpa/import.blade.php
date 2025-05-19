<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Import Data IKPA dari Excel') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @include('layouts.partials.alerts') {{-- Asumsi Anda punya partial untuk alert --}}

                     @if ($errors->any() && !$errors->has('file'))
                        <div class="mb-4 p-4 bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-200 rounded-md">
                            <div class="font-medium">{{ __('Whoops! Ada yang salah.') }}</div>
                            <ul class="mt-1 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-6 p-4 border border-blue-300 dark:border-blue-700 bg-blue-50 dark:bg-gray-700/30 rounded-md">
                        <h3 class="text-lg font-semibold text-blue-700 dark:text-blue-300 mb-2">Petunjuk Impor File Excel:</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            1. Pastikan file Anda berformat .xls atau .xlsx.<br>
                            2. Baris pertama (header) harus berisi judul kolom. Data dimulai dari baris kedua.<br>
                            3. Nama kolom (header) yang diharapkan (case-insensitive):
                        </p>
                        <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400 mt-2 ml-4">
                            <li><code>tahun</code> (Contoh: 2024) - Wajib</li>
                            <li><code>bulan</code> (Contoh: Januari, Februari, dst.) - Wajib, nama bulan Indonesia lengkap</li>
                            <li><code>unit_kerja</code> (Nama lengkap Unit Kerja Eselon I) - Wajib, harus terdaftar di sistem</li>
                            <li><code>aspek_pelaksanaan_anggaran</code> (Contoh: Kualitas Perencanaan Anggaran) - Wajib, harus sesuai opsi</li>
                            <li><code>nilai_aspek</code> (Angka, contoh: 90.50) - Opsional</li>
                            <li><code>konversi_bobot</code> (Angka) - Opsional</li>
                            <li><code>dispensasi_spm_pengurang</code> atau <code>dispensasi_spm</code> (Angka) - Opsional</li>
                            <li><code>nilai_akhir</code> (Angka) - Opsional</li>
                        </ul>
                         <p class="text-sm text-gray-600 dark:text-gray-400 mt-3">
                            4. Data yang tidak sesuai format atau referensi (seperti Unit Kerja, Bulan, Aspek) tidak ditemukan akan dilewati dan errornya akan ditampilkan setelah proses impor.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('ikpa.importExcel') }}" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <x-input-label for="file" :value="__('Pilih File Excel (.xls, .xlsx)')" class="mb-1"/>
                            <input type="file" name="file" id="file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" required accept=".xls,.xlsx">
                            <x-input-error :messages="$errors->get('file')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                             <x-secondary-button-link href="{{ route('ikpa.index') }}" class="mr-3">
                                Batal
                            </x-secondary-button-link>
                            <x-primary-button type="submit">
                                {{ __('Mulai Proses Impor') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>