<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JumlahPhk extends Model
{
    use HasFactory;

    protected $table = 'jumlah_phk';

    protected $fillable = [
        'tahun',
        'bulan',
        'provinsi',
        'kbli',
        // 'jumlah_perusahaan_phk', // Kolom (6) di PDF: Jumlah Perusahaan
        'jumlah_tk_phk',        // Kolom (7) di PDF: Jumlah Tenaga Kerja yang di PHK
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        // 'jumlah_perusahaan_phk' => 'integer',
        'jumlah_tk_phk' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Tidak ada relasi foreign key ke tabel lain berdasarkan PDF untuk tabel ini
    // Jika Provinsi atau KBLI adalah tabel master, tambahkan relasi di sini.
}
