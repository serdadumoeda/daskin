<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediasiBerhasil extends Model
{
    use HasFactory;

    protected $table = 'mediasi_berhasil';

    protected $fillable = [
        'tahun',
        'bulan',
        'provinsi',
        'kbli',
        'jenis_perselisihan', // Teks: Perselisihan Hak, Kepentingan, PHK, SP/SB
        'hasil_mediasi',      // Teks: PB (Perjanjian Bersama), anjuran
        'jumlah_mediasi',
        'jumlah_mediasi_berhasil',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'jumlah_mediasi' => 'integer',
        'jumlah_mediasi_berhasil' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Opsi untuk Jenis Perselisihan (sama dengan di PerselisihanDitindaklanjuti)
    public const JENIS_PERSELISIHAN_HAK = 'Perselisihan Hak';
    public const JENIS_PERSELISIHAN_KEPENTINGAN = 'Perselisihan Kepentingan';
    public const JENIS_PERSELISIHAN_PHK = 'Perselisihan PHK';
    public const JENIS_PERSELISIHAN_SPSB = 'Perselisihan SP/SB';

    public static function getJenisPerselisihanOptions(): array
    {
        return [
            self::JENIS_PERSELISIHAN_HAK => 'Perselisihan Hak',
            self::JENIS_PERSELISIHAN_KEPENTINGAN => 'Perselisihan Kepentingan',
            self::JENIS_PERSELISIHAN_PHK => 'Perselisihan PHK',
            self::JENIS_PERSELISIHAN_SPSB => 'Perselisihan SP/SB',
            // Tambahkan opsi lain jika ada
        ];
    }

    // Opsi untuk Hasil Mediasi
    public const HASIL_MEDIASI_PB = 'PB'; // Perjanjian Bersama
    public const HASIL_MEDIASI_ANJURAN = 'Anjuran';

    public static function getHasilMediasiOptions(): array
    {
        return [
            self::HASIL_MEDIASI_PB => 'Perjanjian Bersama (PB)',
            self::HASIL_MEDIASI_ANJURAN => 'Anjuran',
            // Tambahkan opsi lain jika ada
        ];
    }
}
