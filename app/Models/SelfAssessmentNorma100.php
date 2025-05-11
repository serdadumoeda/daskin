<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelfAssessmentNorma100 extends Model
{
    use HasFactory;

    protected $table = 'self_assessment_norma100';

    protected $fillable = [
        'bulan', // Bulan pencatatan
        'tahun', // Tahun pencatatan
        'provinsi',
        'kbli',
        'skala_perusahaan', // Mikro, Kecil, Menengah, Besar
        'hasil_assessment',  // Rendah (<70), Sedang (71-90), Tinggi (91-100)
        'jumlah_perusahaan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'jumlah_perusahaan' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Tidak ada relasi foreign key ke tabel lain berdasarkan PDF untuk tabel ini
    // Jika Provinsi atau KBLI adalah tabel master, tambahkan relasi di sini.
}
