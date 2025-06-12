<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaduanPelanggaranNorma extends Model
{
    use HasFactory;

    protected $table = 'pengaduan_pelanggaran_norma';

    protected $fillable = [
        'tahun_tindak_lanjut',
        'bulan_tindak_lanjut',
        'jenis_tindak_lanjut',
        'jumlah_pengaduan_tindak_lanjut', // Sebelumnya 'Jumlah' di PDF
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun_tindak_lanjut' => 'integer', // Nullable
        'bulan_tindak_lanjut' => 'integer', // Nullable
        'jumlah_pengaduan_tindak_lanjut' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Tidak ada relasi foreign key ke tabel lain berdasarkan PDF untuk tabel ini
    // Jika Provinsi atau KBLI adalah tabel master, tambahkan relasi di sini.
}
