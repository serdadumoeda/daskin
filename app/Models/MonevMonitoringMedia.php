<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonevMonitoringMedia extends Model
{
    use HasFactory;

    protected $table = 'monev_monitoring_media';

    protected $fillable = [
        'tahun',
        'bulan',
        'jenis_media',      // 1: Media Cetak, 2: Media Online, 3: Media Elektronik
        'sentimen_publik',  // 1: Sentimen Positif, 2: Sentimen Negatif
        'jumlah_berita',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'jenis_media' => 'integer',
        'sentimen_publik' => 'integer',
        'jumlah_berita' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Mendapatkan teks untuk Jenis Media.
     * 1: Media Cetak, 2: Media Online, 3: Media Elektronik
     *
     * @return string
     */
    public function getJenisMediaTextAttribute(): string
    {
        return match ($this->attributes['jenis_media']) {
            1 => 'Media Cetak',
            2 => 'Media Online',
            3 => 'Media Elektronik',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Mendapatkan teks untuk Sentimen Publik.
     * 1: Sentimen Positif
     * 2: Sentimen Negatif
     *
     * @return string
     */
    public function getSentimenPublikTextAttribute(): string
    {
        return match ($this->attributes['sentimen_publik']) {
            1 => 'Positif',
            2 => 'Negatif',
            default => 'Tidak Diketahui',
        };
    }
}
