<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JumlahPenangananKasus;
use App\Models\SatuanKerja;
use Illuminate\Support\Carbon;

class JumlahPenangananKasusSeeder extends Seeder
{
    public function run(): void
    {
        $satuanKerjaBiroHukum = SatuanKerja::where('kode_sk', 'SK-004')->first(); // Biro Hukum
        
        $now = Carbon::now();
        $kasusData = [];

        if ($satuanKerjaBiroHukum) {
            $kasusData[] = [
                'tahun' => 2023,
                'bulan' => 7,
                'kode_satuan_kerja' => $satuanKerjaBiroHukum->kode_sk,
                'jenis_perkara' => 'Sengketa Informasi Publik',
                'jumlah_perkara' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $kasusData[] = [
                'tahun' => 2023,
                'bulan' => 7,
                'kode_satuan_kerja' => $satuanKerjaBiroHukum->kode_sk,
                'jenis_perkara' => 'Gugatan PTUN',
                'jumlah_perkara' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($kasusData)) {
            JumlahPenangananKasus::insert($kasusData);
        }
    }
}