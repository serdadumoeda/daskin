<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressTemuanInternal extends Model
{
    use HasFactory;

    protected $table = 'progress_temuan_internal';

    protected $fillable = [
        'tahun',
        'bulan',
        'kode_unit_kerja_eselon_i',
        'kode_satuan_kerja',
        'temuan_administratif_kasus',
        'temuan_kerugian_negara_rp',
        'tindak_lanjut_administratif_kasus',
        'tindak_lanjut_kerugian_negara_rp',
        'persentase_tindak_lanjut_administratif', // Dihitung otomatis
        'persentase_tindak_lanjut_kerugian_negara', // Dihitung otomatis
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke Unit Kerja Eselon I.
     */
    public function unitKerjaEselonI(): BelongsTo
    {
        // Pastikan foreignKey dan ownerKey sudah benar sesuai skema tabel Anda
        // Jika primary key di unit_kerja_eselon_i adalah 'kode_uke1' (string)
        return $this->belongsTo(UnitKerjaEselonI::class, 'kode_unit_kerja_eselon_i', 'kode_uke1');
    }

    /**
     * Relasi ke Satuan Kerja.
     */
    public function satuanKerja(): BelongsTo
    {
        // Pastikan foreignKey dan ownerKey sudah benar sesuai skema tabel Anda
        // Jika primary key di satuan_kerja adalah 'kode_sk' (string)
        return $this->belongsTo(SatuanKerja::class, 'kode_satuan_kerja', 'kode_sk');
    }
}
