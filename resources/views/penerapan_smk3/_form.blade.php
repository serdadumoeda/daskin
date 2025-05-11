@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_smk3" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_smk3" value="{{ old('tahun', $penerapanSmk3->tahun ?? date('Y')) }}" required 
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_smk3" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_smk3" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $penerapanSmk3->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="provinsi_smk3" class="block text-sm font-medium text-gray-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
        <input type="text" name="provinsi" id="provinsi_smk3" value="{{ old('provinsi', $penerapanSmk3->provinsi ?? '') }}" required 
               class="form-input w-full" maxlength="255" placeholder="Nama Provinsi">
        @error('provinsi') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="kbli_smk3" class="block text-sm font-medium text-gray-700 mb-1">KBLI <span class="text-red-500">*</span></label>
        <input type="text" name="kbli" id="kbli_smk3" value="{{ old('kbli', $penerapanSmk3->kbli ?? '') }}" required 
               class="form-input w-full" maxlength="50" placeholder="Kode atau Kategori KBLI">
        @error('kbli') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div>
        <label for="kategori_penilaian_smk3" class="block text-sm font-medium text-gray-700 mb-1">Kategori Penilaian <span class="text-red-500">*</span></label>
        <select name="kategori_penilaian" id="kategori_penilaian_smk3" required class="form-input w-full pr-8">
            <option value="">Pilih Kategori</option>
            @foreach($kategoriPenilaianOptions as $option)
                <option value="{{ $option }}" {{ old('kategori_penilaian', $penerapanSmk3->kategori_penilaian ?? '') == $option ? 'selected' : '' }}>
                    {{ $option }}
                </option>
            @endforeach
        </select>
        @error('kategori_penilaian') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="tingkat_pencapaian_smk3" class="block text-sm font-medium text-gray-700 mb-1">Tingkat Pencapaian <span class="text-red-500">*</span></label>
        <select name="tingkat_pencapaian" id="tingkat_pencapaian_smk3" required class="form-input w-full pr-8">
            <option value="">Pilih Tingkat</option>
            @foreach($tingkatPencapaianOptions as $option)
                <option value="{{ $option }}" {{ old('tingkat_pencapaian', $penerapanSmk3->tingkat_pencapaian ?? '') == $option ? 'selected' : '' }}>
                    {{ $option }}
                </option>
            @endforeach
        </select>
        @error('tingkat_pencapaian') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="jenis_penghargaan_smk3" class="block text-sm font-medium text-gray-700 mb-1">Jenis Penghargaan <span class="text-red-500">*</span></label>
        <select name="jenis_penghargaan" id="jenis_penghargaan_smk3" required class="form-input w-full pr-8">
            <option value="">Pilih Jenis Penghargaan</option>
            @foreach($jenisPenghargaanOptions as $option)
                <option value="{{ $option }}" {{ old('jenis_penghargaan', $penerapanSmk3->jenis_penghargaan ?? '') == $option ? 'selected' : '' }}>
                    {{ $option }}
                </option>
            @endforeach
        </select>
        @error('jenis_penghargaan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="jumlah_perusahaan_smk3" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Perusahaan <span class="text-red-500">*</span></label>
    <input type="number" name="jumlah_perusahaan" id="jumlah_perusahaan_smk3" value="{{ old('jumlah_perusahaan', $penerapanSmk3->jumlah_perusahaan ?? 0) }}" required 
           class="form-input w-full" min="0">
    @error('jumlah_perusahaan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('binwasnaker.penerapan-smk3.index') }}" 
       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
        Batal
    </a>
    <button type="submit" 
            class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>
