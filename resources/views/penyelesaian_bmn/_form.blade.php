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
    <label for="kode_satuan_kerja_bmn" class="block text-sm font-medium text-gray-700 mb-1">Unit/Satuan Kerja <span class="text-red-500">*</span></label>
    <select name="kode_satuan_kerja" id="kode_satuan_kerja_bmn" required class="form-input w-full pr-8">
        <option value="">Pilih Unit/Satuan Kerja</option>
        @foreach($satuanKerjas as $satker)
            <option value="{{ $satker->kode_sk }}" 
                    {{ old('kode_satuan_kerja', $penyelesaianBmn->kode_satuan_kerja ?? '') == $satker->kode_sk ? 'selected' : '' }}>
                {{ $satker->nama_satuan_kerja }} ({{ $satker->kode_sk }})
            </option>
        @endforeach
    </select>
    @error('kode_satuan_kerja') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div>
        <label for="jenis_bmn_bmn" class="block text-sm font-medium text-gray-700 mb-1">Jenis BMN <span class="text-red-500">*</span></label>
        <select name="jenis_bmn" id="jenis_bmn_bmn" required class="form-input w-full pr-8">
            <option value="">Pilih Jenis BMN</option>
            @foreach($jenisBmnOptions as $key => $value)
                <option value="{{ $key }}" {{ old('jenis_bmn', $penyelesaianBmn->jenis_bmn ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('jenis_bmn') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="henti_guna_bmn" class="block text-sm font-medium text-gray-700 mb-1">Henti Guna <span class="text-red-500">*</span></label>
        <select name="henti_guna" id="henti_guna_bmn" required class="form-input w-full pr-8">
            <option value="">Pilih Status Henti Guna</option>
            <option value="1" {{ old('henti_guna', isset($penyelesaianBmn->henti_guna) ? ($penyelesaianBmn->henti_guna ? '1' : '0') : '') == '1' ? 'selected' : '' }}>Ya</option>
            <option value="0" {{ old('henti_guna', isset($penyelesaianBmn->henti_guna) ? ($penyelesaianBmn->henti_guna ? '1' : '0') : '') == '0' ? 'selected' : '' }}>Tidak</option>
        </select>
        @error('henti_guna') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="status_penggunaan_bmn" class="block text-sm font-medium text-gray-700 mb-1">Status Penggunaan <span class="text-red-500">*</span></label>
        <select name="status_penggunaan" id="status_penggunaan_bmn" required class="form-input w-full pr-8">
            <option value="">Pilih Status Penggunaan</option>
            @foreach($statusPenggunaanOptions as $key => $value)
                <option value="{{ $key }}" {{ old('status_penggunaan', $penyelesaianBmn->status_penggunaan ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('status_penggunaan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="penetapan_status_penggunaan_bmn" class="block text-sm font-medium text-gray-700 mb-1">Penetapan Status Penggunaan</label>
    <input type="text" name="penetapan_status_penggunaan" id="penetapan_status_penggunaan_bmn" value="{{ old('penetapan_status_penggunaan', $penyelesaianBmn->penetapan_status_penggunaan ?? '') }}"
           class="form-input w-full" placeholder="Nomor surat/dokumen penetapan (jika ada)">
    @error('penetapan_status_penggunaan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="kuantitas_bmn" class="block text-sm font-medium text-gray-700 mb-1">Kuantitas <span class="text-red-500">*</span></label>
        <input type="number" name="kuantitas" id="kuantitas_bmn" value="{{ old('kuantitas', $penyelesaianBmn->kuantitas ?? 0) }}" required
               class="form-input w-full" min="0">
        @error('kuantitas') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="nilai_aset_bmn" class="block text-sm font-medium text-gray-700 mb-1">Nilai Aset (Rp) <span class="text-red-500">*</span></label>
        {{--
            Jika old('nilai_aset') ada, itu sudah dalam format '123456.78' karena sudah di-merge di controller.
            Jika tidak, kita format dari $penyelesaianBmn->nilai_aset.
            JavaScript akan menangani format tampilan saat input dan inisialisasi.
        --}}
        <input type="text" name="nilai_aset" id="nilai_aset_bmn" 
               value="{{ old('nilai_aset', $penyelesaianBmn->exists ? number_format($penyelesaianBmn->nilai_aset, 2, ',', '.') : '0,00') }}" 
               required class="form-input w-full" placeholder="Contoh: 1.500.000,75"
               oninput="this.value = formatAngkaInput(this.value)">
        @error('nilai_aset') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route($routeNamePrefix . 'index') }}"
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
    function formatAngkaDisplay(nilaiNumerik) {
        if (typeof nilaiNumerik === 'string') {
            nilaiNumerik = parseFloat(nilaiNumerik.replace(/\./g, '').replace(',', '.'));
        }
        if (isNaN(nilaiNumerik) || nilaiNumerik === null) {
            return '0,00';
        }
        return nilaiNumerik.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatAngkaInput(value) {
        if (!value) return '';
        // Izinkan hanya digit dan satu koma
        let rawValue = value.replace(/[^0-9,]/g, '');
        let parts = rawValue.split(',');

        // Format bagian integer dengan titik ribuan
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        
        if (parts.length > 1) {
            // Batasi desimal hingga 2 digit
            return parts[0] + ',' + parts[1].substring(0, 2);
        }
        return parts[0];
    }

    document.addEventListener('DOMContentLoaded', function() {
        const nilaiAsetInput = document.getElementById('nilai_aset_bmn');
        
        // Format nilai awal saat halaman load (termasuk dari old() atau model)
        if (nilaiAsetInput.value) {
            // Nilai dari old() atau model mungkin sudah string "123456.78" atau float
            // Kita perlu parsing dan format ulang untuk tampilan id-ID
            let currentValue = nilaiAsetInput.value;
            if (typeof currentValue === 'string' && currentValue.includes('.')) { 
                // Jika dari old() yang sudah diproses controller, formatnya "123456.78"
                // atau dari model jika belum di-format $penyelesaianBmn->nilai_aset
                 if (!currentValue.includes(',')){ // Pastikan bukan format id-ID yang salah masuk ke old()
                    currentValue = currentValue.replace('.', ','); // Anggap titik adalah desimal jika tidak ada koma
                 }
            }
             // Jika sudah ada koma, asumsikan format id-ID, bersihkan titik ribuan dulu
            currentValue = currentValue.replace(/\.(?=.*\d{3}(?:,|$))/g, ''); // Hapus titik ribuan

            nilaiAsetInput.value = formatAngkaInput(currentValue);
        }

        // Bersihkan format sebelum submit
        const form = nilaiAsetInput.closest('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (nilaiAsetInput && typeof nilaiAsetInput.value === 'string') {
                    // Ubah dari format '1.234.567,89' menjadi '1234567.89'
                    nilaiAsetInput.value = nilaiAsetInput.value.replace(/\./g, '').replace(',', '.');
                }
            });
        }
    });
</script>
@endpush