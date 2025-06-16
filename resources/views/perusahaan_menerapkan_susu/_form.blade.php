@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_susu" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_susu" value="{{ old('tahun', $perusahaanMenerapkanSusu->tahun ?? date('Y')) }}" required 
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_susu" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_susu" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $perusahaanMenerapkanSusu->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="provinsi_susu" class="block text-sm font-medium text-gray-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
    <input type="text" name="provinsi" id="provinsi_susu" value="{{ old('provinsi', $perusahaanMenerapkanSusu->provinsi ?? '') }}" required 
           class="form-input w-full" maxlength="255" placeholder="Nama Provinsi">
    @error('provinsi') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="mb-6">
    <label for="kbli_susu" class="block text-sm font-medium text-gray-700 mb-1">KBLI <span class="text-red-500">*</span></label>
    <input type="text" name="kbli" id="kbli_susu" value="{{ old('kbli', $perusahaanMenerapkanSusu->kbli ?? '') }}" required 
           class="form-input w-full" maxlength="50" placeholder="Kode atau Kategori KBLI">
    @error('kbli') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="mb-6">
    <label for="jumlah_perusahaan_susu_input" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Perusahaan Menerapkan SUSU <span class="text-red-500">*</span></label>
    <input type="number" name="jumlah_perusahaan_susu" id="jumlah_perusahaan_susu_input" value="{{ old('jumlah_perusahaan_susu', $perusahaanMenerapkanSusu->jumlah_perusahaan_susu ?? 0) }}" required 
           class="form-input w-full" min="0">
    @error('jumlah_perusahaan_susu') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('phi.perusahaan-menerapkan-susu.index') }}" 
       class="btn-secondary-outline">
        Batal
    </a>
    <button type="submit" 
            class="btn-primary">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>
