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

{{-- Input Satuan Kerja diganti menjadi Substansi --}}
<div class="mb-6">
    <label for="substansi_regulasi" class="block text-sm font-medium text-gray-700 mb-1">Substansi <span class="text-red-500">*</span></label>
    <select name="substansi" id="substansi_regulasi" required class="form-input w-full pr-8">
        <option value="">Pilih Substansi</option>
        @foreach($substansiOptions as $key => $value)
            <option value="{{ $key }}"
                    {{ old('substansi', $jumlahRegulasiBaru->substansi ?? '') == $key ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
    </select>
    @error('substansi') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="mb-6">
    <label for="jenis_regulasi_regulasi" class="block text-sm font-medium text-gray-700 mb-1">Jenis Regulasi <span class="text-red-500">*</span></label>
    <select name="jenis_regulasi" id="jenis_regulasi_regulasi" required class="form-input w-full pr-8">
        <option value="">Pilih Jenis Regulasi</option>
        @foreach($jenisRegulasiOptions as $key => $value) {{-- Menggunakan $jenisRegulasiOptions yang sudah diupdate dari controller --}}
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
       class="btn-secondary-outline">
        Batal
    </a>
    <button type="submit"
            class="btn-primary">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>