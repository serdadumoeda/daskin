<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenyelesaianBmn extends Model
{
    use HasFactory;

    protected $table = 'penyelesaian_bmn';

    protected $fillable = [
        'tahun',
        'bulan',
        'kode_satuan_kerja',
        'status_penggunaan_aset',
        'status_aset_digunakan',
        'nup',
        'kuantitas',
        'nilai_aset_rp',
        'total_aset_rp',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'status_penggunaan_aset' => 'integer',
        'status_aset_digunakan' => 'integer',
        'kuantitas' => 'integer',
        'nilai_aset_rp' => 'decimal:2',
        'total_aset_rp' => 'decimal:2',
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
     * Mendapatkan teks untuk Status Penggunaan Aset.
     * 1: Aset Digunakan
     * 2: Aset Tetap yang Tidak Digunakan dalam Operasional Pemerintah
     *
     * @return string
     */
    public function getStatusPenggunaanAsetTextAttribute(): string
    {
        return match ($this->attributes['status_penggunaan_aset']) {
            1 => 'Aset Digunakan',
            2 => 'Aset Tetap Tidak Digunakan',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Mendapatkan teks untuk Status Aset Digunakan.
     * Hanya relevan jika status_penggunaan_aset = 1
     * 1: Sudah PSP
     * 2: Belum PSP
     *
     * @return string|null
     */
    public function getStatusAsetDigunakanTextAttribute(): ?string
    {
        if ($this->attributes['status_penggunaan_aset'] != 1) {
            return null;
        }
        return match ($this->attributes['status_aset_digunakan']) {
            1 => 'Sudah PSP',
            2 => 'Belum PSP',
            default => null,
        };
    }
}
