@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_integrasi" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_integrasi" value="{{ old('tahun', $aplikasiIntegrasiSiapkerja->tahun ?? date('Y')) }}" required 
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_integrasi" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_integrasi" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $aplikasiIntegrasiSiapkerja->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="jenis_instansi_integrasi" class="block text-sm font-medium text-gray-700 mb-1">Jenis Instansi <span class="text-red-500">*</span></label>
    <select name="jenis_instansi" id="jenis_instansi_integrasi" required class="form-input w-full pr-8">
        <option value="">Pilih Jenis Instansi</option>
        @foreach($jenisInstansiOptions as $key => $value)
            <option value="{{ $key }}" {{ old('jenis_instansi', $aplikasiIntegrasiSiapkerja->jenis_instansi ?? '') == $key ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
    </select>
    @error('jenis_instansi') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="mb-6">
    <label for="nama_instansi_integrasi" class="block text-sm font-medium text-gray-700 mb-1">Nama Instansi <span class="text-red-500">*</span></label>
    <input type="text" name="nama_instansi" id="nama_instansi_integrasi" value="{{ old('nama_instansi', $aplikasiIntegrasiSiapkerja->nama_instansi ?? '') }}" required 
           class="form-input w-full" maxlength="255" placeholder="Contoh: Kementerian Koperasi dan UKM">
    @error('nama_instansi') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="mb-6">
    <label for="nama_aplikasi_website_integrasi" class="block text-sm font-medium text-gray-700 mb-1">Nama Aplikasi/Website <span class="text-red-500">*</span></label>
    <input type="text" name="nama_aplikasi_website" id="nama_aplikasi_website_integrasi" value="{{ old('nama_aplikasi_website', $aplikasiIntegrasiSiapkerja->nama_aplikasi_website ?? '') }}" required 
           class="form-input w-full" maxlength="255" placeholder="Contoh: SISKOP UKM">
    @error('nama_aplikasi_website') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="mb-6">
    <label for="status_integrasi_integrasi" class="block text-sm font-medium text-gray-700 mb-1">Status Integrasi <span class="text-red-500">*</span></label>
    <select name="status_integrasi" id="status_integrasi_integrasi" required class="form-input w-full pr-8">
        <option value="">Pilih Status</option>
        @foreach($statusIntegrasiOptions as $key => $value)
            <option value="{{ $key }}" {{ old('status_integrasi', $aplikasiIntegrasiSiapkerja->status_integrasi ?? '') == $key ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
    </select>
    @error('status_integrasi') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('barenbang.aplikasi-integrasi-siapkerja.index') }}" 
       class="btn-secondary-outline">
        Batal
    </a>
    <button type="submit" 
            class="btn-primary">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>
