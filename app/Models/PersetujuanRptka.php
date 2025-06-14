<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersetujuanRptka extends Model
{
    use HasFactory;

    protected $table = 'persetujuan_rptka';

    protected $fillable = [
        'tahun',
        'bulan',
        'jenis_kelamin',
        'negara_asal',
        'jabatan',
        'lapangan_usaha_kbli', // Tetap di fillable
        'provinsi_penempatan',
        'status_pengajuan',
        'jumlah',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'jenis_kelamin' => 'integer',
        'jabatan' => 'integer',
        'lapangan_usaha_kbli' => 'string', // Diubah ke string
        'provinsi_penempatan' => 'string',
        'status_pengajuan' => 'integer',
        'jumlah' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public const JENIS_KELAMIN_OPTIONS = [
        1 => 'Laki-laki',
        2 => 'Perempuan',
    ];

    public const JABATAN_OPTIONS = [
        1 => 'Advisor/Consultant',
        2 => 'Direksi',
        3 => 'Komisaris',
        4 => 'Manager',
        5 => 'Profesional',
    ];

    // Hapus LAPANGAN_USAHA_KBLI_OPTIONS
    // public const LAPANGAN_USAHA_KBLI_OPTIONS = [ ... ];

    public const STATUS_PENGAJUAN_OPTIONS = [
        1 => 'Diterima',
        2 => 'Ditolak',
    ];

    public function getJenisKelaminTextAttribute(): string
    {
        return self::JENIS_KELAMIN_OPTIONS[$this->jenis_kelamin] ?? 'Tidak Diketahui';
    }

    public function getJabatanTextAttribute(): string
    {
        return self::JABATAN_OPTIONS[$this->jabatan] ?? 'Tidak Diketahui';
    }

    // Hapus getLapanganUsahaKbliTextAttribute()
    // public function getLapanganUsahaKbliTextAttribute(): string { ... }

    public function getStatusPengajuanTextAttribute(): string
    {
        return self::STATUS_PENGAJUAN_OPTIONS[$this->status_pengajuan] ?? 'Tidak Diketahui';
    }
}