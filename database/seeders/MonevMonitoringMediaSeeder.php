<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MonevMonitoringMedia;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker; // Tambahkan Faker

class MonevMonitoringMediaSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create(); // Inisialisasi Faker
        $now = Carbon::now();
        $newData = []; // Array untuk data baru

        // Opsi untuk jenis_media dan sentimen_publik (berdasarkan model/migrasi)
        // 1: Media Cetak, 2: Media Online, 3: Media Elektronik
        $jenisMediaOptions = [1, 2, 3]; 
        // 1: Sentimen Positif, 2: Sentimen Negatif
        $sentimenPublikOptions = [1, 2]; 
        
        $tahunSekarang = (int) $now->year;

        for ($i = 0; $i < 20; $i++) {
            $tahun = rand($tahunSekarang - 2, $tahunSekarang); // Data untuk 3 tahun terakhir
            $bulan = rand(1, 12);

            $newData[] = [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'jenis_media' => $faker->randomElement($jenisMediaOptions),
                'sentimen_publik' => $faker->randomElement($sentimenPublikOptions),
                'jumlah_berita' => rand(5, 250), // Jumlah berita acak
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Hapus data contoh statis yang ada sebelumnya ($mediaData array)
        // Loop $newData untuk insert menggunakan create()
        if (!empty($newData)) {
            foreach ($newData as $item) {
                MonevMonitoringMedia::create($item);
            }
            // Alternatif: MonevMonitoringMedia::insert($newData); untuk bulk insert
        }
    }
}