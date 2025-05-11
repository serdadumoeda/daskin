@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_kehadiran" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_kehadiran" value="{{ old('tahun', $persentaseKehadiran->tahun ?? date('Y')) }}" required 
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_kehadiran" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_kehadiran" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $persentaseKehadiran->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="kode_unit_kerja_eselon_i_kehadiran" class="block text-sm font-medium text-gray-700 mb-1">Unit Kerja Eselon I <span class="text-red-500">*</span></label>
        <select name="kode_unit_kerja_eselon_i" id="kode_unit_kerja_eselon_i_kehadiran" required class="form-input w-full pr-8">
            <option value="">Pilih Unit Kerja</option>
            @foreach($unitKerjaEselonIs as $unit)
                <option value="{{ $unit->kode_uke1 }}" 
                        {{ old('kode_unit_kerja_eselon_i', $persentaseKehadiran->kode_unit_kerja_eselon_i ?? '') == $unit->kode_uke1 ? 'selected' : '' }}>
                    {{ $unit->nama_unit_kerja_eselon_i }}
                </option>
            @endforeach
        </select>
        @error('kode_unit_kerja_eselon_i') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="kode_satuan_kerja_kehadiran" class="block text-sm font-medium text-gray-700 mb-1">Satuan Kerja <span class="text-red-500">*</span></label>
        <select name="kode_satuan_kerja" id="kode_satuan_kerja_kehadiran" required 
                data-old-value="{{ old('kode_satuan_kerja', $persentaseKehadiran->kode_satuan_kerja ?? '') }}"
                class="form-input w-full pr-8">
            <option value="">Pilih Satuan Kerja</option>
            {{-- Opsi diisi oleh JavaScript --}}
             @if(isset($persentaseKehadiran) && $persentaseKehadiran->kode_unit_kerja_eselon_i && isset($satuanKerjas))
                @foreach($satuanKerjas->where('kode_unit_kerja_eselon_i', $persentaseKehadiran->kode_unit_kerja_eselon_i) as $satker)
                     <option value="{{ $satker->kode_sk }}" {{ old('kode_satuan_kerja', $persentaseKehadiran->kode_satuan_kerja) == $satker->kode_sk ? 'selected' : '' }}>
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
        <label for="status_asn_kehadiran" class="block text-sm font-medium text-gray-700 mb-1">Status ASN <span class="text-red-500">*</span></label>
        <select name="status_asn" id="status_asn_kehadiran" required class="form-input w-full pr-8">
            <option value="">Pilih Status ASN</option>
            @foreach($statusAsnOptions as $key => $value)
                <option value="{{ $key }}" {{ old('status_asn', $persentaseKehadiran->status_asn ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('status_asn') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="status_kehadiran_kehadiran" class="block text-sm font-medium text-gray-700 mb-1">Status Kehadiran <span class="text-red-500">*</span></label>
        <select name="status_kehadiran" id="status_kehadiran_kehadiran" required class="form-input w-full pr-8">
            <option value="">Pilih Status Kehadiran</option>
            @foreach($statusKehadiranOptions as $key => $value)
                <option value="{{ $key }}" {{ old('status_kehadiran', $persentaseKehadiran->status_kehadiran ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('status_kehadiran') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="jumlah_orang_kehadiran" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Orang <span class="text-red-500">*</span></label>
    <input type="number" name="jumlah_orang" id="jumlah_orang_kehadiran" value="{{ old('jumlah_orang', $persentaseKehadiran->jumlah_orang ?? 0) }}" required 
           class="form-input w-full" min="0">
    @error('jumlah_orang') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('sekretariat-jenderal.persentase-kehadiran.index') }}" 
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
    const kodeUke1Select = document.getElementById('kode_unit_kerja_eselon_i_kehadiran');
    const kodeSatkerSelect = document.getElementById('kode_satuan_kerja_kehadiran');
    
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
});
</script>
@endpush
