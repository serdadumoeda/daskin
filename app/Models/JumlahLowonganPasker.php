<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JumlahLowonganPasker extends Model
{
    use HasFactory;

    protected $table = 'jumlah_lowongan_pasker';

    protected $fillable = [
        'tahun',
        'bulan',
        'jenis_kelamin',
        'provinsi_penempatan',
        'lapangan_usaha_kbli',
        'status_disabilitas',
        'jumlah_lowongan', // Sesuai dengan controller sebelumnya
    ];

    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'jenis_kelamin' => 'integer',
        'provinsi_penempatan' => 'string',
        'lapangan_usaha_kbli' => 'string',
        'status_disabilitas' => 'integer',
        'jumlah_lowongan' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public const JENIS_KELAMIN_OPTIONS = [
        1 => 'Laki-laki',
        2 => 'Perempuan',
    ];

    public const STATUS_DISABILITAS_OPTIONS = [
        1 => 'Ya', // Disabilitas
        2 => 'Tidak', // Non-Disabilitas
    ];

    // Accessors
    public function getJenisKelaminTextAttribute(): string
    {
        return self::JENIS_KELAMIN_OPTIONS[$this->jenis_kelamin] ?? 'Tidak Diketahui';
    }

    public function getStatusDisabilitasTextAttribute(): string
    {
        return self::STATUS_DISABILITAS_OPTIONS[$this->status_disabilitas] ?? 'Tidak Diketahui';
    }
}