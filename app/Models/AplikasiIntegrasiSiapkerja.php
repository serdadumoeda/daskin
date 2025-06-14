<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AplikasiIntegrasiSiapkerja extends Model
{
    use HasFactory;

    protected $table = 'jumlah_aplikasi_integrasi_siapkerja';

    protected $fillable = [
        'tahun',
        'bulan',
        'jenis_instansi',
        'nama_instansi',
        'nama_aplikasi_website',
        'status_integrasi',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'jenis_instansi' => 'integer',
        'status_integrasi' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getJenisInstansiTextAttribute(): string
    {
        return match ($this->attributes['jenis_instansi']) {
            1 => 'Kementerian',
            2 => 'Lembaga',
            3 => 'Daerah Provinsi',
            4 => 'Daerah Kabupaten/Kota',
            default => 'Tidak Diketahui',
        };
    }

    public function getStatusIntegrasiTextAttribute(): string
    {
        return match ($this->attributes['status_integrasi']) {
            1 => 'Terintegrasi',
            2 => 'Belum terintegrasi',
            default => 'Tidak Diketahui',
        };
    }
    
    public static function getJenisInstansiOptions(): array
    {
        return [
            1 => 'Kementerian',
            2 => 'Lembaga',
            3 => 'Daerah Provinsi',
            4 => 'Daerah Kabupaten/Kota',
        ];
    }

    public static function getStatusIntegrasiOptions(): array
    {
        return [1 => 'Terintegrasi', 2 => 'Belum terintegrasi'];
    }
}
