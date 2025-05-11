@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_regulasi" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_regulasi" value="{{ old('tahun', $jumlahRegulasiBaru->tahun ?? date('Y')) }}" required 
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_regulasi" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_regulasi" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $jumlahRegulasiBaru->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="kode_satuan_kerja_regulasi" class="block text-sm font-medium text-gray-700 mb-1">Satuan Kerja <span class="text-red-500">*</span></label>
    <select name="kode_satuan_kerja" id="kode_satuan_kerja_regulasi" required class="form-input w-full pr-8">
        <option value="">Pilih Satuan Kerja</option>
        @foreach($satuanKerjas as $satker)
            <option value="{{ $satker->kode_sk }}" 
                    {{ old('kode_satuan_kerja', $jumlahRegulasiBaru->kode_satuan_kerja ?? '') == $satker->kode_sk ? 'selected' : '' }}>
                {{ $satker->nama_satuan_kerja }}
            </option>
        @endforeach
    </select>
    @error('kode_satuan_kerja') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="mb-6">
    <label for="jenis_regulasi_regulasi" class="block text-sm font-medium text-gray-700 mb-1">Jenis Regulasi <span class="text-red-500">*</span></label>
    <select name="jenis_regulasi" id="jenis_regulasi_regulasi" required class="form-input w-full pr-8">
        <option value="">Pilih Jenis Regulasi</option>
        @foreach($jenisRegulasiOptions as $key => $value)
            <option value="{{ $key }}" {{ old('jenis_regulasi', $jumlahRegulasiBaru->jenis_regulasi ?? '') == $key ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
    </select>
    @error('jenis_regulasi') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="mb-6">
    <label for="jumlah_regulasi_input" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Regulasi <span class="text-red-500">*</span></label>
    <input type="number" name="jumlah_regulasi" id="jumlah_regulasi_input" value="{{ old('jumlah_regulasi', $jumlahRegulasiBaru->jumlah_regulasi ?? 0) }}" required 
           class="form-input w-full" min="0">
    @error('jumlah_regulasi') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('sekretariat-jenderal.jumlah-regulasi-baru.index') }}" 
       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
        Batal
    </a>
    <button type="submit" 
            class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>
