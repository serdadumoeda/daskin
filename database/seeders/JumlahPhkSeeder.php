<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JumlahPhk;
use Illuminate\Support\Carbon;

class JumlahPhkSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $data = [
            [
                'tahun' => 2023,
                'bulan' => 8,
                'provinsi' => 'Jawa Timur',
                'kbli' => 'C28', // PEMBUATAN MESIN DAN PERLENGKAPAN YTDL
                'jumlah_perusahaan_phk' => 5,
                'jumlah_tk_phk' => 50,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tahun' => 2024,
                'bulan' => 1,
                'provinsi' => 'Jawa Barat',
                'kbli' => 'C29', // PEMBUATAN KENDARAAN BERMOTOR, TRAILER DAN SEMI-TRAILER
                'jumlah_perusahaan_phk' => 2,
                'jumlah_tk_phk' => 25,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        if (!empty($data)) {
            JumlahPhk::insert($data);
        }
    }
}