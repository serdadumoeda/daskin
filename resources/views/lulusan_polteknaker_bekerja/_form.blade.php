@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_lulusan" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_lulusan" value="{{ old('tahun', $lulusanPolteknakerBekerja->tahun ?? date('Y')) }}" required 
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_lulusan" class="block text-sm font-medium text-gray-700 mb-1">Bulan (Periode Data) <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_lulusan" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $lulusanPolteknakerBekerja->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="program_studi_lulusan" class="block text-sm font-medium text-gray-700 mb-1">Program Studi <span class="text-red-500">*</span></label>
    <select name="program_studi" id="program_studi_lulusan" required class="form-input w-full pr-8">
        <option value="">Pilih Program Studi</option>
        @foreach($programStudiOptions as $key => $value)
            <option value="{{ $key }}" {{ old('program_studi', $lulusanPolteknakerBekerja->program_studi ?? '') == $key ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
    </select>
    @error('program_studi') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="jumlah_lulusan_input" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Lulusan <span class="text-red-500">*</span></label>
        <input type="number" name="jumlah_lulusan" id="jumlah_lulusan_input" value="{{ old('jumlah_lulusan', $lulusanPolteknakerBekerja->jumlah_lulusan ?? 0) }}" required 
               class="form-input w-full" min="0">
        @error('jumlah_lulusan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="jumlah_lulusan_bekerja_input" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Lulusan Bekerja <span class="text-red-500">*</span></label>
        <input type="number" name="jumlah_lulusan_bekerja" id="jumlah_lulusan_bekerja_input" value="{{ old('jumlah_lulusan_bekerja', $lulusanPolteknakerBekerja->jumlah_lulusan_bekerja ?? 0) }}" required 
               class="form-input w-full" min="0">
        @error('jumlah_lulusan_bekerja') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('sekretariat-jenderal.lulusan-polteknaker-bekerja.index') }}" 
       class="btn-secondary-outline">
        Batal
    </a>
    <button type="submit" 
            class="btn-primary">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>
