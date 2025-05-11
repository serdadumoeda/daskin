<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerselisihanDitindaklanjuti extends Model
{
    use HasFactory;

    protected $table = 'perselisihan_ditindaklanjuti';

    protected $fillable = [
        'tahun', // Tahun pengaduan/pencatatan
        'bulan', // Bulan pengaduan/pencatatan
        'provinsi',
        'kbli',
        'jenis_perselisihan', // Teks: Perselisihan Hak, Kepentingan, PHK, SP/SB
        'cara_penyelesaian',  // Teks: Bipartit, Mediasi, Konsoliasi, Arbitrasi
        'jumlah_perselisihan',
        'jumlah_ditindaklanjuti',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'jumlah_perselisihan' => 'integer',
        'jumlah_ditindaklanjuti' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Jika Jenis Perselisihan atau Cara Penyelesaian memiliki daftar tetap,
    // Anda bisa membuat konstanta atau accessor di sini.
    // Contoh:
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

    public const CARA_PENYELESAIAN_BIPARTIT = 'Bipartit';
    public const CARA_PENYELESAIAN_MEDIASI = 'Mediasi';
    public const CARA_PENYELESAIAN_KONSOLIASI = 'Konsoliasi';
    public const CARA_PENYELESAIAN_ARBITRASI = 'Arbitrasi';

    public static function getCaraPenyelesaianOptions(): array
    {
        return [
            self::CARA_PENYELESAIAN_BIPARTIT => 'Bipartit',
            self::CARA_PENYELESAIAN_MEDIASI => 'Mediasi',
            self::CARA_PENYELESAIAN_KONSOLIASI => 'Konsoliasi',
            self::CARA_PENYELESAIAN_ARBITRASI => 'Arbitrasi',
            // Tambahkan opsi lain jika ada
        ];
    }
}
