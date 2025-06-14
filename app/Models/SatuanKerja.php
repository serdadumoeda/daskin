<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SatuanKerja extends Model
{
    use HasFactory;

    protected $table = 'satuan_kerja';
    protected $primaryKey = 'kode_sk';    // Mendefinisikan primary key
    public $incrementing = false;        // Memberitahu Laravel bahwa PK tidak auto-increment
    protected $keyType = 'string';       // Memberitahu Laravel bahwa PK adalah string

    protected $fillable = [
        'kode_sk', // PK juga harus fillable
        'kode_unit_kerja_eselon_i',
        'nama_satuan_kerja',
    ];

    public function unitKerjaEselonI(): BelongsTo
    {
        // foreignKey, ownerKey (PK di tabel parent)
        return $this->belongsTo(UnitKerjaEselonI::class, 'kode_unit_kerja_eselon_i', 'kode_uke1');
    }
}