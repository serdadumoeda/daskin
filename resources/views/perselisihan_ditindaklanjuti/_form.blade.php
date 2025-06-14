@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_perselisihan" class="block text-sm font-medium text-gray-700 mb-1">Tahun Pengaduan <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_perselisihan" value="{{ old('tahun', $perselisihanDitindaklanjuti->tahun ?? date('Y')) }}" required
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_perselisihan" class="block text-sm font-medium text-gray-700 mb-1">Bulan Pengaduan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_perselisihan" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $perselisihanDitindaklanjuti->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

{{-- Kolom untuk tahun dan bulan tindak lanjut tidak ada di tabel ini berdasarkan PDF --}}

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="provinsi_perselisihan" class="block text-sm font-medium text-gray-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
        <input type="text" name="provinsi" id="provinsi_perselisihan" value="{{ old('provinsi', $perselisihanDitindaklanjuti->provinsi ?? '') }}" required
               class="form-input w-full" maxlength="255" placeholder="Nama Provinsi">
        @error('provinsi') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="jenis_perselisihan_perselisihan" class="block text-sm font-medium text-gray-700 mb-1">Jenis Perselisihan <span class="text-red-500">*</span></label>
        <select name="jenis_perselisihan" id="jenis_perselisihan_perselisihan" required class="form-input w-full pr-8">
            <option value="">Pilih Jenis Perselisihan</option>
            @foreach($jenisPerselisihanOptions as $key => $value)
                <option value="{{ $key }}" {{ old('jenis_perselisihan', $perselisihanDitindaklanjuti->jenis_perselisihan ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('jenis_perselisihan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="cara_penyelesaian_perselisihan" class="block text-sm font-medium text-gray-700 mb-1">Cara Penyelesaian <span class="text-red-500">*</span></label>
        <select name="cara_penyelesaian" id="cara_penyelesaian_perselisihan" required class="form-input w-full pr-8">
            <option value="">Pilih Cara Penyelesaian</option>
             @foreach($caraPenyelesaianOptions as $key => $value)
                <option value="{{ $key }}" {{ old('cara_penyelesaian', $perselisihanDitindaklanjuti->cara_penyelesaian ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('cara_penyelesaian') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="jumlah_perselisihan_input" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Perselisihan <span class="text-red-500">*</span></label>
        <input type="number" name="jumlah_perselisihan" id="jumlah_perselisihan_input" value="{{ old('jumlah_perselisihan', $perselisihanDitindaklanjuti->jumlah_perselisihan ?? 0) }}" required
               class="form-input w-full" min="0">
        @error('jumlah_perselisihan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="jumlah_ditindaklanjuti_input" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Penyelesaian Perselisihan <span class="text-red-500">*</span></label>
        <input type="number" name="jumlah_ditindaklanjuti" id="jumlah_ditindaklanjuti_input" value="{{ old('jumlah_ditindaklanjuti', $perselisihanDitindaklanjuti->jumlah_ditindaklanjuti ?? 0) }}" required
               class="form-input w-full" min="0">
        @error('jumlah_ditindaklanjuti') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('phi.perselisihan-ditindaklanjuti.index') }}"
       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
        Batal
    </a>
    <button type="submit"
            class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>
