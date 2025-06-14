<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelaporanWlkpOnline extends Model
{
    use HasFactory;

    protected $table = 'pelaporan_wlkp_online';

    protected $fillable = [
        'tahun',
        'bulan',
        'provinsi',
        'kbli',
        'skala_perusahaan',
        'jumlah_perusahaan_melapor', // Pastikan ini adalah nama kolom yang benar
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'jumlah_perusahaan_melapor' => 'integer', // Pastikan ini adalah nama kolom yang benar
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
