<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PelaporanWlkpOnline;
use Illuminate\Support\Carbon;

class PelaporanWlkpOnlineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PelaporanWlkpOnline::truncate(); // Hapus data lama jika ada

        $now = Carbon::now();
        $data = [
            [
                'tahun' => 2023,
                'bulan' => 10,
                'provinsi' => 'Jawa Barat',
                'kbli' => 'C', 
                'skala_perusahaan' => 'Besar',
                'jumlah_perusahaan_melapor' => 150, // Nama kolom yang benar
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tahun' => 2023,
                'bulan' => 10,
                'provinsi' => 'Jawa Barat',
                'kbli' => 'G', 
                'skala_perusahaan' => 'Menengah',
                'jumlah_perusahaan_melapor' => 200, // Nama kolom yang benar
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tahun' => 2024,
                'bulan' => 1,
                'provinsi' => 'DKI Jakarta',
                'kbli' => 'J', 
                'skala_perusahaan' => 'Kecil',
                'jumlah_perusahaan_melapor' => 75, // Nama kolom yang benar
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        if (!empty($data)) {
            PelaporanWlkpOnline::insert($data);
        }
    }
}
