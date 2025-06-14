<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitKerjaEselonI extends Model
{
    use HasFactory;

    protected $table = 'unit_kerja_eselon_i';
    protected $primaryKey = 'kode_uke1'; // Mendefinisikan primary key
    public $incrementing = false;       // Memberitahu Laravel bahwa PK tidak auto-increment
    protected $keyType = 'string';      // Memberitahu Laravel bahwa PK adalah string

    protected $fillable = [
        'kode_uke1', // PK juga harus fillable jika Anda ingin membuatnya melalui create()
        'nama_unit_kerja_eselon_i',
    ];

    public function satuanKerja(): HasMany
    {
        // foreignKey, localKey
        return $this->hasMany(SatuanKerja::class, 'kode_unit_kerja_eselon_i', 'kode_uke1');
    }
}