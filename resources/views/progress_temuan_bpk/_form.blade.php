@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_bpk" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_bpk" value="{{ old('tahun', $progressTemuanBpk->tahun ?? date('Y')) }}" required 
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_bpk" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_bpk" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $progressTemuanBpk->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="kode_unit_kerja_eselon_i_bpk" class="block text-sm font-medium text-gray-700 mb-1">Unit Kerja Eselon I <span class="text-red-500">*</span></label>
        <select name="kode_unit_kerja_eselon_i" id="kode_unit_kerja_eselon_i_bpk" required class="form-input w-full pr-8">
            <option value="">Pilih Unit Kerja</option>
            @foreach($unitKerjaEselonIs as $unit)
                <option value="{{ $unit->kode_uke1 }}" 
                        data-nama="{{ $unit->nama_unit_kerja_eselon_i }}"
                        {{ old('kode_unit_kerja_eselon_i', $progressTemuanBpk->kode_unit_kerja_eselon_i ?? '') == $unit->kode_uke1 ? 'selected' : '' }}>
                    {{ $unit->nama_unit_kerja_eselon_i }}
                </option>
            @endforeach
        </select>
        @error('kode_unit_kerja_eselon_i') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="kode_satuan_kerja_bpk" class="block text-sm font-medium text-gray-700 mb-1">Satuan Kerja <span class="text-red-500">*</span></label>
        <select name="kode_satuan_kerja" id="kode_satuan_kerja_bpk" required 
                data-old-value="{{ old('kode_satuan_kerja', $progressTemuanBpk->kode_satuan_kerja ?? '') }}"
                class="form-input w-full pr-8">
            <option value="">Pilih Satuan Kerja</option>
            @if(isset($progressTemuanBpk) && $progressTemuanBpk->kode_unit_kerja_eselon_i && isset($satuanKerjas))
                @foreach($satuanKerjas->where('kode_unit_kerja_eselon_i', $progressTemuanBpk->kode_unit_kerja_eselon_i) as $satker)
                     <option value="{{ $satker->kode_sk }}" {{ old('kode_satuan_kerja', $progressTemuanBpk->kode_satuan_kerja) == $satker->kode_sk ? 'selected' : '' }}>
                        {{ $satker->nama_satuan_kerja }}
                    </option>
                @endforeach
            @elseif(old('kode_unit_kerja_eselon_i') && isset($satuanKerjas))
                @foreach($satuanKerjas->where('kode_unit_kerja_eselon_i', old('kode_unit_kerja_eselon_i')) as $satker)
                     <option value="{{ $satker->kode_sk }}" {{ old('kode_satuan_kerja') == $satker->kode_sk ? 'selected' : '' }}>
                        {{ $satker->nama_satuan_kerja }}
                    </option>
                @endforeach
            @endif
        </select>
        @error('kode_satuan_kerja') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="temuan_administratif_kasus_bpk" class="block text-sm font-medium text-gray-700 mb-1">Temuan Administratif (Kasus) <span class="text-red-500">*</span></label>
        <input type="number" name="temuan_administratif_kasus" id="temuan_administratif_kasus_bpk" value="{{ old('temuan_administratif_kasus', $progressTemuanBpk->temuan_administratif_kasus ?? 0) }}" required 
               class="form-input w-full" min="0">
        @error('temuan_administratif_kasus') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="temuan_kerugian_negara_rp_bpk" class="block text-sm font-medium text-gray-700 mb-1">Temuan Kerugian Negara (Rp) <span class="text-red-500">*</span></label>
        <input type="number" step="0.01" name="temuan_kerugian_negara_rp" id="temuan_kerugian_negara_rp_bpk" value="{{ old('temuan_kerugian_negara_rp', $progressTemuanBpk->temuan_kerugian_negara_rp ?? 0.00) }}" required 
               class="form-input w-full" min="0">
        @error('temuan_kerugian_negara_rp') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tindak_lanjut_administratif_kasus_bpk" class="block text-sm font-medium text-gray-700 mb-1">Tindak Lanjut Administratif (Kasus) <span class="text-red-500">*</span></label>
        <input type="number" name="tindak_lanjut_administratif_kasus" id="tindak_lanjut_administratif_kasus_bpk" value="{{ old('tindak_lanjut_administratif_kasus', $progressTemuanBpk->tindak_lanjut_administratif_kasus ?? 0) }}" required 
               class="form-input w-full" min="0">
        @error('tindak_lanjut_administratif_kasus') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="tindak_lanjut_kerugian_negara_rp_bpk" class="block text-sm font-medium text-gray-700 mb-1">Tindak Lanjut Kerugian Negara (Rp) <span class="text-red-500">*</span></label>
        <input type="number" step="0.01" name="tindak_lanjut_kerugian_negara_rp" id="tindak_lanjut_kerugian_negara_rp_bpk" value="{{ old('tindak_lanjut_kerugian_negara_rp', $progressTemuanBpk->tindak_lanjut_kerugian_negara_rp ?? 0.00) }}" required 
               class="form-input w-full" min="0">
        @error('tindak_lanjut_kerugian_negara_rp') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="persentase_tindak_lanjut_administratif_bpk" class="block text-sm font-medium text-gray-700 mb-1">Persentase Tindak Lanjut Administratif (%)</label>
        <input type="number" step="0.01" name="persentase_tindak_lanjut_administratif" id="persentase_tindak_lanjut_administratif_bpk" 
               value="{{ old('persentase_tindak_lanjut_administratif', $progressTemuanBpk->persentase_tindak_lanjut_administratif ?? 0.00) }}" readonly 
               class="form-input w-full bg-gray-100">
        <p class="mt-1 text-xs text-gray-500">Dihitung otomatis.</p>
    </div>
    <div>
        <label for="persentase_tindak_lanjut_kerugian_negara_bpk" class="block text-sm font-medium text-gray-700 mb-1">Persentase Tindak Lanjut Kerugian Negara (%)</label>
        <input type="number" step="0.01" name="persentase_tindak_lanjut_kerugian_negara" id="persentase_tindak_lanjut_kerugian_negara_bpk" 
               value="{{ old('persentase_tindak_lanjut_kerugian_negara', $progressTemuanBpk->persentase_tindak_lanjut_kerugian_negara ?? 0.00) }}" readonly 
               class="form-input w-full bg-gray-100">
        <p class="mt-1 text-xs text-gray-500">Dihitung otomatis.</p>
    </div>
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('inspektorat.progress-temuan-bpk.index') }}" 
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
    const kodeUke1Select = document.getElementById('kode_unit_kerja_eselon_i_bpk'); 
    const kodeSatkerSelect = document.getElementById('kode_satuan_kerja_bpk'); 
    
    function populateSatkerDropdown(satkerData, selectedSatkerValue) {
        if (!kodeSatkerSelect) return;
        while (kodeSatkerSelect.options.length > 1) { 
            kodeSatkerSelect.remove(1);
        }
        if (satkerData) {
            for (const [kode_sk, nama_satuan_kerja] of Object.entries(satkerData)) {
                const option = new Option(nama_satuan_kerja, kode_sk);
                kodeSatkerSelect.add(option);
            }
            if (selectedSatkerValue) {
                kodeSatkerSelect.value = selectedSatkerValue;
            }
        }
    }

    if (kodeUke1Select) {
        kodeUke1Select.addEventListener('change', function() {
            const selectedUke1 = this.value;
            if (!kodeSatkerSelect) return;
            while (kodeSatkerSelect.options.length > 1) {
                kodeSatkerSelect.remove(1);
            }
            kodeSatkerSelect.value = ""; 

            if (selectedUke1) {
                fetch(`/get-satuan-kerja/${selectedUke1}`)
                    .then(response => response.ok ? response.json() : Promise.reject('Network error'))
                    .then(data => populateSatkerDropdown(data, null))
                    .catch(error => console.error('Error fetching satuan kerja:', error));
            }
        });

        const initialUke1 = kodeUke1Select.value;
        const initialSatker = kodeSatkerSelect.dataset.oldValue;

        if (initialUke1) { 
            fetch(`/get-satuan-kerja/${initialUke1}`)
                .then(response => response.ok ? response.json() : Promise.reject('Network error'))
                .then(data => populateSatkerDropdown(data, initialSatker))
                .catch(error => console.error('Error initial fetching satuan kerja:', error));
        }
    }

    const temuanAdminKasusInput = document.getElementById('temuan_administratif_kasus_bpk');
    const tindakLanjutAdminKasusInput = document.getElementById('tindak_lanjut_administratif_kasus_bpk');
    const persentaseAdminOutput = document.getElementById('persentase_tindak_lanjut_administratif_bpk');
    const temuanKerugianRpInput = document.getElementById('temuan_kerugian_negara_rp_bpk');
    const tindakLanjutKerugianRpInput = document.getElementById('tindak_lanjut_kerugian_negara_rp_bpk');
    const persentaseKerugianOutput = document.getElementById('persentase_tindak_lanjut_kerugian_negara_bpk');

    function calculateAndDisplayPercentages() {
        if(!temuanAdminKasusInput || !tindakLanjutAdminKasusInput || !persentaseAdminOutput ||
           !temuanKerugianRpInput || !tindakLanjutKerugianRpInput || !persentaseKerugianOutput) {
            return; 
        }
        const temuanAdmin = parseInt(temuanAdminKasusInput.value) || 0;
        const tindakLanjutAdmin = parseInt(tindakLanjutAdminKasusInput.value) || 0;
        persentaseAdminOutput.value = (temuanAdmin > 0) ? Math.min(100, (tindakLanjutAdmin / temuanAdmin) * 100).toFixed(2) : (0).toFixed(2);

        const temuanKerugian = parseFloat(temuanKerugianRpInput.value) || 0;
        const tindakLanjutKerugian = parseFloat(tindakLanjutKerugianRpInput.value) || 0;
        persentaseKerugianOutput.value = (temuanKerugian > 0) ? Math.min(100, (tindakLanjutKerugian / temuanKerugian) * 100).toFixed(2) : (0).toFixed(2);
    }

    [temuanAdminKasusInput, tindakLanjutAdminKasusInput, temuanKerugianRpInput, tindakLanjutKerugianRpInput].forEach(input => {
        if(input) input.addEventListener('input', calculateAndDisplayPercentages);
    });
    calculateAndDisplayPercentages(); 
});
</script>
@endpush
