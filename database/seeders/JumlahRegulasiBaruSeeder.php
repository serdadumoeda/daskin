<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JumlahRegulasiBaru;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker; // Tambahkan Faker

class JumlahRegulasiBaruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Opsional: Hapus data lama jika diperlukan.
        // JumlahRegulasiBaru::truncate(); 

        $faker = Faker::create(); // Inisialisasi Faker
        $now = Carbon::now();
        $newData = []; // Array untuk data baru

        // Opsi untuk substansi dan jenis_regulasi (berdasarkan model/migrasi)
        // Keterangan Substansi: 1-8
        $substansiOptions = range(1, 8); 
        // Keterangan Jenis Regulasi: 1-10
        $jenisRegulasiOptions = range(1, 10); 
        
        $tahunSekarang = (int) $now->year;

        for ($i = 0; $i < 20; $i++) {
            $tahun = rand($tahunSekarang - 2, $tahunSekarang); // Data untuk 3 tahun terakhir
            $bulan = rand(1, 12);

            $newData[] = [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'substansi' => $faker->randomElement($substansiOptions),
                'jenis_regulasi' => $faker->randomElement($jenisRegulasiOptions),
                'jumlah_regulasi' => rand(1, 7), // Jumlah regulasi baru per record
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Hapus data contoh statis yang ada sebelumnya ($regulasiData array)
        // Loop $newData untuk insert menggunakan create()
        if (!empty($newData)) {
            foreach ($newData as $item) {
                JumlahRegulasiBaru::create($item);
            }
            // Alternatif: JumlahRegulasiBaru::insert($newData); untuk bulk insert
        }

        // $this->command->info('Seeder JumlahRegulasiBaru berhasil dijalankan.'); // Bisa diaktifkan jika ingin notifikasi
    }
}