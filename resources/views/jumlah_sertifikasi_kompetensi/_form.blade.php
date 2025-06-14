@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_sertifikasi" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_sertifikasi" value="{{ old('tahun', $jumlahSertifikasiKompetensi->tahun ?? date('Y')) }}" required 
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_sertifikasi" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_sertifikasi" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $jumlahSertifikasiKompetensi->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="jenis_lsp_sertifikasi" class="block text-sm font-medium text-gray-700 mb-1">Jenis LSP <span class="text-red-500">*</span></label>
        <select name="jenis_lsp" id="jenis_lsp_sertifikasi" required class="form-input w-full pr-8">
            <option value="">Pilih Jenis LSP</option>
            @foreach($jenisLspOptions as $key => $value)
                <option value="{{ $key }}" {{ old('jenis_lsp', $jumlahSertifikasiKompetensi->jenis_lsp ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('jenis_lsp') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="jenis_kelamin_sertifikasi" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
        <select name="jenis_kelamin" id="jenis_kelamin_sertifikasi" required class="form-input w-full pr-8">
            <option value="">Pilih Jenis Kelamin</option>
            @foreach($jenisKelaminOptions as $key => $value)
                <option value="{{ $key }}" {{ old('jenis_kelamin', $jumlahSertifikasiKompetensi->jenis_kelamin ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('jenis_kelamin') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="provinsi_sertifikasi" class="block text-sm font-medium text-gray-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
        <input type="text" name="provinsi" id="provinsi_sertifikasi" value="{{ old('provinsi', $jumlahSertifikasiKompetensi->provinsi ?? '') }}" required 
               class="form-input w-full" maxlength="255" placeholder="Nama Provinsi">
        @error('provinsi') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="lapangan_usaha_kbli_sertifikasi" class="block text-sm font-medium text-gray-700 mb-1">Lapangan Usaha (KBLI) <span class="text-red-500">*</span></label>
        <input type="text" name="lapangan_usaha_kbli" id="lapangan_usaha_kbli_sertifikasi" value="{{ old('lapangan_usaha_kbli', $jumlahSertifikasiKompetensi->lapangan_usaha_kbli ?? '') }}" required 
               class="form-input w-full" maxlength="255" placeholder="Kategori KBLI atau Deskripsi">
        @error('lapangan_usaha_kbli') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="jumlah_sertifikasi_input" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Sertifikasi <span class="text-red-500">*</span></label>
    <input type="number" name="jumlah_sertifikasi" id="jumlah_sertifikasi_input" value="{{ old('jumlah_sertifikasi', $jumlahSertifikasiKompetensi->jumlah_sertifikasi ?? 0) }}" required 
           class="form-input w-full" min="0">
    @error('jumlah_sertifikasi') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('binalavotas.jumlah-sertifikasi-kompetensi.index') }}" 
       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
        Batal
    </a>
    <button type="submit" 
            class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>
