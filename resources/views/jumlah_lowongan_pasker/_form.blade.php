@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_lowongan" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_lowongan" value="{{ old('tahun', $jumlahLowonganPasker->tahun ?? date('Y')) }}" required
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_lowongan" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_lowongan" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $jumlahLowonganPasker->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="jenis_kelamin_lowongan" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
    <select name="jenis_kelamin" id="jenis_kelamin_lowongan" required class="form-input w-full pr-8">
        <option value="">Pilih Jenis Kelamin</option>
        @foreach($options['jenisKelaminOptions'] as $key => $value)
            <option value="{{ $key }}" {{ old('jenis_kelamin', $jumlahLowonganPasker->jenis_kelamin ?? '') == $key ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
    </select>
    @error('jenis_kelamin') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="mb-6">
    <label for="provinsi_penempatan_lowongan" class="block text-sm font-medium text-gray-700 mb-1">Provinsi Penempatan <span class="text-red-500">*</span></label>
    <input type="text" name="provinsi_penempatan" id="provinsi_penempatan_lowongan" 
           value="{{ old('provinsi_penempatan', $jumlahLowonganPasker->provinsi_penempatan ?? '') }}" required
           class="form-input w-full" maxlength="100" placeholder="Masukkan provinsi penempatan">
    @error('provinsi_penempatan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="mb-6">
    <label for="lapangan_usaha_kbli_lowongan" class="block text-sm font-medium text-gray-700 mb-1">Lapangan Usaha (KBLI) <span class="text-red-500">*</span></label>
    <input type="text" name="lapangan_usaha_kbli" id="lapangan_usaha_kbli_lowongan" 
           value="{{ old('lapangan_usaha_kbli', $jumlahLowonganPasker->lapangan_usaha_kbli ?? '') }}" required
           class="form-input w-full" maxlength="255" placeholder="Masukkan deskripsi Lapangan Usaha (KBLI)">
    @error('lapangan_usaha_kbli') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="status_disabilitas_lowongan" class="block text-sm font-medium text-gray-700 mb-1">Status Disabilitas <span class="text-red-500">*</span></label>
        <select name="status_disabilitas" id="status_disabilitas_lowongan" required class="form-input w-full pr-8">
            <option value="">Pilih Status Disabilitas</option>
            @foreach($options['statusDisabilitasOptions'] as $key => $value)
                <option value="{{ $key }}" {{ old('status_disabilitas', $jumlahLowonganPasker->status_disabilitas ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('status_disabilitas') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="jumlah_lowongan_input" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Lowongan <span class="text-red-500">*</span></label>
        <input type="number" name="jumlah_lowongan" id="jumlah_lowongan_input" value="{{ old('jumlah_lowongan', $jumlahLowonganPasker->jumlah_lowongan ?? 0) }}" required
               class="form-input w-full" min="0">
        @error('jumlah_lowongan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route($routeNamePrefix . 'index') }}"
       class="btn-secondary-outline">
        Batal
    </a>
    <button type="submit"
            class="btn-primary">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>