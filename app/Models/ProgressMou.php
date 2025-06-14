<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressMou extends Model
{
    use HasFactory;

    protected $table = 'progress_mou';

    protected $fillable = [
        'tahun',
        'bulan',
        'judul_mou',
        'tanggal_mulai_perjanjian',
        'tanggal_selesai_perjanjian',
        'pihak_terlibat',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'tanggal_mulai_perjanjian' => 'date',
        'tanggal_selesai_perjanjian' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
