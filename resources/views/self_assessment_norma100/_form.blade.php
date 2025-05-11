@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_sa" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_sa" value="{{ old('tahun', $selfAssessmentNorma100->tahun ?? date('Y')) }}" required 
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_sa" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_sa" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $selfAssessmentNorma100->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="provinsi_sa" class="block text-sm font-medium text-gray-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
        <input type="text" name="provinsi" id="provinsi_sa" value="{{ old('provinsi', $selfAssessmentNorma100->provinsi ?? '') }}" required 
               class="form-input w-full" maxlength="255" placeholder="Nama Provinsi">
        @error('provinsi') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="kbli_sa" class="block text-sm font-medium text-gray-700 mb-1">KBLI <span class="text-red-500">*</span></label>
        <input type="text" name="kbli" id="kbli_sa" value="{{ old('kbli', $selfAssessmentNorma100->kbli ?? '') }}" required 
               class="form-input w-full" maxlength="50" placeholder="Kode atau Kategori KBLI">
        @error('kbli') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="skala_perusahaan_sa" class="block text-sm font-medium text-gray-700 mb-1">Skala Perusahaan <span class="text-red-500">*</span></label>
        <select name="skala_perusahaan" id="skala_perusahaan_sa" required class="form-input w-full pr-8">
            <option value="">Pilih Skala</option>
            @foreach($skalaPerusahaanOptions as $skala)
                <option value="{{ $skala }}" {{ old('skala_perusahaan', $selfAssessmentNorma100->skala_perusahaan ?? '') == $skala ? 'selected' : '' }}>
                    {{ $skala }}
                </option>
            @endforeach
        </select>
        @error('skala_perusahaan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="hasil_assessment_sa" class="block text-sm font-medium text-gray-700 mb-1">Hasil Assessment <span class="text-red-500">*</span></label>
        <select name="hasil_assessment" id="hasil_assessment_sa" required class="form-input w-full pr-8">
            <option value="">Pilih Hasil</option>
            @foreach($hasilAssessmentOptions as $hasil)
                <option value="{{ $hasil }}" {{ old('hasil_assessment', $selfAssessmentNorma100->hasil_assessment ?? '') == $hasil ? 'selected' : '' }}>
                    {{ $hasil }}
                </option>
            @endforeach
        </select>
        @error('hasil_assessment') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="jumlah_perusahaan_sa" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Perusahaan <span class="text-red-500">*</span></label>
    <input type="number" name="jumlah_perusahaan" id="jumlah_perusahaan_sa" value="{{ old('jumlah_perusahaan', $selfAssessmentNorma100->jumlah_perusahaan ?? 0) }}" required 
           class="form-input w-full" min="0">
    @error('jumlah_perusahaan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('binwasnaker.self-assessment-norma100.index') }}" 
       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
        Batal
    </a>
    <button type="submit" 
            class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>
