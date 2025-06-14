<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JumlahKajianRekomendasi extends Model
{
    use HasFactory;

    protected $table = 'jumlah_kajian_rekomendasi';

    protected $fillable = [
        'tahun',
        'bulan',
        'substansi',
        'jenis_output',
        'jumlah',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'substansi' => 'integer',
        'jenis_output' => 'integer',
        'jumlah' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getSubstansiTextAttribute(): string
    {
        return match ($this->attributes['substansi']) {
            1 => 'Pelatihan Vokasi dan Produktivitas',
            2 => 'Penempatan Tenaga Kerja dan Perluasan Kesempatan Kerja',
            3 => 'Hubungan Industrial dan Jaminan Sosial Tenaga Kerja',
            4 => 'Pengawasan Ketenagakerjaan dan K3',
            5 => 'Lainnya',
            default => 'Tidak Diketahui',
        };
    }

    public function getJenisOutputTextAttribute(): string
    {
        return match ($this->attributes['jenis_output']) {
            1 => 'Kajian',
            2 => 'Rekomendasi',
            default => 'Tidak Diketahui',
        };
    }
    
    public static function getSubstansiOptions(): array
    {
        return [
            1 => 'Pelatihan Vokasi dan Produktivitas',
            2 => 'Penempatan Tenaga Kerja dan Perluasan Kesempatan Kerja',
            3 => 'Hubungan Industrial dan Jaminan Sosial Tenaga Kerja',
            4 => 'Pengawasan Ketenagakerjaan dan K3',
            5 => 'Lainnya',
        ];
    }

    public static function getJenisOutputOptions(): array
    {
        return [1 => 'Kajian', 2 => 'Rekomendasi'];
    }
}
