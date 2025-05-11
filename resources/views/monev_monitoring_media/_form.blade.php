@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_monev_media" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_monev_media" value="{{ old('tahun', $monevMonitoringMedia->tahun ?? date('Y')) }}" required 
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_monev_media" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_monev_media" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $monevMonitoringMedia->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="jenis_media_monev_media" class="block text-sm font-medium text-gray-700 mb-1">Jenis Media <span class="text-red-500">*</span></label>
        <select name="jenis_media" id="jenis_media_monev_media" required class="form-input w-full pr-8">
            <option value="">Pilih Jenis Media</option>
            @foreach($jenisMediaOptions as $key => $value)
                <option value="{{ $key }}" {{ old('jenis_media', $monevMonitoringMedia->jenis_media ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('jenis_media') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="sentimen_publik_monev_media" class="block text-sm font-medium text-gray-700 mb-1">Sentimen Publik <span class="text-red-500">*</span></label>
        <select name="sentimen_publik" id="sentimen_publik_monev_media" required class="form-input w-full pr-8">
            <option value="">Pilih Sentimen</option>
            @foreach($sentimenPublikOptions as $key => $value)
                <option value="{{ $key }}" {{ old('sentimen_publik', $monevMonitoringMedia->sentimen_publik ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('sentimen_publik') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="jumlah_berita_monev_media" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Berita <span class="text-red-500">*</span></label>
    <input type="number" name="jumlah_berita" id="jumlah_berita_monev_media" value="{{ old('jumlah_berita', $monevMonitoringMedia->jumlah_berita ?? 0) }}" required 
           class="form-input w-full" min="0">
    @error('jumlah_berita') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('sekretariat-jenderal.monev-monitoring-media.index') }}" 
       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
        Batal
    </a>
    <button type="submit" 
            class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>
