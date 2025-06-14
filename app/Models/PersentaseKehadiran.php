<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersentaseKehadiran extends Model
{
    use HasFactory;

    protected $table = 'persentase_kehadiran';

    protected $fillable = [
        'tahun',
        'bulan',
        'kode_unit_kerja_eselon_i', // Kolom (4) di PDF, diasumsikan Unit Kerja Eselon I
        'status_asn',             // 1: ASN, 2: Non ASN
        'status_kehadiran',       // 1: WFO, 2: Cuti, 3: Dinas Luar, 4: Sakit, 5: Tugas Belajar, 6: Tanpa Keterangan
        'jumlah_orang',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'status_asn' => 'integer',
        'status_kehadiran' => 'integer',
        'jumlah_orang' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke Unit Kerja Eselon I.
     */
    public function unitKerjaEselonI(): BelongsTo
    {
        return $this->belongsTo(UnitKerjaEselonI::class, 'kode_unit_kerja_eselon_i', 'kode_uke1');
    }

    /**
     * Mendapatkan teks untuk Status ASN.
     * 1: ASN
     * 2: Non ASN
     */
    public function getStatusAsnTextAttribute(): string
    {
        return match ($this->attributes['status_asn']) {
            1 => 'ASN',
            2 => 'Non ASN',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Mendapatkan teks untuk Status Kehadiran.
     * 1: WFO, 2: Cuti, 3: Dinas Luar, 4: Sakit, 5: Tugas Belajar, 6: Tanpa Keterangan
     */
    public function getStatusKehadiranTextAttribute(): string
    {
        return match ($this->attributes['status_kehadiran']) {
            1 => 'WFO',
            2 => 'Cuti',
            3 => 'Dinas Luar',
            4 => 'Sakit',
            5 => 'Tugas Belajar',
            6 => 'Tanpa Keterangan',
            default => 'Tidak Diketahui',
        };
    }
}
