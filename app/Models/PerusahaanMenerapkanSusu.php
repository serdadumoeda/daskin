<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerusahaanMenerapkanSusu extends Model
{
    use HasFactory;

    protected $table = 'perusahaan_menerapkan_susu';

    protected $fillable = [
        'tahun',
        'bulan',
        'provinsi',
        'kbli',
        'jumlah_perusahaan_susu', // Jumlah Perusahaan yang Menerapkan Struktur dan Skala Upah
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'jumlah_perusahaan_susu' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Tidak ada relasi foreign key ke tabel lain berdasarkan PDF untuk tabel ini
    // Jika Provinsi atau KBLI adalah tabel master, tambahkan relasi di sini.
}
