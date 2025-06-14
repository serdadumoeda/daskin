<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LulusanPolteknakerBekerja;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker; // Tambahkan Faker

class LulusanPolteknakerBekerjaSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create(); // Inisialisasi Faker
        $now = Carbon::now();
        $newData = []; // Array untuk data baru

        // Opsi untuk program_studi (berdasarkan model/migrasi)
        // 1: Relasi Industri, 2: K3, 3: MSDM
        $programStudiOptions = [1, 2, 3]; 
        
        $tahunSekarang = (int) $now->year;

        for ($i = 0; $i < 20; $i++) {
            $tahun = rand($tahunSekarang - 2, $tahunSekarang); // Data untuk 3 tahun terakhir
            
            // Bulan bisa merepresentasikan periode kelulusan. 
            // Untuk data dummy, kita bisa acak atau tentukan beberapa periode umum, misal Maret dan September.
            // $bulan = $faker->randomElement([3, 9]); 
            // Atau acak sepenuhnya:
            $bulan = rand(1, 12); 

            $jumlahLulusan = rand(25, 80); // Jumlah lulusan per prodi per periode
            // Jumlah lulusan bekerja tidak boleh melebihi jumlah lulusan
            $jumlahLulusanBekerja = rand( (int)($jumlahLulusan * 0.5) , $jumlahLulusan ); // Minimal 50% bekerja

            $newData[] = [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'program_studi' => $faker->randomElement($programStudiOptions),
                'jumlah_lulusan' => $jumlahLulusan,
                'jumlah_lulusan_bekerja' => $jumlahLulusanBekerja,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Hapus data contoh statis yang ada sebelumnya ($lulusanData array)
        // Loop $newData untuk insert menggunakan create()
        if (!empty($newData)) {
            foreach ($newData as $item) {
                LulusanPolteknakerBekerja::create($item);
            }
            // Alternatif: LulusanPolteknakerBekerja::insert($newData); untuk bulk insert
        }
    }
}