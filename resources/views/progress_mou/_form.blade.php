@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tahun_mou" class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
        <input type="number" name="tahun" id="tahun_mou" value="{{ old('tahun', $progressMou->tahun ?? date('Y')) }}" required 
               class="form-input w-full" min="2000" max="{{ date('Y') + 5 }}">
        @error('tahun') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="bulan_mou" class="block text-sm font-medium text-gray-700 mb-1">Bulan <span class="text-red-500">*</span></label>
        <select name="bulan" id="bulan_mou" required class="form-input w-full pr-8">
            @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ old('bulan', $progressMou->bulan ?? '') == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                </option>
            @endfor
        </select>
        @error('bulan') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="judul_mou_input" class="block text-sm font-medium text-gray-700 mb-1">Judul MoU <span class="text-red-500">*</span></label>
    <textarea name="judul_mou" id="judul_mou_input" rows="3" required 
              class="form-input w-full">{{ old('judul_mou', $progressMou->judul_mou ?? '') }}</textarea>
    @error('judul_mou') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="tanggal_mulai_perjanjian_mou" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Perjanjian <span class="text-red-500">*</span></label>
        <input type="date" name="tanggal_mulai_perjanjian" id="tanggal_mulai_perjanjian_mou" 
               value="{{ old('tanggal_mulai_perjanjian', isset($progressMou->tanggal_mulai_perjanjian) ? ($progressMou->tanggal_mulai_perjanjian instanceof \Carbon\Carbon ? $progressMou->tanggal_mulai_perjanjian->format('Y-m-d') : $progressMou->tanggal_mulai_perjanjian) : '') }}" required 
               class="form-input w-full">
        @error('tanggal_mulai_perjanjian') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="tanggal_selesai_perjanjian_mou" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai Perjanjian</label>
        <input type="date" name="tanggal_selesai_perjanjian" id="tanggal_selesai_perjanjian_mou" 
               value="{{ old('tanggal_selesai_perjanjian', isset($progressMou->tanggal_selesai_perjanjian) ? ($progressMou->tanggal_selesai_perjanjian instanceof \Carbon\Carbon ? $progressMou->tanggal_selesai_perjanjian->format('Y-m-d') : $progressMou->tanggal_selesai_perjanjian) : '') }}" 
               class="form-input w-full">
        @error('tanggal_selesai_perjanjian') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
</div>

<div class="mb-6">
    <label for="pihak_terlibat_mou" class="block text-sm font-medium text-gray-700 mb-1">Pihak Terlibat</label>
    <textarea name="pihak_terlibat" id="pihak_terlibat_mou" rows="3" 
              class="form-input w-full">{{ old('pihak_terlibat', $progressMou->pihak_terlibat ?? '') }}</textarea>
    @error('pihak_terlibat') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>

<div class="flex justify-end space-x-3 mt-8">
    <a href="{{ route('sekretariat-jenderal.progress-mou.index') }}" 
       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-button hover:bg-gray-300 text-sm font-medium">
        Batal
    </a>
    <button type="submit" 
            class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 text-sm font-medium">
        <i class="ri-save-line mr-1"></i> Simpan
    </button>
</div>

@push('styles')
<style>
    /* Styling tambahan untuk input date agar lebih konsisten jika browser defaultnya kurang baik */
    input[type="date"]::-webkit-calendar-picker-indicator {
        opacity: 0.5;
        cursor: pointer;
    }
    input[type="date"] {
        position: relative;
    }
    /* Untuk Firefox, agar teks tidak terpotong */
    input[type="date"]::-moz-calendar-picker-indicator {
        /* Tidak bisa di-style langsung, tapi pastikan padding cukup */
    }
</style>
@endpush
