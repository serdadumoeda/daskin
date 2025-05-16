<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// BelongsTo tidak lagi digunakan untuk SatuanKerja secara langsung di sini
// use Illuminate\Database\Eloquent\Relations\BelongsTo; 

class JumlahPenangananKasus extends Model
{
    use HasFactory;

    protected $table = 'jumlah_penanganan_kasus';

    protected $fillable = [
        'tahun',
        'bulan',
        'substansi', // Menggantikan kode_satuan_kerja
        'jenis_perkara',
        'jumlah_perkara',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'jumlah_perkara' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // 'substansi' akan menjadi string by default, tidak perlu cast khusus kecuali ada kebutuhan lain
    ];

    /**
     * Relasi ke Satuan Kerja dihapus karena kolom kode_satuan_kerja diganti substansi (string).
     * Jika 'substansi' nantinya merujuk ke tabel lain, relasi baru bisa ditambahkan di sini.
     */
    // public function satuanKerja(): BelongsTo
    // {
    //     return $this->belongsTo(SatuanKerja::class, 'kode_satuan_kerja', 'kode_sk');
    // }
}