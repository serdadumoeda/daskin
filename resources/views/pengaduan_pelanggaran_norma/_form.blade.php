@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_tindak_lanjut_norma" class="block text-sm font-medium text-gray-700 mb-1">Tahun Tindak Lanjut</label>
        <input type="number" name="tahun_tindak_lanjut" id="tahun_tindak_lanjut_norma" value="{{ old('tahun_tindak_lanjut', $pengaduanPelanggaranNorma->tahun_tindak_lanjut ?? date('Y')) }}" required
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun_tindak_lanjut') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_tindak_lanjut_norma" class="block text-sm font-medium text-gray-700 mb-1">Bulan Tindak Lanjut</label>
        <select name="bulan_tindak_lanjut" id="bulan_tindak_lanjut_norma" class="form-input w-full pr-8" required>
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
        <label for="jenis_tindak_lanjut_norma" class="block text-sm font-medium text-gray-700 mb-1">Jenis Tindak Lanjut <span class="text-red-500">*</span></label>
        <input type="text" name="jenis_tindak_lanjut" id="jenis_tindak_lanjut_norma" value="{{ old('jenis_tindak_lanjut', $pengaduanPelanggaranNorma->jenis_tindak_lanjut ?? '') }}" required
               class="form-input w-full" maxlength="255" placeholder="Contoh: pemeriksaan, atensi">
        @error('jenis_tindak_lanjut') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="jumlah_pengaduan_tindak_lanjut_norma" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Pengaduan Yang Ditindak Lanjut <span class="text-red-500">*</span></label>
        <input type="number" name="jumlah_pengaduan_tindak_lanjut" id="jumlah_pengaduan_tindak_lanjut_norma" value="{{ old('jumlah_pengaduan_tindak_lanjut', $pengaduanPelanggaranNorma->jumlah_pengaduan_tindak_lanjut ?? 0) }}" required
            class="form-input w-full" min="0">
        @error('jumlah_pengaduan_tindak_lanjut') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">

</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('binwasnaker.pengaduan-pelanggaran-norma.index') }}"
       class="btn-secondary-outline">
        Batal
    </a>
    <button type="submit"
            class="btn-primary">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>
