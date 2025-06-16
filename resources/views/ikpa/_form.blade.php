@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_ikpa" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span
                class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_ikpa"
            value="{{ old('tahun', $indikatorKinerjaPelaksanaanAnggaran->tahun ?? date('Y')) }}" required
            class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun')
            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="bulan_ikpa" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span
                class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_ikpa" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}"
                    {{ old('bulan', $indikatorKinerjaPelaksanaanAnggaran->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan')
            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="kode_unit_kerja_eselon_i_ikpa" class="block text-sm font-medium text-gray-700 mb-1">Unit Kerja
            Eselon I <span class="text-red-500">*</span></label>
        <select name="kode_unit_kerja_eselon_i" id="kode_unit_kerja_eselon_i_ikpa" required
            class="form-input w-full pr-8">
            <option value="">Pilih Unit Kerja</option>
            @foreach ($unitKerjaEselonIs as $unit)
                <option value="{{ $unit->kode_uke1 }}"
                    {{ old('kode_unit_kerja_eselon_i', $indikatorKinerjaPelaksanaanAnggaran->kode_unit_kerja_eselon_i ?? '') == $unit->kode_uke1 ? 'selected' : '' }}>
                    {{ $unit->nama_unit_kerja_eselon_i }}
                </option>
            @endforeach
        </select>
        @error('kode_unit_kerja_eselon_i')
            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
        @enderror
    </div>
    <div>
        <label for="aspek_pelaksanaan_anggaran_ikpa" class="block text-sm font-medium text-gray-700 mb-1">Aspek Pelaksanaan Anggaran<span class="text-red-500">*</span></label>
        <select name="aspek_pelaksanaan_anggaran" id="aspek_pelaksanaan_anggaran_ikpa" required
            class="form-input w-full pr-8">
            <option value="">Pilih Aspek Pelaksanaan Anggaran</option>
            @foreach ($aspekPelaksanaanAnggaranOptions as $value)
                <option value="{{ $value }}"
                    {{ old('aspek_pelaksanaan_anggaran', $indikatorKinerjaPelaksanaanAnggaran->aspek_pelaksanaan_anggaran ?? '') == $value ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('aspek_pelaksanaan_anggaran')
            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="nilai_aspek" class="block text-sm font-medium text-gray-700 mb-1">Nilai Aspek<span
                class="text-red-500">*</span></label>
        <input type="number" name="nilai_aspek" id="nilai_aspek"
            value="{{ old('nilai_aspek', $indikatorKinerjaPelaksanaanAnggaran->nilai_aspek ?? 0) }}" required
            class="form-input w-full" min="0" max="999999">
        @error('nilai_aspek')
            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="konversi_bobot" class="block text-sm font-medium text-gray-700 mb-1">Konversi Bobot <span
                class="text-red-500">*</span></label>
        <input type="number" name="konversi_bobot" id="konversi_bobot"
            value="{{ old('konversi_bobot', $indikatorKinerjaPelaksanaanAnggaran->konversi_bobot ?? 0) }}" required
            class="form-input w-full" min="0" max="999999">
        @error('konversi_bobot')
            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="dispensasi_spm" class="block text-sm font-medium text-gray-700 mb-1">Dispensasi SPM<span
                class="text-red-500">*</span></label>
        <input type="number" name="dispensasi_spm" id="dispensasi_spm"
            value="{{ old('dispensasi_spm', $indikatorKinerjaPelaksanaanAnggaran->dispensasi_spm ?? 0) }}" required
            class="form-input w-full" min="0" max="999999">
        @error('dispensasi_spm')
            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label for="nilai_akhir" class="block text-sm font-medium text-gray-700 mb-1">Nilai Akhir<span
                class="text-red-500">*</span></label>
        <input type="number" name="nilai_akhir" id="nilai_akhir"
            value="{{ old('nilai_akhir', $indikatorKinerjaPelaksanaanAnggaran->nilai_akhir ?? 0) }}" required
            class="form-input w-full" min="0" max="999999">
        @error('nilai_akhir')
            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('sekretariat-jenderal.ikpa.index') }}"
       class="btn-secondary-outline">
        Batal
    </a>
    <button type="submit"
            class="btn-primary">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>

