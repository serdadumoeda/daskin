@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_penempatan" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_penempatan" value="{{ old('tahun', $jumlahPenempatanKemnaker->tahun ?? date('Y')) }}" required 
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_penempatan" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_penempatan" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $jumlahPenempatanKemnaker->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="jenis_kelamin_penempatan" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
        <select name="jenis_kelamin" id="jenis_kelamin_penempatan" required class="form-input w-full pr-8">
            <option value="">Pilih Jenis Kelamin</option>
            @foreach($jenisKelaminOptions as $key => $value)
                <option value="{{ $key }}" {{ old('jenis_kelamin', $jumlahPenempatanKemnaker->jenis_kelamin ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('jenis_kelamin') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="provinsi_domisili_penempatan" class="block text-sm font-medium text-gray-700 mb-1">Provinsi Domisili <span class="text-red-500">*</span></label>
        <input type="text" name="provinsi_domisili" id="provinsi_domisili_penempatan" value="{{ old('provinsi_domisili', $jumlahPenempatanKemnaker->provinsi_domisili ?? '') }}" required 
               class="form-input w-full" maxlength="255" placeholder="Nama Provinsi">
        @error('provinsi_domisili') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="lapangan_usaha_kbli_penempatan" class="block text-sm font-medium text-gray-700 mb-1">Lapangan Usaha (KBLI) <span class="text-red-500">*</span></label>
    <input type="text" name="lapangan_usaha_kbli" id="lapangan_usaha_kbli_penempatan" value="{{ old('lapangan_usaha_kbli', $jumlahPenempatanKemnaker->lapangan_usaha_kbli ?? '') }}" required 
           class="form-input w-full" maxlength="255" placeholder="Kategori KBLI atau Deskripsi">
    @error('lapangan_usaha_kbli') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="status_disabilitas_penempatan" class="block text-sm font-medium text-gray-700 mb-1">Status Disabilitas <span class="text-red-500">*</span></label>
        <select name="status_disabilitas" id="status_disabilitas_penempatan" required class="form-input w-full pr-8">
            <option value="">Pilih Status</option>
            @foreach($statusDisabilitasOptions as $key => $value)
                <option value="{{ $key }}" {{ old('status_disabilitas', $jumlahPenempatanKemnaker->status_disabilitas ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('status_disabilitas') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div id="ragam_disabilitas_field_penempatan" class="{{ old('status_disabilitas', $jumlahPenempatanKemnaker->status_disabilitas ?? '2') == '1' ? '' : 'hidden' }}">
        <label for="ragam_disabilitas_penempatan" class="block text-sm font-medium text-gray-700 mb-1">Ragam Disabilitas</label>
        <select name="ragam_disabilitas" id="ragam_disabilitas_penempatan" class="form-input w-full pr-8">
            <option value="">Pilih Ragam (jika disabilitas)</option>
            @foreach($ragamDisabilitasOptions as $key => $value)
                <option value="{{ $key }}" {{ old('ragam_disabilitas', $jumlahPenempatanKemnaker->ragam_disabilitas ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('ragam_disabilitas') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="jumlah_penempatan" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Ditempatkan <span class="text-red-500">*</span></label>
    <input type="number" name="jumlah" id="jumlah_penempatan" value="{{ old('jumlah', $jumlahPenempatanKemnaker->jumlah ?? 0) }}" required 
           class="form-input w-full" min="0">
    @error('jumlah') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('binapenta.jumlah-penempatan-kemnaker.index') }}" 
       class="btn-secondary-outline">
        Batal
    </a>
    <button type="submit" 
            class="btn-primary">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusDisabilitasSelect = document.getElementById('status_disabilitas_penempatan');
    const ragamDisabilitasField = document.getElementById('ragam_disabilitas_field_penempatan');
    const ragamDisabilitasSelect = document.getElementById('ragam_disabilitas_penempatan');

    function toggleRagamDisabilitas() {
        if (!statusDisabilitasSelect || !ragamDisabilitasField || !ragamDisabilitasSelect) return;

        if (statusDisabilitasSelect.value == '1') { // Jika Ya Disabilitas
            ragamDisabilitasField.classList.remove('hidden');
            ragamDisabilitasSelect.required = true;
        } else {
            ragamDisabilitasField.classList.add('hidden');
            ragamDisabilitasSelect.required = false;
            ragamDisabilitasSelect.value = ''; // Reset value
        }
    }

    if (statusDisabilitasSelect) {
        statusDisabilitasSelect.addEventListener('change', toggleRagamDisabilitas);
        // Initial check on page load (for edit form or old input)
        toggleRagamDisabilitas();
    }
});
</script>
@endpush
