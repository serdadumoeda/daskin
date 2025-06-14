<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JumlahTkaDisetujui extends Model
{
    use HasFactory;

    protected $table = 'jumlah_tka_disetujui';

    protected $fillable = [
        'tahun',
        'bulan',
        'jenis_kelamin',
        'negara_asal',
        'jabatan',
        'lapangan_usaha_kbli',
        'provinsi_penempatan',
        'jumlah_tka', // Mengganti nama field agar lebih generik untuk jumlah TKA
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'jenis_kelamin' => 'integer',
        'jumlah_tka' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Accessor untuk Jenis Kelamin
    public function getJenisKelaminTextAttribute(): string
    {
        return match ($this->attributes['jenis_kelamin']) {
            1 => 'Laki-laki',
            2 => 'Perempuan',
            default => 'Tidak Diketahui',
        };
    }
    
    // Opsi untuk dropdown di form/filter
    public static function getJenisKelaminOptions(): array
    {
        return [1 => 'Laki-laki', 2 => 'Perempuan'];
    }
}
