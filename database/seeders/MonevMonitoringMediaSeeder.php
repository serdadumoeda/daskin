<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MonevMonitoringMedia;
use Illuminate\Support\Carbon;

class MonevMonitoringMediaSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $mediaData = [
            [
                'tahun' => 2024,
                'bulan' => 1,
                'jenis_media' => 2, // Online
                'sentimen_publik' => 1, // Positif
                'jumlah_berita' => 150,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tahun' => 2024,
                'bulan' => 1,
                'jenis_media' => 2, // Online
                'sentimen_publik' => 2, // Negatif
                'jumlah_berita' => 20,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tahun' => 2024,
                'bulan' => 1,
                'jenis_media' => 1, // Cetak
                'sentimen_publik' => 1, // Positif
                'jumlah_berita' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        if(!empty($mediaData)){
            MonevMonitoringMedia::insert($mediaData);
        }
    }
}