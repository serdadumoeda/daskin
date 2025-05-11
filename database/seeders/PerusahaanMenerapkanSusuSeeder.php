<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PerusahaanMenerapkanSusu;
use Illuminate\Support\Carbon;

class PerusahaanMenerapkanSusuSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $data = [
            [
                'tahun' => 2023,
                'bulan' => 12,
                'provinsi' => 'DKI Jakarta',
                'kbli' => 'K64', // Aktivitas Jasa Keuangan, Selain Asuransi Dan Dana Pensiun
                'jumlah_perusahaan_susu' => 250,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tahun' => 2024,
                'bulan' => 1,
                'provinsi' => 'Jawa Tengah',
                'kbli' => 'C10', // Industri Makanan
                'jumlah_perusahaan_susu' => 180,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        if (!empty($data)) {
            PerusahaanMenerapkanSusu::insert($data);
        }
    }
}