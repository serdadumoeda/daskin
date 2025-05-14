@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_pasker" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_pasker" value="{{ old('tahun', $jumlahLowonganPasker->tahun ?? date('Y')) }}" required 
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_pasker" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_pasker" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $jumlahLowonganPasker->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="provinsi_perusahaan_pasker" class="block text-sm font-medium text-gray-700 mb-1">Provinsi Perusahaan <span class="text-red-500">*</span></label>
        <input type="text" name="provinsi_perusahaan" id="provinsi_perusahaan_pasker" value="{{ old('provinsi_perusahaan', $jumlahLowonganPasker->provinsi_perusahaan ?? '') }}" required 
               class="form-input w-full" maxlength="255" placeholder="Nama Provinsi">
        @error('provinsi_perusahaan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="lapangan_usaha_kbli_pasker" class="block text-sm font-medium text-gray-700 mb-1">Lapangan Usaha (KBLI) <span class="text-red-500">*</span></label>
        <input type="text" name="lapangan_usaha_kbli" id="lapangan_usaha_kbli_pasker" value="{{ old('lapangan_usaha_kbli', $jumlahLowonganPasker->lapangan_usaha_kbli ?? '') }}" required 
               class="form-input w-full" maxlength="255" placeholder="Kategori KBLI atau Deskripsi">
        @error('lapangan_usaha_kbli') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="jabatan_pasker" class="block text-sm font-medium text-gray-700 mb-1">Jabatan <span class="text-red-500">*</span></label>
    <input type="text" name="jabatan" id="jabatan_pasker" value="{{ old('jabatan', $jumlahLowonganPasker->jabatan ?? '') }}" required 
           class="form-input w-full" maxlength="255" placeholder="Nama Jabatan">
    @error('jabatan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="jenis_kelamin_dibutuhkan_pasker" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin Dibutuhkan <span class="text-red-500">*</span></label>
        <select name="jenis_kelamin_dibutuhkan" id="jenis_kelamin_dibutuhkan_pasker" required class="form-input w-full pr-8">
            <option value="">Pilih Jenis Kelamin</option>
            @foreach($jenisKelaminDibutuhkanOptions as $key => $value)
                <option value="{{ $key }}" {{ old('jenis_kelamin_dibutuhkan', $jumlahLowonganPasker->jenis_kelamin_dibutuhkan ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('jenis_kelamin_dibutuhkan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="status_disabilitas_dibutuhkan_pasker" class="block text-sm font-medium text-gray-700 mb-1">Status Disabilitas Dibutuhkan <span class="text-red-500">*</span></label>
        <select name="status_disabilitas_dibutuhkan" id="status_disabilitas_dibutuhkan_pasker" required class="form-input w-full pr-8">
            <option value="">Pilih Status</option>
            @foreach($statusDisabilitasDibutuhkanOptions as $key => $value)
                <option value="{{ $key }}" {{ old('status_disabilitas_dibutuhkan', $jumlahLowonganPasker->status_disabilitas_dibutuhkan ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('status_disabilitas_dibutuhkan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="jumlah_lowongan_pasker" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Lowongan <span class="text-red-500">*</span></label>
    <input type="number" name="jumlah_lowongan" id="jumlah_lowongan_pasker" value="{{ old('jumlah_lowongan', $jumlahLowonganPasker->jumlah_lowongan ?? 0) }}" required 
           class="form-input w-full" min="0">
    @error('jumlah_lowongan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('binapenta.jumlah-lowongan-pasker.index') }}" 
       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
        Batal
    </a>
    <button type="submit" 
            class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>
