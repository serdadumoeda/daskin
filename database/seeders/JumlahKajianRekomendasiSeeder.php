<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JumlahKajianRekomendasi;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker; // Tambahkan Faker

class JumlahKajianRekomendasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Opsional: Hapus data lama. Aktifkan jika ingin memulai dengan tabel bersih.
        // JumlahKajianRekomendasi::truncate(); 

        $faker = Faker::create(); // Inisialisasi Faker
        $now = Carbon::now();
        $newData = []; // Array untuk data baru

        // Mengambil kunci (integer) dari opsi yang ada di model
        $substansiOptions = array_keys(JumlahKajianRekomendasi::getSubstansiOptions()); // [1, 2, 3, 4, 5]
        $jenisOutputOptions = array_keys(JumlahKajianRekomendasi::getJenisOutputOptions()); // [1, 2]
        
        $tahunSekarang = (int) $now->year;

        for ($i = 0; $i < 20; $i++) {
            $tahun = rand($tahunSekarang - 2, $tahunSekarang); // Data untuk 3 tahun terakhir
            $bulan = rand(1, 12);

            $newData[] = [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'substansi' => $faker->randomElement($substansiOptions),
                'jenis_output' => $faker->randomElement($jenisOutputOptions),
                'jumlah' => rand(1, 7), // Jumlah kajian atau rekomendasi per record
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Mengganti penggunaan firstOrCreate dengan create dalam loop untuk data dummy
        if (!empty($newData)) {
            foreach ($newData as $item) {
                JumlahKajianRekomendasi::create($item);
            }
        }
    }
}