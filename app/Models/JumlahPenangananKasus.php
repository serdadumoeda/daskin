<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JumlahPenangananKasus extends Model
{
    use HasFactory;

    protected $table = 'jumlah_penanganan_kasus';

    protected $fillable = [
        'tahun',
        'bulan',
        'kode_satuan_kerja', // Foreign key ke tabel satuan_kerja
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
}
