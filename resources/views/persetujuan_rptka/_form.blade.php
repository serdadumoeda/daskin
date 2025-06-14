@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_rptka" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_rptka" value="{{ old('tahun', $persetujuanRptka->tahun ?? date('Y')) }}" required
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_rptka" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_rptka" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $persetujuanRptka->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="jenis_kelamin_rptka" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
        <select name="jenis_kelamin" id="jenis_kelamin_rptka" required class="form-input w-full pr-8">
            <option value="">Pilih Jenis Kelamin</option>
            @foreach($options['jenisKelaminOptions'] as $key => $value)
                <option value="{{ $key }}" {{ old('jenis_kelamin', $persetujuanRptka->jenis_kelamin ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('jenis_kelamin') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="negara_asal_rptka" class="block text-sm font-medium text-gray-700 mb-1">Negara Asal <span class="text-red-500">*</span></label>
        <input type="text" name="negara_asal" id="negara_asal_rptka" value="{{ old('negara_asal', $persetujuanRptka->negara_asal ?? '') }}" required
               class="form-input w-full" maxlength="100" placeholder="Masukkan negara asal TKA">
        @error('negara_asal') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="jabatan_rptka" class="block text-sm font-medium text-gray-700 mb-1">Jabatan <span class="text-red-500">*</span></label>
    <select name="jabatan" id="jabatan_rptka" required class="form-input w-full pr-8">
        <option value="">Pilih Jabatan</option>
        @foreach($options['jabatanOptions'] as $key => $value)
            <option value="{{ $key }}" {{ old('jabatan', $persetujuanRptka->jabatan ?? '') == $key ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
    </select>
    @error('jabatan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

{{-- Mengubah Lapangan Usaha (KBLI) menjadi input teks --}}
<div class="mb-6">
    <label for="lapangan_usaha_kbli_rptka" class="block text-sm font-medium text-gray-700 mb-1">Lapangan Usaha (KBLI) <span class="text-red-500">*</span></label>
    <input type="text" name="lapangan_usaha_kbli" id="lapangan_usaha_kbli_rptka" 
           value="{{ old('lapangan_usaha_kbli', $persetujuanRptka->lapangan_usaha_kbli ?? '') }}" required
           class="form-input w-full" maxlength="255" placeholder="Masukkan deskripsi Lapangan Usaha (KBLI)">
    @error('lapangan_usaha_kbli') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="mb-6">
    <label for="provinsi_penempatan_rptka" class="block text-sm font-medium text-gray-700 mb-1">Provinsi Penempatan <span class="text-red-500">*</span></label>
    <input type="text" name="provinsi_penempatan" id="provinsi_penempatan_rptka" 
           value="{{ old('provinsi_penempatan', $persetujuanRptka->provinsi_penempatan ?? '') }}" required
           class="form-input w-full" maxlength="100" placeholder="Masukkan provinsi atau 'Lintas Provinsi'">
    @error('provinsi_penempatan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="status_pengajuan_rptka" class="block text-sm font-medium text-gray-700 mb-1">Status Pengajuan RPTKA <span class="text-red-500">*</span></label>
        <select name="status_pengajuan" id="status_pengajuan_rptka" required class="form-input w-full pr-8">
            <option value="">Pilih Status Pengajuan</option>
            @foreach($options['statusPengajuanOptions'] as $key => $value)
                <option value="{{ $key }}" {{ old('status_pengajuan', $persetujuanRptka->status_pengajuan ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('status_pengajuan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="jumlah_rptka" class="block text-sm font-medium text-gray-700 mb-1">Jumlah <span class="text-red-500">*</span></label>
        <input type="number" name="jumlah" id="jumlah_rptka" value="{{ old('jumlah', $persetujuanRptka->jumlah ?? 0) }}" required
               class="form-input w-full" min="0">
        @error('jumlah') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
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