@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_tka" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_tka" value="{{ old('tahun', $jumlahTkaDisetujui->tahun ?? date('Y')) }}" required 
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_tka" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_tka" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $jumlahTkaDisetujui->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="jenis_kelamin_tka" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
        <select name="jenis_kelamin" id="jenis_kelamin_tka" required class="form-input w-full pr-8">
            <option value="">Pilih Jenis Kelamin</option>
            @foreach($jenisKelaminOptions as $key => $value)
                <option value="{{ $key }}" {{ old('jenis_kelamin', $jumlahTkaDisetujui->jenis_kelamin ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('jenis_kelamin') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="negara_asal_tka" class="block text-sm font-medium text-gray-700 mb-1">Negara Asal <span class="text-red-500">*</span></label>
        <input type="text" name="negara_asal" id="negara_asal_tka" value="{{ old('negara_asal', $jumlahTkaDisetujui->negara_asal ?? '') }}" required 
               class="form-input w-full" maxlength="255" placeholder="Nama Negara">
        @error('negara_asal') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="jabatan_tka" class="block text-sm font-medium text-gray-700 mb-1">Jabatan <span class="text-red-500">*</span></label>
    <input type="text" name="jabatan" id="jabatan_tka" value="{{ old('jabatan', $jumlahTkaDisetujui->jabatan ?? '') }}" required 
           class="form-input w-full" maxlength="255" placeholder="Nama Jabatan">
    @error('jabatan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="lapangan_usaha_kbli_tka" class="block text-sm font-medium text-gray-700 mb-1">Lapangan Usaha (KBLI) <span class="text-red-500">*</span></label>
        <input type="text" name="lapangan_usaha_kbli" id="lapangan_usaha_kbli_tka" value="{{ old('lapangan_usaha_kbli', $jumlahTkaDisetujui->lapangan_usaha_kbli ?? '') }}" required 
               class="form-input w-full" maxlength="255" placeholder="Kategori KBLI atau Deskripsi">
        @error('lapangan_usaha_kbli') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="provinsi_penempatan_tka" class="block text-sm font-medium text-gray-700 mb-1">Provinsi Penempatan <span class="text-red-500">*</span></label>
        <input type="text" name="provinsi_penempatan" id="provinsi_penempatan_tka" value="{{ old('provinsi_penempatan', $jumlahTkaDisetujui->provinsi_penempatan ?? '') }}" required 
               class="form-input w-full" maxlength="255" placeholder="Nama Provinsi atau Lintas Provinsi">
        @error('provinsi_penempatan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="jumlah_tka_input" class="block text-sm font-medium text-gray-700 mb-1">Jumlah TKA Disetujui <span class="text-red-500">*</span></label>
    <input type="number" name="jumlah_tka" id="jumlah_tka_input" value="{{ old('jumlah_tka', $jumlahTkaDisetujui->jumlah_tka ?? 0) }}" required 
           class="form-input w-full" min="0">
    @error('jumlah_tka') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('binapenta.jumlah-tka-disetujui.index') }}" 
       class="btn-secondary-outline">
        Batal
    </a>
    <button type="submit" 
            class="btn-primary">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>
