<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LulusanPolteknakerBekerja extends Model
{
    use HasFactory;

    protected $table = 'lulusan_polteknaker_bekerja';

    protected $fillable = [
        'tahun',
        'bulan', // atau periode_lulusan jika lebih sesuai
        'program_studi', // 1: Relasi Industri, 2: K3, 3: MSDM
        'jumlah_lulusan',
        'jumlah_lulusan_bekerja',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'program_studi' => 'integer',
        'jumlah_lulusan' => 'integer',
        'jumlah_lulusan_bekerja' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Mendapatkan teks untuk Program Studi.
     * Keterangan Program Studi:
     * 1: Relasi Industri
     * 2: Keselamatan dan Kesehatan Kerja (K3)
     * 3: Manajemen Sumber Daya Manusia (MSDM)
     *
     * @return string
     */
    public function getProgramStudiTextAttribute(): string
    {
        return match ($this->attributes['program_studi']) {
            1 => 'Relasi Industri',
            2 => 'Keselamatan dan Kesehatan Kerja',
            3 => 'Manajemen Sumber Daya Manusia',
            default => 'Tidak Diketahui',
        };
    }
}
