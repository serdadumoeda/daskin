@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_dk" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_dk" value="{{ old('tahun', $dataKetenagakerjaan->tahun ?? date('Y')) }}" required 
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_dk" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_dk" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $dataKetenagakerjaan->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }} ({{ $i }})
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="p-4 border border-gray-200 rounded-md mb-6">
    <p class="text-sm font-medium text-gray-700 mb-3">Penduduk & Angkatan Kerja (dalam Ribu Jiwa)</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4">
        <div>
            <label for="penduduk_15_atas_dk" class="block text-xs font-medium text-gray-600 mb-1">Penduduk 15+</label>
            <input type="text" name="penduduk_15_atas" id="penduduk_15_atas_dk" value="{{ old('penduduk_15_atas', isset($dataKetenagakerjaan->penduduk_15_atas) ? number_format($dataKetenagakerjaan->penduduk_15_atas, 3, ',', '.') : '') }}" 
                   class="form-input w-full text-sm" placeholder="Contoh: 200.000,500">
            @error('penduduk_15_atas') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="angkatan_kerja_dk" class="block text-xs font-medium text-gray-600 mb-1">Angkatan Kerja</label>
            <input type="text" name="angkatan_kerja" id="angkatan_kerja_dk" value="{{ old('angkatan_kerja', isset($dataKetenagakerjaan->angkatan_kerja) ? number_format($dataKetenagakerjaan->angkatan_kerja, 3, ',', '.') : '') }}" 
                   class="form-input w-full text-sm" placeholder="Contoh: 130.000,750">
            @error('angkatan_kerja') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="tpak_dk" class="block text-xs font-medium text-gray-600 mb-1">TPAK (%)</label>
            <input type="text" name="tpak" id="tpak_dk" value="{{ old('tpak', isset($dataKetenagakerjaan->tpak) ? number_format($dataKetenagakerjaan->tpak, 2, ',', '.') : '') }}" 
                   class="form-input w-full text-sm" placeholder="Contoh: 65,80">
            @error('tpak') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

<div class="p-4 border border-gray-200 rounded-md mb-6">
    <p class="text-sm font-medium text-gray-700 mb-3">Bukan Angkatan Kerja (dalam Ribu Jiwa)</p>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-4">
        <div>
            <label for="bukan_angkatan_kerja_dk" class="block text-xs font-medium text-gray-600 mb-1">Total Bukan AK</label>
            <input type="text" name="bukan_angkatan_kerja" id="bukan_angkatan_kerja_dk" value="{{ old('bukan_angkatan_kerja', isset($dataKetenagakerjaan->bukan_angkatan_kerja) ? number_format($dataKetenagakerjaan->bukan_angkatan_kerja, 3, ',', '.') : '') }}" 
                   class="form-input w-full text-sm" placeholder="Contoh: 70.000,000">
            @error('bukan_angkatan_kerja') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="sekolah_dk" class="block text-xs font-medium text-gray-600 mb-1">Sekolah</label>
            <input type="text" name="sekolah" id="sekolah_dk" value="{{ old('sekolah', isset($dataKetenagakerjaan->sekolah) ? number_format($dataKetenagakerjaan->sekolah, 3, ',', '.') : '') }}" 
                   class="form-input w-full text-sm" placeholder="Contoh: 30.000,000">
            @error('sekolah') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="mengurus_rumah_tangga_dk" class="block text-xs font-medium text-gray-600 mb-1">Mengurus Rumah Tangga</label>
            <input type="text" name="mengurus_rumah_tangga" id="mengurus_rumah_tangga_dk" value="{{ old('mengurus_rumah_tangga', isset($dataKetenagakerjaan->mengurus_rumah_tangga) ? number_format($dataKetenagakerjaan->mengurus_rumah_tangga, 3, ',', '.') : '') }}" 
                   class="form-input w-full text-sm" placeholder="Contoh: 25.000,000">
            @error('mengurus_rumah_tangga') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="lainnya_bak_dk" class="block text-xs font-medium text-gray-600 mb-1">Lainnya (Bukan AK)</label>
            <input type="text" name="lainnya_bak" id="lainnya_bak_dk" value="{{ old('lainnya_bak', isset($dataKetenagakerjaan->lainnya_bak) ? number_format($dataKetenagakerjaan->lainnya_bak, 3, ',', '.') : '') }}" 
                   class="form-input w-full text-sm" placeholder="Contoh: 15.000,000">
            @error('lainnya_bak') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

<div class="p-4 border border-gray-200 rounded-md mb-6">
    <p class="text-sm font-medium text-gray-700 mb-3">Status Pekerjaan</p>
     <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4">
        <div>
            <label for="bekerja_dk" class="block text-xs font-medium text-gray-600 mb-1">Bekerja (Ribu Jiwa)</label>
            <input type="text" name="bekerja" id="bekerja_dk" value="{{ old('bekerja', isset($dataKetenagakerjaan->bekerja) ? number_format($dataKetenagakerjaan->bekerja, 3, ',', '.') : '') }}" 
                   class="form-input w-full text-sm" placeholder="Contoh: 120.500,200">
            @error('bekerja') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="pengangguran_terbuka_dk" class="block text-xs font-medium text-gray-600 mb-1">Pengangguran Terbuka (Ribu Jiwa)</label>
            <input type="text" name="pengangguran_terbuka" id="pengangguran_terbuka_dk" value="{{ old('pengangguran_terbuka', isset($dataKetenagakerjaan->pengangguran_terbuka) ? number_format($dataKetenagakerjaan->pengangguran_terbuka, 3, ',', '.') : '') }}" 
                   class="form-input w-full text-sm" placeholder="Contoh: 9.500,500">
            @error('pengangguran_terbuka') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="tpt_dk" class="block text-xs font-medium text-gray-600 mb-1">TPT (%)</label>
            <input type="text" name="tpt" id="tpt_dk" value="{{ old('tpt', isset($dataKetenagakerjaan->tpt) ? number_format($dataKetenagakerjaan->tpt, 2, ',', '.') : '') }}" 
                   class="form-input w-full text-sm" placeholder="Contoh: 7,25">
            @error('tpt') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>
    </div>
</div>

<div class="mb-6">
    <label for="tingkat_kesempatan_kerja_dk" class="block text-sm font-medium text-gray-700 mb-1">Tingkat Kesempatan Kerja (%)</label>
    <input type="text" name="tingkat_kesempatan_kerja" id="tingkat_kesempatan_kerja_dk" value="{{ old('tingkat_kesempatan_kerja', isset($dataKetenagakerjaan->tingkat_kesempatan_kerja) ? number_format($dataKetenagakerjaan->tingkat_kesempatan_kerja, 2, ',', '.') : '') }}" 
            class="form-input w-full" placeholder="Contoh: 92,75">
    @error('tingkat_kesempatan_kerja') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>


<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('barenbang.data-ketenagakerjaan.index') }}" 
       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
        Batal
    </a>
    <button type="submit" 
            class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>
