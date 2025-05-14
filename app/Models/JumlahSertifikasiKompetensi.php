<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JumlahSertifikasiKompetensi extends Model
{
    use HasFactory;

    protected $table = 'jumlah_sertifikasi_kompetensi';

    protected $fillable = [
        'tahun',
        'bulan',
        'jenis_lsp',
        'jenis_kelamin',
        'provinsi',
        'lapangan_usaha_kbli',
        'jumlah_sertifikasi',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'jenis_lsp' => 'integer',
        'jenis_kelamin' => 'integer',
        'jumlah_sertifikasi' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getJenisLspTextAttribute(): string
    {
        return match ($this->attributes['jenis_lsp']) {
            1 => 'P1',
            2 => 'P2',
            3 => 'P3',
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
    
    public static function getJenisLspOptions(): array
    {
        return [1 => 'P1', 2 => 'P2', 3 => 'P3'];
    }

    public static function getJenisKelaminOptions(): array
    {
        return [1 => 'Laki-laki', 2 => 'Perempuan'];
    }
}
