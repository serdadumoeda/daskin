<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerapanSmk3 extends Model
{
    use HasFactory;

    protected $table = 'penerapan_smk3';

    protected $fillable = [
        'tahun',
        'bulan',
        'provinsi',
        'kbli',
        'kategori_penilaian', // awal, transisi, lanjutan
        'tingkat_pencapaian', // baik, memuaskan
        'jenis_penghargaan',  // sertifikat emas, sertifikat emas dan bendera emas, dst.
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
    // Accessors untuk teks bisa ditambahkan jika nilai di DB adalah kode,
    // tapi berdasarkan PDF, ini adalah string.
}
