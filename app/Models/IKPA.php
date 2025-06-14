<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IKPA extends Model
{
    use HasFactory;

    protected $table = 'ikpa';

    protected $fillable = [
        'tahun',
        'bulan',
        'kode_unit_kerja_eselon_i',
        'aspek_pelaksanaan_anggaran',
        'nilai_aspek',
        'konversi_bobot',
        'dispensasi_spm',
        'nilai_akhir'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'aspek_pelaksanaan_anggaran' => 'string',
        'nilai_aspek' => 'integer',
        'konversi_bobot' => 'integer',
        'dispensasi_spm' => 'integer',
        'nilai_akhir' => 'integer',
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
     * Mendapatkan teks untuk Aspek Pelaksanaan Anggaran.
     * Keterangan Aspek Pelaksanaan Anggaran:
     * 1: Kualitas Perencanaan Anggaran
     * 2: Kualitas Pelaksanaan Anggaran
     * 3: Kualitas Hasil Pelaksanaan Anggaran
     * 4: Total
     *
     * @return string
     */
    public function getAspekPelaksanaanAnggaranTextAttribute(): string
    {
        return match ($this->attributes['aspek_pelaksanaan_anggaran']) {
            1 => 'Kualitas Perencanaan Anggaran',
            2 => 'Kualitas Pelaksanaan Anggaran',
            3 => 'Kualitas Hasil Pelaksanaan Anggaran',
            4 => 'Total',
            default => 'Tidak Diketahui',
        };
    }
}
