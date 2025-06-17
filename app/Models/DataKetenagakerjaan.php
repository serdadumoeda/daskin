<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataKetenagakerjaan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     */
    protected $table = 'data_ketenagakerjaan'; 

    protected $fillable = [
        'tahun',
        'bulan',
        'penduduk_15_atas', 
        'angkatan_kerja',
        'bukan_angkatan_kerja',    
        'sekolah',                 
        'mengurus_rumah_tangga',   
        'lainnya_bak', 
        'tpak', 
        'bekerja',
        'pengangguran_terbuka',
        'tpt', 
        'tingkat_kesempatan_kerja', 
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'penduduk_15_tahun_ke_atas' => 'decimal:3',
        'angkatan_kerja' => 'decimal:3',
        'bukan_angkatan_kerja' => 'decimal:3', 
        'sekolah' => 'decimal:3',              
        'mengurus_rumah_tangga' => 'decimal:3',
        'lainnya_bukan_angkatan_kerja' => 'decimal:3',          
        'tingkat_partisipasi_angkatan_kerja' => 'decimal:2',
        'bekerja' => 'decimal:3',
        'pengangguran_terbuka' => 'decimal:3',
        'tingkat_pengangguran_terbuka' => 'decimal:2',
        'tingkat_kesempatan_kerja' => 'decimal:2', 
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
