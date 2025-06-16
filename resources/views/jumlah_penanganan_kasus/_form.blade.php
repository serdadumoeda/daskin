@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_kasus" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_kasus" value="{{ old('tahun', $jumlahPenangananKasus->tahun ?? date('Y')) }}" required 
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_kasus" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_kasus" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $jumlahPenangananKasus->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="substansi_kasus" class="block text-sm font-medium text-gray-700 mb-1">Substansi <span class="text-red-500">*</span></label>
    <input type="text" name="substansi" id="substansi_kasus" value="{{ old('substansi', $jumlahPenangananKasus->substansi ?? '') }}" required 
           class="form-input w-full" maxlength="255" placeholder="Masukkan substansi perkara">
    @error('substansi') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="mb-6">
    <label for="jenis_perkara_kasus" class="block text-sm font-medium text-gray-700 mb-1">Jenis Perkara <span class="text-red-500">*</span></label>
    <input type="text" name="jenis_perkara" id="jenis_perkara_kasus" value="{{ old('jenis_perkara', $jumlahPenangananKasus->jenis_perkara ?? '') }}" required 
           class="form-input w-full" maxlength="255" placeholder="Contoh: Putusan MA, Putusan MK">
    @error('jenis_perkara') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="mb-6">
    <label for="jumlah_perkara_kasus" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Perkara <span class="text-red-500">*</span></label>
    <input type="number" name="jumlah_perkara" id="jumlah_perkara_kasus" value="{{ old('jumlah_perkara', $jumlahPenangananKasus->jumlah_perkara ?? 0) }}" required 
           class="form-input w-full" min="0">
    @error('jumlah_perkara') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>


<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('sekretariat-jenderal.jumlah-penanganan-kasus.index') }}" 
       class="btn-secondary-outline">
        Batal
    </a>
    <button type="submit" 
            class="btn-primary">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>