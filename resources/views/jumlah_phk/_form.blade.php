@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_phk" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_phk" value="{{ old('tahun', $jumlahPhk->tahun ?? date('Y')) }}" required
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_phk" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_phk" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $jumlahPhk->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="provinsi_phk" class="block text-sm font-medium text-gray-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
    <input type="text" name="provinsi" id="provinsi_phk" value="{{ old('provinsi', $jumlahPhk->provinsi ?? '') }}" required
           class="form-input w-full" maxlength="255" placeholder="Nama Provinsi">
    @error('provinsi') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="jumlah_perusahaan_phk_input" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Perusahaan PHK <span class="text-red-500">*</span></label>
        <input type="number" name="jumlah_perusahaan_phk" id="jumlah_perusahaan_phk_input" value="{{ old('jumlah_perusahaan_phk', $jumlahPhk->jumlah_perusahaan_phk ?? 0) }}" required
               class="form-input w-full" min="0">
        @error('jumlah_perusahaan_phk') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="jumlah_tk_phk_input" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Tenaga Kerja di PHK <span class="text-red-500">*</span></label>
        <input type="number" name="jumlah_tk_phk" id="jumlah_tk_phk_input" value="{{ old('jumlah_tk_phk', $jumlahPhk->jumlah_tk_phk ?? 0) }}" required
               class="form-input w-full" min="0">
        @error('jumlah_tk_phk') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('phi.jumlah-phk.index') }}"
       class="btn-secondary-outline">
        Batal
    </a>
    <button type="submit"
            class="btn-primary">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>
