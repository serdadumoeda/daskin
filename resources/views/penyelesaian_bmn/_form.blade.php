@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_bmn" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_bmn" value="{{ old('tahun', $penyelesaianBmn->tahun ?? date('Y')) }}" required 
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_bmn" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_bmn" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $penyelesaianBmn->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="kode_satuan_kerja_bmn" class="block text-sm font-medium text-gray-700 mb-1">Satuan Kerja <span class="text-red-500">*</span></label>
    <select name="kode_satuan_kerja" id="kode_satuan_kerja_bmn" required class="form-input w-full pr-8">
        <option value="">Pilih Satuan Kerja</option>
        @foreach($satuanKerjas as $satker)
            <option value="{{ $satker->kode_sk }}" 
                    {{ old('kode_satuan_kerja', $penyelesaianBmn->kode_satuan_kerja ?? '') == $satker->kode_sk ? 'selected' : '' }}>
                {{ $satker->nama_satuan_kerja }}
            </option>
        @endforeach
    </select>
    @error('kode_satuan_kerja') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="status_penggunaan_aset_bmn" class="block text-sm font-medium text-gray-700 mb-1">Status Penggunaan Aset <span class="text-red-500">*</span></label>
        <select name="status_penggunaan_aset" id="status_penggunaan_aset_bmn" required class="form-input w-full pr-8">
            <option value="">Pilih Status</option>
            @foreach($statusPenggunaanOptions as $key => $value)
                <option value="{{ $key }}" {{ old('status_penggunaan_aset', $penyelesaianBmn->status_penggunaan_aset ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('status_penggunaan_aset') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div id="aset_digunakan_fields_bmn" class="{{ old('status_penggunaan_aset', $penyelesaianBmn->status_penggunaan_aset ?? '') == 1 ? '' : 'hidden' }}">
        <label for="status_aset_digunakan_bmn" class="block text-sm font-medium text-gray-700 mb-1">Status Aset Digunakan</label>
        <select name="status_aset_digunakan" id="status_aset_digunakan_bmn" class="form-input w-full pr-8">
            <option value="">Pilih Status</option>
            @foreach($statusAsetDigunakanOptions as $key => $value)
                <option value="{{ $key }}" {{ old('status_aset_digunakan', $penyelesaianBmn->status_aset_digunakan ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('status_aset_digunakan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6 {{ (old('status_penggunaan_aset', $penyelesaianBmn->status_penggunaan_aset ?? '') == 1 && old('status_aset_digunakan', $penyelesaianBmn->status_aset_digunakan ?? '') == 2) || (isset($penyelesaianBmn) && $penyelesaianBmn->status_penggunaan_aset == 1 && $penyelesaianBmn->status_aset_digunakan == 2) ? '' : 'hidden' }}" id="nup_field_bmn">
    <label for="nup_bmn" class="block text-sm font-medium text-gray-700 mb-1">NUP (Nomor Urut Pendaftaran)</label>
    <input type="text" name="nup" id="nup_bmn" value="{{ old('nup', $penyelesaianBmn->nup ?? '') }}" 
           class="form-input w-full" maxlength="255" placeholder="Masukkan NUP jika Belum PSP">
    @error('nup') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div>
        <label for="kuantitas_bmn" class="block text-sm font-medium text-gray-700 mb-1">Kuantitas <span class="text-red-500">*</span></label>
        <input type="number" name="kuantitas" id="kuantitas_bmn" value="{{ old('kuantitas', $penyelesaianBmn->kuantitas ?? 0) }}" required 
               class="form-input w-full" min="0">
        @error('kuantitas') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="nilai_aset_rp_bmn" class="block text-sm font-medium text-gray-700 mb-1">Nilai Aset (Rp) <span class="text-red-500">*</span></label>
        <input type="number" step="0.01" name="nilai_aset_rp" id="nilai_aset_rp_bmn" value="{{ old('nilai_aset_rp', $penyelesaianBmn->nilai_aset_rp ?? 0.00) }}" required 
               class="form-input w-full" min="0">
        @error('nilai_aset_rp') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="total_aset_rp_bmn" class="block text-sm font-medium text-gray-700 mb-1">Total Aset (Rp)</label>
        <input type="number" step="0.01" name="total_aset_rp" id="total_aset_rp_bmn" value="{{ old('total_aset_rp', $penyelesaianBmn->total_aset_rp ?? 0.00) }}" 
               class="form-input w-full bg-gray-100" readonly>
         <p class="mt-1 text-xs text-gray-500">Otomatis: Kuantitas x Nilai Aset (Rp). Atau isi manual jika perlu.</p>
        @error('total_aset_rp') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('sekretariat-jenderal.penyelesaian-bmn.index') }}" 
       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
        Batal
    </a>
    <button type="submit" 
            class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusPenggunaanAsetSelect = document.getElementById('status_penggunaan_aset_bmn');
    const asetDigunakanFieldsDiv = document.getElementById('aset_digunakan_fields_bmn');
    const statusAsetDigunakanSelect = document.getElementById('status_aset_digunakan_bmn');
    const nupFieldDiv = document.getElementById('nup_field_bmn');
    const nupInput = document.getElementById('nup_bmn');

    const kuantitasInput = document.getElementById('kuantitas_bmn');
    const nilaiAsetRpInput = document.getElementById('nilai_aset_rp_bmn');
    const totalAsetRpInput = document.getElementById('total_aset_rp_bmn');

    function toggleAsetDigunakanFields() {
        if (statusPenggunaanAsetSelect.value == '1') { // Aset Digunakan
            asetDigunakanFieldsDiv.classList.remove('hidden');
            // NUP field visibility depends on status_aset_digunakan
            toggleNupField();
        } else { // Aset Tidak Digunakan
            asetDigunakanFieldsDiv.classList.add('hidden');
            if(statusAsetDigunakanSelect) statusAsetDigunakanSelect.value = ''; // Reset
            nupFieldDiv.classList.add('hidden');
            if(nupInput) nupInput.value = ''; // Reset
            if(nupInput) nupInput.required = false;
        }
    }

    function toggleNupField() {
        if (statusPenggunaanAsetSelect.value == '1' && statusAsetDigunakanSelect && statusAsetDigunakanSelect.value == '2') { // Aset Digunakan & Belum PSP
            nupFieldDiv.classList.remove('hidden');
            if(nupInput) nupInput.required = true;
        } else {
            nupFieldDiv.classList.add('hidden');
            if(nupInput) nupInput.required = false;
            // if(nupInput) nupInput.value = ''; // Jangan reset NUP jika sudah ada dan hanya statusnya berubah
        }
    }

    function calculateTotalAset() {
        if (!kuantitasInput || !nilaiAsetRpInput || !totalAsetRpInput) return;
        const kuantitas = parseInt(kuantitasInput.value) || 0;
        const nilaiAset = parseFloat(nilaiAsetRpInput.value) || 0;
        totalAsetRpInput.value = (kuantitas * nilaiAset).toFixed(2);
    }

    if (statusPenggunaanAsetSelect) {
        statusPenggunaanAsetSelect.addEventListener('change', toggleAsetDigunakanFields);
        // Initial check
        toggleAsetDigunakanFields();
    }
    if (statusAsetDigunakanSelect) {
        statusAsetDigunakanSelect.addEventListener('change', toggleNupField);
        // Initial check
        toggleNupField();
    }

    if (kuantitasInput) kuantitasInput.addEventListener('input', calculateTotalAset);
    if (nilaiAsetRpInput) nilaiAsetRpInput.addEventListener('input', calculateTotalAset);
    // Initial calculation for total_aset_rp if values are present
    if (totalAsetRpInput && totalAsetRpInput.value === '0.00' && (kuantitasInput.value > 0 || nilaiAsetRpInput.value > 0) ) {
        calculateTotalAset();
    }


});
</script>
@endpush
