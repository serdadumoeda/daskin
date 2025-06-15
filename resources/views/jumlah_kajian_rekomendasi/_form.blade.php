@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_kajian" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_kajian" value="{{ old('tahun', $jumlahKajianRekomendasi->tahun ?? date('Y')) }}" required 
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_kajian" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_kajian" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $jumlahKajianRekomendasi->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="substansi_kajian" class="block text-sm font-medium text-gray-700 mb-1">Substansi <span class="text-red-500">*</span></label>
    <select name="substansi" id="substansi_kajian" required class="form-input w-full pr-8">
        <option value="">Pilih Substansi</option>
        @foreach($substansiOptions as $key => $value)
            <option value="{{ $key }}" {{ old('substansi', $jumlahKajianRekomendasi->substansi ?? '') == $key ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
    </select>
    @error('substansi') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="jenis_output_kajian" class="block text-sm font-medium text-gray-700 mb-1">Jenis Output <span class="text-red-500">*</span></label>
        <select name="jenis_output" id="jenis_output_kajian" required class="form-input w-full pr-8">
            <option value="">Pilih Jenis</option>
            @foreach($jenisOutputOptions as $key => $value)
                <option value="{{ $key }}" {{ old('jenis_output', $jumlahKajianRekomendasi->jenis_output ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('jenis_output') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="jumlah_kajian_input" class="block text-sm font-medium text-gray-700 mb-1">Jumlah <span class="text-red-500">*</span></label>
        <input type="number" name="jumlah" id="jumlah_kajian_input" value="{{ old('jumlah', $jumlahKajianRekomendasi->jumlah ?? 0) }}" required 
               class="form-input w-full" min="0">
        @error('jumlah') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('barenbang.jumlah-kajian-rekomendasi.index') }}" 
       class="btn-secondary-outline">
        Batal
    </a>
    <button type="submit" 
            class="btn-primary ">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>
