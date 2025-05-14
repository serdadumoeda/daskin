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
        'provinsi_perusahaan',
        'lapangan_usaha_kbli',
        'jabatan',
        'jenis_kelamin_dibutuhkan',
        'status_disabilitas_dibutuhkan',
        'jumlah_lowongan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'jenis_kelamin_dibutuhkan' => 'integer',
        'status_disabilitas_dibutuhkan' => 'integer',
        'jumlah_lowongan' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Accessor untuk Jenis Kelamin yang Dibutuhkan
    public function getJenisKelaminDibutuhkanTextAttribute(): string
    {
        return match ($this->attributes['jenis_kelamin_dibutuhkan']) {
            1 => 'Laki-laki',
            2 => 'Perempuan',
            3 => 'Laki-laki/Perempuan',
            default => 'Tidak Diketahui',
        };
    }

    // Accessor untuk Status Disabilitas yang Dibutuhkan
    public function getStatusDisabilitasDibutuhkanTextAttribute(): string
    {
        return match ($this->attributes['status_disabilitas_dibutuhkan']) {
            1 => 'Ya (Disabilitas)',
            2 => 'Tidak (Non-Disabilitas)',
            default => 'Tidak Diketahui',
        };
    }
    
    // Opsi untuk dropdown di form/filter
    public static function getJenisKelaminDibutuhkanOptions(): array
    {
        return [1 => 'Laki-laki', 2 => 'Perempuan', 3 => 'Laki-laki/Perempuan'];
    }

    public static function getStatusDisabilitasDibutuhkanOptions(): array
    {
        return [1 => 'Ya (Disabilitas)', 2 => 'Tidak (Non-Disabilitas)'];
    }
}
