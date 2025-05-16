<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JumlahRegulasiBaru extends Model
{
    use HasFactory;

    protected $table = 'jumlah_regulasi_baru';

    protected $fillable = [
        'tahun',
        'bulan',
        'substansi',         // Baru
        'jenis_regulasi',
        'jumlah_regulasi',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'substansi' => 'integer', // Baru
        'jenis_regulasi' => 'integer',
        'jumlah_regulasi' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Mendapatkan teks untuk substansi.
     * Keterangan Substansi:
     * 1: Perencanaan dan Pengembangan
     * 2: Pelatihan Vokasi dan Produktivitas
     * 3: Penempatan Tenaga Kerja dan Perluasan Kesempatan Kerja
     * 4: Hubungan Industrial dan Jaminan Sosial
     * 5: Pengawasan Ketenagakerjaan dan K3
     * 6: Pengawasan Internal
     * 7: Kesekretariatan
     * 8: Lainnya
     *
     * @return string
     */
    public function getSubstansiTextAttribute(): string
    {
        return match ($this->substansi) {
            1 => 'Perencanaan dan Pengembangan',
            2 => 'Pelatihan Vokasi dan Produktivitas',
            3 => 'Penempatan Tenaga Kerja dan Perluasan Kesempatan Kerja',
            4 => 'Hubungan Industrial dan Jaminan Sosial',
            5 => 'Pengawasan Ketenagakerjaan dan K3',
            6 => 'Pengawasan Internal',
            7 => 'Kesekretariatan',
            8 => 'Lainnya',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Mendapatkan teks untuk jenis regulasi.
     * Keterangan Jenis Regulasi:
     * 1: Undang-Undang
     * 2: Peraturan Pemerintah
     * 3: Peraturan Presiden
     * 4: Keputusan Presiden
     * 5: Instruksi Presiden
     * 6: Peraturan Menteri
     * 7: Keputusan Menteri
     * 8: SE/Instruksi Menteri
     * 9: Peraturan/Keputusan Pejabat Eselon I
     * 10: Peraturan Terkait
     *
     * @return string
     */
    public function getJenisRegulasiTextAttribute(): string
    {
        return match ($this->jenis_regulasi) {
            1 => 'Undang-Undang',
            2 => 'Peraturan Pemerintah',
            3 => 'Peraturan Presiden',
            4 => 'Keputusan Presiden',
            5 => 'Instruksi Presiden',
            6 => 'Peraturan Menteri',
            7 => 'Keputusan Menteri',
            8 => 'SE/Instruksi Menteri',
            9 => 'Peraturan/Keputusan Pejabat Eselon I',
            10 => 'Peraturan Terkait',
            default => 'Tidak Diketahui',
        };
    }
}