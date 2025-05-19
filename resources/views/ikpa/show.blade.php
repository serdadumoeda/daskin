<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Data IKPA') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6 flex justify-start gap-x-3">
                        <x-secondary-button-link href="{{ route('ikpa.index') }}">
                            Kembali ke Daftar
                        </x-secondary-button-link>
                        <x-primary-button-link href="{{ route('ikpa.edit', $ikpa->id) }}" class="bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                            Edit Data Ini
                        </x-primary-button-link>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tahun:</strong>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $ikpa->tahun }}</p>
                        </div>
                        <div>
                            <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Bulan:</strong>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $ikpa->bulan }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Unit Kerja Eselon I:</strong>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $ikpa->unitKerjaEselonI->nama_unit_kerja_eselon_i ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Aspek Pelaksanaan Anggaran:</strong>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $ikpa->getAspekPelaksanaanAnggaranTextAttribute() }}</p>
                        </div>
                        <div>
                            <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nilai Aspek:</strong>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ number_format($ikpa->nilai_aspek, 2, ',', '.') }}</p>
                        </div>
                        <div>
                            <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Konversi Bobot:</strong>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ number_format($ikpa->konversi_bobot, 2, ',', '.') }}</p>
                        </div>
                        <div>
                            <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Dispensasi SPM (Pengurang):</strong>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ number_format($ikpa->dispensasi_spm, 2, ',', '.') }}</p>
                        </div>
                        <div>
                            <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nilai Akhir:</strong>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ number_format($ikpa->nilai_akhir, 2, ',', '.') }}</p>
                        </div>
                        <hr class="md:col-span-2 my-2 border-gray-200 dark:border-gray-700">
                        <div>
                            <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Dibuat Oleh:</strong>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $ikpa->createdByUser->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tanggal Dibuat:</strong>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $ikpa->created_at ? $ikpa->created_at->translatedFormat('d F Y H:i:s') : 'N/A' }}</p>
                        </div>
                        <div>
                            <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Diperbarui Oleh:</strong>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $ikpa->updatedByUser->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <strong class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tanggal Diperbarui:</strong>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $ikpa->updated_at ? $ikpa->updated_at->translatedFormat('d F Y H:i:s') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>