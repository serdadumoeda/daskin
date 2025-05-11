<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressTemuanBpk extends Model
{
    use HasFactory;

    protected $table = 'progress_temuan_bpk';

    protected $fillable = [
        'tahun',
        'bulan',
        'kode_unit_kerja_eselon_i',
        'kode_satuan_kerja',
        'temuan_administratif_kasus',
        'temuan_kerugian_negara_rp',
        'tindak_lanjut_administratif_kasus',
        'tindak_lanjut_kerugian_negara_rp',
        'persentase_tindak_lanjut_administratif',
        'persentase_tindak_lanjut_kerugian_negara',
    ];

    /**
     * Definisi tipe data untuk casting otomatis.
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'temuan_administratif_kasus' => 'integer',
        'temuan_kerugian_negara_rp' => 'decimal:2',
        'tindak_lanjut_administratif_kasus' => 'integer',
        'tindak_lanjut_kerugian_negara_rp' => 'decimal:2',
        'persentase_tindak_lanjut_administratif' => 'float',
        'persentase_tindak_lanjut_kerugian_negara' => 'float',
    ];

    /**
     * Relasi ke Unit Kerja Eselon I.
     */
    public function unitKerjaEselonI(): BelongsTo
    {
        return $this->belongsTo(UnitKerjaEselonI::class, 'kode_unit_kerja_eselon_i', 'kode_uke1');
    }

    /**
     * Relasi ke Satuan Kerja.
     */
    public function satuanKerja(): BelongsTo
    {
        return $this->belongsTo(SatuanKerja::class, 'kode_satuan_kerja', 'kode_sk');
    }

    // Opsional: Accessor untuk menghitung persentase jika tidak ingin menyimpannya langsung
    // atau untuk validasi/formatting.
    // Contoh:
    // public function getCalculatedPersentaseAdministratifAttribute(): float
    // {
    //     if ($this->temuan_administratif_kasus > 0) {
    //         return round(($this->tindak_lanjut_administratif_kasus / $this->temuan_administratif_kasus) * 100, 2);
    //     }
    //     return 0.0;
    // }
}