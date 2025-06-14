<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SdmMengikutiPelatihan extends Model
{
    use HasFactory;

    protected $table = 'sdm_mengikuti_pelatihan';

    protected $fillable = [
        'tahun',
        'bulan',
        'kode_unit_kerja_eselon_i',
        'kode_satuan_kerja',
        'jenis_pelatihan', // 1: Diklat Dasar, 2: Diklat Kepemimpinan, 3: Diklat Fungsional
        'jumlah_peserta',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'jenis_pelatihan' => 'integer',
        'jumlah_peserta' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    /**
     * Mendapatkan teks untuk Jenis Pelatihan.
     * 1: Diklat Dasar
     * 2: Diklat Kepemimpinan
     * 3: Diklat Fungsional
     *
     * @return string
     */
    public function getJenisPelatihanTextAttribute(): string
    {
        return match ($this->attributes['jenis_pelatihan']) {
            1 => 'Diklat Dasar',
            2 => 'Diklat Kepemimpinan',
            3 => 'Diklat Fungsional',
            default => 'Tidak Diketahui',
        };
    }
}
