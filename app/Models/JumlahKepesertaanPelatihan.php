<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JumlahKepesertaanPelatihan extends Model
{
    use HasFactory;

    protected $table = 'jumlah_kepesertaan_pelatihan';

    protected $fillable = [
        'tahun',
        'bulan',
        'penyelenggara_pelatihan',
        'tipe_lembaga',
        'jenis_kelamin',
        'provinsi_tempat_pelatihan',
        'kejuruan',
        'status_kelulusan',
        'jumlah',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'penyelenggara_pelatihan' => 'integer',
        'tipe_lembaga' => 'integer',
        'jenis_kelamin' => 'integer',
        'status_kelulusan' => 'integer',
        'jumlah' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getPenyelenggaraPelatihanTextAttribute(): string
    {
        return match ($this->attributes['penyelenggara_pelatihan']) {
            1 => 'Internal',
            2 => 'Eksternal',
            default => 'Tidak Diketahui',
        };
    }

    public function getTipeLembagaTextAttribute(): string
    {
        return match ($this->attributes['tipe_lembaga']) {
            1 => 'UPTP',
            2 => 'UPTD',
            3 => 'BLKLN',
            4 => 'Lembaga Pelatihan K/L',
            5 => 'SKPD',
            6 => 'LPK Swasta',
            7 => 'BLK Komunitas',
            default => 'Tidak Diketahui',
        };
    }

    public function getJenisKelaminTextAttribute(): string
    {
        return match ($this->attributes['jenis_kelamin']) {
            1 => 'Laki-laki',
            2 => 'Perempuan',
            default => 'Tidak Diketahui',
        };
    }

    public function getStatusKelulusanTextAttribute(): string
    {
        return match ($this->attributes['status_kelulusan']) {
            1 => 'Lulus',
            2 => 'Tidak Lulus',
            default => 'Tidak Diketahui',
        };
    }
    
    // Opsi untuk dropdown di form/filter
    public static function getPenyelenggaraPelatihanOptions(): array
    {
        return [1 => 'Internal', 2 => 'Eksternal'];
    }

    public static function getTipeLembagaOptions(): array
    {
        return [
            1 => 'UPTP', 2 => 'UPTD', 3 => 'BLKLN', 
            4 => 'Lembaga Pelatihan K/L', 5 => 'SKPD', 
            6 => 'LPK Swasta', 7 => 'BLK Komunitas'
        ];
    }

    public static function getJenisKelaminOptions(): array
    {
        return [1 => 'Laki-laki', 2 => 'Perempuan'];
    }

    public static function getStatusKelulusanOptions(): array
    {
        return [1 => 'Lulus', 2 => 'Tidak Lulus'];
    }
}
