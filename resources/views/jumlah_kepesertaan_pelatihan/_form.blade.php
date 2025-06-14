@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_kepesertaan" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_kepesertaan" value="{{ old('tahun', $jumlahKepesertaanPelatihan->tahun ?? date('Y')) }}" required 
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_kepesertaan" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_kepesertaan" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $jumlahKepesertaanPelatihan->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="penyelenggara_pelatihan_kepesertaan" class="block text-sm font-medium text-gray-700 mb-1">Penyelenggara Pelatihan <span class="text-red-500">*</span></label>
        <select name="penyelenggara_pelatihan" id="penyelenggara_pelatihan_kepesertaan" required class="form-input w-full pr-8">
            <option value="">Pilih Penyelenggara</option>
            @foreach($penyelenggaraOptions as $key => $value)
                <option value="{{ $key }}" {{ old('penyelenggara_pelatihan', $jumlahKepesertaanPelatihan->penyelenggara_pelatihan ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('penyelenggara_pelatihan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="tipe_lembaga_kepesertaan" class="block text-sm font-medium text-gray-700 mb-1">Tipe Lembaga <span class="text-red-500">*</span></label>
        <select name="tipe_lembaga" id="tipe_lembaga_kepesertaan" required class="form-input w-full pr-8">
            <option value="">Pilih Tipe Lembaga</option>
            @foreach($tipeLembagaOptions as $key => $value)
                <option value="{{ $key }}" {{ old('tipe_lembaga', $jumlahKepesertaanPelatihan->tipe_lembaga ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('tipe_lembaga') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="jenis_kelamin_kepesertaan" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin Peserta <span class="text-red-500">*</span></label>
        <select name="jenis_kelamin" id="jenis_kelamin_kepesertaan" required class="form-input w-full pr-8">
            <option value="">Pilih Jenis Kelamin</option>
            @foreach($jenisKelaminOptions as $key => $value)
                <option value="{{ $key }}" {{ old('jenis_kelamin', $jumlahKepesertaanPelatihan->jenis_kelamin ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('jenis_kelamin') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="provinsi_tempat_pelatihan_kepesertaan" class="block text-sm font-medium text-gray-700 mb-1">Provinsi Tempat Pelatihan <span class="text-red-500">*</span></label>
        <input type="text" name="provinsi_tempat_pelatihan" id="provinsi_tempat_pelatihan_kepesertaan" value="{{ old('provinsi_tempat_pelatihan', $jumlahKepesertaanPelatihan->provinsi_tempat_pelatihan ?? '') }}" required 
               class="form-input w-full" maxlength="255" placeholder="Nama Provinsi">
        @error('provinsi_tempat_pelatihan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="kejuruan_kepesertaan" class="block text-sm font-medium text-gray-700 mb-1">Kejuruan <span class="text-red-500">*</span></label>
    <input type="text" name="kejuruan" id="kejuruan_kepesertaan" value="{{ old('kejuruan', $jumlahKepesertaanPelatihan->kejuruan ?? '') }}" required 
           class="form-input w-full" maxlength="255" placeholder="Nama Kejuruan">
    @error('kejuruan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="status_kelulusan_kepesertaan" class="block text-sm font-medium text-gray-700 mb-1">Status Kelulusan <span class="text-red-500">*</span></label>
        <select name="status_kelulusan" id="status_kelulusan_kepesertaan" required class="form-input w-full pr-8">
            <option value="">Pilih Status</option>
            @foreach($statusKelulusanOptions as $key => $value)
                <option value="{{ $key }}" {{ old('status_kelulusan', $jumlahKepesertaanPelatihan->status_kelulusan ?? '') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
        @error('status_kelulusan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="jumlah_kepesertaan_input" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Peserta <span class="text-red-500">*</span></label>
        <input type="number" name="jumlah" id="jumlah_kepesertaan_input" value="{{ old('jumlah', $jumlahKepesertaanPelatihan->jumlah ?? 0) }}" required 
               class="form-input w-full" min="0">
        @error('jumlah') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('binalavotas.jumlah-kepesertaan-pelatihan.index') }}" 
       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
        Batal
    </a>
    <button type="submit" 
            class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>
