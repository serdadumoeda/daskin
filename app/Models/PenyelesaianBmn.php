<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Tambahkan ini

class PenyelesaianBmn extends Model
{
    use HasFactory;

    protected $table = 'penyelesaian_bmn';

    protected $fillable = [
        'tahun',
        'bulan',
        'kode_satuan_kerja', // Diubah dari unit_kerja
        'jenis_bmn',
        'henti_guna',
        'status_penggunaan',
        'penetapan_status_penggunaan',
        'kuantitas',
        'nilai_aset',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        // kode_satuan_kerja adalah string, tidak perlu cast khusus
        'jenis_bmn' => 'integer',
        'henti_guna' => 'boolean',
        'status_penggunaan' => 'integer',
        'kuantitas' => 'integer',
        'nilai_aset' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public const JENIS_BMN_OPTIONS = [
        1 => 'Alat Angkutan Bermotor',
        2 => 'Aset Tak Berwujud',
        3 => 'Aset Tetap Lainnya',
        4 => 'Instalasi dan Jaringan',
        5 => 'Konstruksi Dalam Pengerjaan (KDP)',
        6 => 'Mesin Peralatan Khusus TIK',
        7 => 'Mesin Peralatan Khusus Non TIK',
    ];

    public const HENTI_GUNA_OPTIONS = [
        true => 'Ya',
        false => 'Tidak',
    ];

    public const STATUS_PENGGUNAAN_OPTIONS = [
        1 => 'Digunakan sendiri untuk operasional',
        2 => 'Digunakan sendiri untuk dinas jabatan',
    ];

    /**
     * Relasi ke Satuan Kerja.
     */
    public function satuanKerja(): BelongsTo
    {
        // Parameter kedua adalah foreign key di tabel penyelesaian_bmn (yaitu kode_satuan_kerja)
        // Parameter ketiga adalah owner key di tabel satuan_kerja (yaitu kode_sk)
        return $this->belongsTo(SatuanKerja::class, 'kode_satuan_kerja', 'kode_sk');
    }

    public function getJenisBmnTextAttribute(): string
    {
        return self::JENIS_BMN_OPTIONS[$this->jenis_bmn] ?? 'Tidak Diketahui';
    }

    public function getHentiGunaTextAttribute(): string
    {
        $key = is_bool($this->henti_guna) ? $this->henti_guna : (bool)$this->henti_guna;
        return self::HENTI_GUNA_OPTIONS[$key] ?? 'Tidak Diketahui';
    }

    public function getStatusPenggunaanTextAttribute(): string
    {
        return self::STATUS_PENGGUNAAN_OPTIONS[$this->status_penggunaan] ?? 'Tidak Diketahui';
    }
}