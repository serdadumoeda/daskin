<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JumlahRegulasiBaru;
use App\Models\SatuanKerja; // Untuk mengambil kode_sk yang valid
use Illuminate\Support\Carbon;

class JumlahRegulasiBaruSeeder extends Seeder
{
    public function run(): void
    {
        $satuanKerja1 = SatuanKerja::where('kode_sk', 'SK-004')->first(); // Biro Hukum
        $satuanKerja2 = SatuanKerja::where('kode_sk', 'SK-100')->first(); // Sekretariat Ditjen Binalavotas
        
        $now = Carbon::now();
        $regulasiData = [];

        if ($satuanKerja1) {
            $regulasiData[] = [
                'tahun' => 2023,
                'bulan' => 6,
                'kode_satuan_kerja' => $satuanKerja1->kode_sk,
                'jenis_regulasi' => 3, // Permen
                'jumlah_regulasi' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $regulasiData[] = [
                'tahun' => 2023,
                'bulan' => 6,
                'kode_satuan_kerja' => $satuanKerja1->kode_sk,
                'jenis_regulasi' => 4, // Kepmen
                'jumlah_regulasi' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        if ($satuanKerja2) {
             $regulasiData[] = [
                'tahun' => 2024,
                'bulan' => 2,
                'kode_satuan_kerja' => $satuanKerja2->kode_sk,
                'jenis_regulasi' => 3, // Permen
                'jumlah_regulasi' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($regulasiData)) {
            JumlahRegulasiBaru::insert($regulasiData);
        }
    }
}