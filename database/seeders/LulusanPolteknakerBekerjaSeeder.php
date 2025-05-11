<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LulusanPolteknakerBekerja;
use Illuminate\Support\Carbon;

class LulusanPolteknakerBekerjaSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $lulusanData = [
            [
                'tahun' => 2023,
                'bulan' => 9, // Misal periode kelulusan September
                'program_studi' => 1, // Relasi Industri
                'jumlah_lulusan' => 50,
                'jumlah_lulusan_bekerja' => 40,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tahun' => 2023,
                'bulan' => 9,
                'program_studi' => 2, // K3
                'jumlah_lulusan' => 45,
                'jumlah_lulusan_bekerja' => 38,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tahun' => 2023,
                'bulan' => 9,
                'program_studi' => 3, // MSDM
                'jumlah_lulusan' => 55,
                'jumlah_lulusan_bekerja' => 48,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        if(!empty($lulusanData)){
            LulusanPolteknakerBekerja::insert($lulusanData);
        }
    }
}