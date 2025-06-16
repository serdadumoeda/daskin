@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_wlkp" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_wlkp" value="{{ old('tahun', $pelaporanWlkpOnline->tahun ?? date('Y')) }}" required
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_wlkp" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_wlkp" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $pelaporanWlkpOnline->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="provinsi_wlkp" class="block text-sm font-medium text-gray-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
        <input type="text" name="provinsi" id="provinsi_wlkp" value="{{ old('provinsi', $pelaporanWlkpOnline->provinsi ?? '') }}" required
               class="form-input w-full" maxlength="255" placeholder="Contoh: Jawa Barat">
        @error('provinsi') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="jumlah_perusahaan_melapor_wlkp" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Perusahaan Melapor <span class="text-red-500">*</span></label>
        <input type="number" name="jumlah_perusahaan_melapor" id="jumlah_perusahaan_melapor_wlkp" value="{{ old('jumlah_perusahaan_melapor', $pelaporanWlkpOnline->jumlah_perusahaan_melapor ?? 0) }}" required
               class="form-input w-full" min="0">
        @error('jumlah_perusahaan_melapor') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('binwasnaker.pelaporan-wlkp-online.index') }}"
       class="btn-secondary-outline">
        Batal
    </a>
    <button type="submit"
            class="btn-primary">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>
