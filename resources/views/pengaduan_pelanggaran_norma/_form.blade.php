@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_pengaduan_norma" class="block text-sm font-medium text-gray-700 mb-1">Tahun Pengaduan <span class="text-red-500">*</span></label>
        <input type="number" name="tahun_pengaduan" id="tahun_pengaduan_norma" value="{{ old('tahun_pengaduan', $pengaduanPelanggaranNorma->tahun_pengaduan ?? date('Y')) }}" required 
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun_pengaduan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_pengaduan_norma" class="block text-sm font-medium text-gray-700 mb-1">Bulan Pengaduan <span class="text-red-500">*</span></label>
        <select name="bulan_pengaduan" id="bulan_pengaduan_norma" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan_pengaduan', $pengaduanPelanggaranNorma->bulan_pengaduan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan_pengaduan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_tindak_lanjut_norma" class="block text-sm font-medium text-gray-700 mb-1">Tahun Tindak Lanjut</label>
        <input type="number" name="tahun_tindak_lanjut" id="tahun_tindak_lanjut_norma" value="{{ old('tahun_tindak_lanjut', $pengaduanPelanggaranNorma->tahun_tindak_lanjut ?? '') }}" 
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun_tindak_lanjut') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_tindak_lanjut_norma" class="block text-sm font-medium text-gray-700 mb-1">Bulan Tindak Lanjut</label>
        <select name="bulan_tindak_lanjut" id="bulan_tindak_lanjut_norma" class="form-input w-full pr-8">
            <option value="">Pilih Bulan TL</option>
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan_tindak_lanjut', $pengaduanPelanggaranNorma->bulan_tindak_lanjut ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan_tindak_lanjut') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="provinsi_norma" class="block text-sm font-medium text-gray-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
        <input type="text" name="provinsi" id="provinsi_norma" value="{{ old('provinsi', $pengaduanPelanggaranNorma->provinsi ?? '') }}" required 
               class="form-input w-full" maxlength="255">
        @error('provinsi') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="kbli_norma" class="block text-sm font-medium text-gray-700 mb-1">KBLI <span class="text-red-500">*</span></label>
        <input type="text" name="kbli" id="kbli_norma" value="{{ old('kbli', $pengaduanPelanggaranNorma->kbli ?? '') }}" required 
               class="form-input w-full" maxlength="50">
        @error('kbli') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="jenis_pelanggaran_norma" class="block text-sm font-medium text-gray-700 mb-1">Jenis Pelanggaran <span class="text-red-500">*</span></label>
    <input type="text" name="jenis_pelanggaran" id="jenis_pelanggaran_norma" value="{{ old('jenis_pelanggaran', $pengaduanPelanggaranNorma->jenis_pelanggaran ?? '') }}" required 
           class="form-input w-full" maxlength="255" placeholder="Contoh: Upah Lembur, K3, dll.">
    @error('jenis_pelanggaran') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="jenis_tindak_lanjut_norma" class="block text-sm font-medium text-gray-700 mb-1">Jenis Tindak Lanjut <span class="text-red-500">*</span></label>
        <input type="text" name="jenis_tindak_lanjut" id="jenis_tindak_lanjut_norma" value="{{ old('jenis_tindak_lanjut', $pengaduanPelanggaranNorma->jenis_tindak_lanjut ?? '') }}" required 
               class="form-input w-full" maxlength="255" placeholder="Contoh: pemeriksaan, atensi">
        @error('jenis_tindak_lanjut') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="hasil_tindak_lanjut_norma" class="block text-sm font-medium text-gray-700 mb-1">Hasil Tindak Lanjut <span class="text-red-500">*</span></label>
        <input type="text" name="hasil_tindak_lanjut" id="hasil_tindak_lanjut_norma" value="{{ old('hasil_tindak_lanjut', $pengaduanPelanggaranNorma->hasil_tindak_lanjut ?? '') }}" required 
               class="form-input w-full" maxlength="255" placeholder="Contoh: NP1, rekomendasi">
        @error('hasil_tindak_lanjut') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="jumlah_kasus_norma" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kasus <span class="text-red-500">*</span></label>
    <input type="number" name="jumlah_kasus" id="jumlah_kasus_norma" value="{{ old('jumlah_kasus', $pengaduanPelanggaranNorma->jumlah_kasus ?? 0) }}" required 
           class="form-input w-full" min="0">
    @error('jumlah_kasus') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('binwasnaker.pengaduan-pelanggaran-norma.index') }}" 
       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
        Batal
    </a>
    <button type="submit" 
            class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>
