<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JumlahRegulasiBaru extends Model
{
    use HasFactory;

    protected $table = 'jumlah_regulasi_baru';

    protected $fillable = [
        'tahun',
        'bulan',
        'kode_satuan_kerja', // Foreign key ke tabel satuan_kerja
        'jenis_regulasi',    // 1: UU, 2: PP, 3: Permen, 4: Kepmen
        'jumlah_regulasi',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'jenis_regulasi' => 'integer',
        'jumlah_regulasi' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke Satuan Kerja.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function satuanKerja(): BelongsTo
    {
        return $this->belongsTo(SatuanKerja::class, 'kode_satuan_kerja', 'kode_sk');
    }

    /**
     * Mendapatkan teks untuk jenis regulasi.
     * Keterangan Jenis Regulasi:
     * 1: Undang-Undang
     * 2: Peraturan Pemerintah
     * 3: Permen (Peraturan Menteri)
     * 4: Kepmen (Keputusan Menteri)
     *
     * @return string
     */
    public function getJenisRegulasiTextAttribute(): string
    {
        return match ($this->jenis_regulasi) {
            1 => 'Undang-Undang',
            2 => 'Peraturan Pemerintah',
            3 => 'Permen',
            4 => 'Kepmen',
            default => 'Tidak Diketahui',
        };
    }
}
