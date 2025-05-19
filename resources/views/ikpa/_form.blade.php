@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
    <div>
        <x-input-label for="tahun" :value="__('Tahun')" />
        <x-text-input id="tahun" class="block mt-1 w-full" type="number" name="tahun" :value="old('tahun', $ikpa->tahun ?? date('Y'))" required min="2000" max="{{ date('Y') + 5 }}" />
        <x-input-error :messages="$errors->get('tahun')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="bulan" :value="__('Bulan')" />
        <select name="bulan" id="bulan" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" required>
            <option value="">Pilih Bulan</option>
            @foreach($bulanOptions as $key => $value)
                <option value="{{ $key }}" {{ (old('bulan', $ikpa->bulan ?? '') == $key) ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('bulan')" class="mt-2" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="id_unit_kerja_eselon_i" :value="__('Unit Kerja Eselon I')" />
        <select name="id_unit_kerja_eselon_i" id="id_unit_kerja_eselon_i" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" required>
            <option value="">Pilih Unit Kerja</option>
            @foreach($unitKerjaEselonIs as $unit)
                <option value="{{ $unit->id }}" {{ (old('id_unit_kerja_eselon_i', $ikpa->id_unit_kerja_eselon_i ?? '') == $unit->id) ? 'selected' : '' }}>
                    {{ $unit->nama_unit_kerja_eselon_i }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('id_unit_kerja_eselon_i')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="aspek_pelaksanaan_anggaran" :value="__('Aspek Pelaksanaan Anggaran')" />
        <select name="aspek_pelaksanaan_anggaran" id="aspek_pelaksanaan_anggaran" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" required>
            <option value="">Pilih Aspek</option>
            @foreach($aspekOptions as $key => $value)
                <option value="{{ $key }}" {{ (old('aspek_pelaksanaan_anggaran', $ikpa->aspek_pelaksanaan_anggaran ?? '') == $key) ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('aspek_pelaksanaan_anggaran')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="nilai_aspek" :value="__('Nilai Aspek')" />
        <x-text-input id="nilai_aspek" class="block mt-1 w-full" type="number" step="0.01" name="nilai_aspek" :value="old('nilai_aspek', $ikpa->nilai_aspek ?? '')" placeholder="Contoh: 90.50"/>
        <x-input-error :messages="$errors->get('nilai_aspek')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="konversi_bobot" :value="__('Konversi Bobot')" />
        <x-text-input id="konversi_bobot" class="block mt-1 w-full" type="number" step="0.01" name="konversi_bobot" :value="old('konversi_bobot', $ikpa->konversi_bobot ?? '')" placeholder="Contoh: 25.00"/>
        <x-input-error :messages="$errors->get('konversi_bobot')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="dispensasi_spm" :value="__('Dispensasi SPM (Pengurang)')" />
        <x-text-input id="dispensasi_spm" class="block mt-1 w-full" type="number" step="0.01" name="dispensasi_spm" :value="old('dispensasi_spm', $ikpa->dispensasi_spm ?? '')" placeholder="Contoh: 0.00"/>
        <x-input-error :messages="$errors->get('dispensasi_spm')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="nilai_akhir" :value="__('Nilai Akhir')" />
        <x-text-input id="nilai_akhir" class="block mt-1 w-full" type="number" step="0.01" name="nilai_akhir" :value="old('nilai_akhir', $ikpa->nilai_akhir ?? '')" placeholder="Contoh: 90.50"/>
        <x-input-error :messages="$errors->get('nilai_akhir')" class="mt-2" />
    </div>
</div>

<div class="flex items-center justify-end mt-6">
    <a href="{{ route('ikpa.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mr-3">
        Batal
    </a>
    <x-primary-button>
        {{ $ikpa->exists ? __('Perbarui') : __('Simpan') }}
    </x-primary-button>
</div>