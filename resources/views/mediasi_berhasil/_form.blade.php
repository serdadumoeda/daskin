@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_mediasi" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_mediasi" value="{{ old('tahun', $mediasiBerhasil->tahun ?? date('Y')) }}" required 
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_mediasi" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_mediasi" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $mediasiBerhasil->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="provinsi_mediasi" class="block text-sm font-medium text-gray-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
        <input type="text" name="provinsi" id="provinsi_mediasi" value="{{ old('provinsi', $mediasiBerhasil->provinsi ?? '') }}" required 
               class="form-input w-full" maxlength="255" placeholder="Nama Provinsi">
        @error('provinsi') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="kbli_mediasi" class="block text-sm font-medium text-gray-700 mb-1">KBLI <span class="text-red-500">*</span></label>
        <input type="text" name="kbli" id="kbli_mediasi" value="{{ old('kbli', $mediasiBerhasil->kbli ?? '') }}" required 
               class="form-input w-full" maxlength="50" placeholder="Kode atau Kategori KBLI">
        @error('kbli') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="jenis_perselisihan_mediasi" class="block text-sm font-medium text-gray-700 mb-1">Jenis Perselisihan <span class="text-red-500">*</span></label>
        <select name="jenis_perselisihan" id="jenis_perselisihan_mediasi" required class="form-input w-full pr-8">
            <option value="">Pilih Jenis Perselisihan</option>
            @foreach($jenisPerselisihanOptions as $key => $value)
                <option value="{{ $key }}" {{ old('jenis_perselisihan', $mediasiBerhasil->jenis_perselisihan ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('jenis_perselisihan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="hasil_mediasi_mediasi" class="block text-sm font-medium text-gray-700 mb-1">Hasil Mediasi <span class="text-red-500">*</span></label>
        <select name="hasil_mediasi" id="hasil_mediasi_mediasi" required class="form-input w-full pr-8">
            <option value="">Pilih Hasil Mediasi</option>
             @foreach($hasilMediasiOptions as $key => $value)
                <option value="{{ $key }}" {{ old('hasil_mediasi', $mediasiBerhasil->hasil_mediasi ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('hasil_mediasi') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="jumlah_mediasi_input" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Mediasi <span class="text-red-500">*</span></label>
        <input type="number" name="jumlah_mediasi" id="jumlah_mediasi_input" value="{{ old('jumlah_mediasi', $mediasiBerhasil->jumlah_mediasi ?? 0) }}" required 
               class="form-input w-full" min="0">
        @error('jumlah_mediasi') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="jumlah_mediasi_berhasil_input" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Mediasi Berhasil <span class="text-red-500">*</span></label>
        <input type="number" name="jumlah_mediasi_berhasil" id="jumlah_mediasi_berhasil_input" value="{{ old('jumlah_mediasi_berhasil', $mediasiBerhasil->jumlah_mediasi_berhasil ?? 0) }}" required 
               class="form-input w-full" min="0">
        @error('jumlah_mediasi_berhasil') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('phi.mediasi-berhasil.index') }}" 
       class="btn-secondary-outline">
        Batal
    </a>
    <button type="submit" 
            class="btn-primary">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>
