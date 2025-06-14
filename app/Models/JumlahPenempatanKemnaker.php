<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JumlahPenempatanKemnaker extends Model
{
    use HasFactory;

    protected $table = 'jumlah_penempatan_kemnaker';

    protected $fillable = [
        'tahun',
        'bulan',
        'jenis_kelamin',      // 1: Laki-laki, 2: Perempuan
        'provinsi_domisili',  // Teks nama provinsi
        'lapangan_usaha_kbli',// Teks KBLI atau kode kategori
        'status_disabilitas', // 1: Ya, 2: Tidak
        'ragam_disabilitas',  // Teks: Fisik, Intelektual, Mental, Sensorik, Lebih dari 1 (nullable jika status_disabilitas = Tidak)
        'jumlah',             // Jumlah orang yang ditempatkan
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
        'status_disabilitas' => 'integer',
        'jumlah' => 'integer',
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

    // Accessor untuk Status Disabilitas
    public function getStatusDisabilitasTextAttribute(): string
    {
        return match ($this->attributes['status_disabilitas']) {
            1 => 'Ya',
            2 => 'Tidak',
            default => 'Tidak Diketahui',
        };
    }
    
    // Opsi untuk dropdown di form/filter
    public static function getJenisKelaminOptions(): array
    {
        return [1 => 'Laki-laki', 2 => 'Perempuan'];
    }

    public static function getStatusDisabilitasOptions(): array
    {
        return [1 => 'Ya', 2 => 'Tidak'];
    }

    // Ragam disabilitas bisa banyak, ini contoh dari PDF
    public static function getRagamDisabilitasOptions(): array
    {
        return [
            'Disabilitas Fisik' => 'Disabilitas Fisik',
            'Disabilitas Intelektual' => 'Disabilitas Intelektual',
            'Disabilitas Mental' => 'Disabilitas Mental',
            'Disabilitas Sensorik' => 'Disabilitas Sensorik',
            'Lebih dari 1 Disabilitas' => 'Lebih dari 1 Disabilitas',
            // Tambahkan 'Lainnya' jika perlu
        ];
    }
}
