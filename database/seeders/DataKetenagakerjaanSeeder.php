<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataKetenagakerjaan;
use Illuminate\Support\Carbon;

class DataKetenagakerjaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DataKetenagakerjaan::truncate(); // Opsional
        $now = Carbon::now();

        $data = [
            [
                'tahun' => 2023, 'bulan' => 2, // Februari
                'penduduk_15_atas' => 200000.500, 
                'angkatan_kerja' => 130000.750, 
                'bukan_angkatan_kerja' => 69999.750, 
                'sekolah' => 30000.000,
                'mengurus_rumah_tangga' => 25000.000,
                'lainnya_bak' => 14999.750, 
                'tpak' => 65.00,
                'bekerja' => 120000.250, 
                'pengangguran_terbuka' => 10000.500, 
                'tpt' => 7.69,
                'tingkat_kesempatan_kerja' => 92.31, 
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2023, 'bulan' => 8, // Agustus
                'penduduk_15_atas' => 205000.123, 
                'angkatan_kerja' => 135000.456, 
                'bukan_angkatan_kerja' => 69999.667,
                'sekolah' => 28000.000,
                'mengurus_rumah_tangga' => 27000.000,
                'lainnya_bak' => 14999.667,
                'tpak' => 65.85,
                'bekerja' => 125000.789, 
                'pengangguran_terbuka' => 10000.123, 
                'tpt' => 7.41,
                'tingkat_kesempatan_kerja' => 92.59,
                'created_at' => $now, 'updated_at' => $now
            ],
        ];

        foreach ($data as $item) {
            DataKetenagakerjaan::updateOrCreate(
                [ 'tahun' => $item['tahun'], 'bulan' => $item['bulan'] ], 
                $item
            );
        }
    }
}
