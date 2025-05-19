<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PelaporanWlkpOnline;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker; // Tambahkan Faker

class PelaporanWlkpOnlineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Opsi: Hapus data lama jika ada. Hati-hati jika ada relasi atau data penting.
        // PelaporanWlkpOnline::truncate(); 

        $faker = Faker::create('id_ID'); // Inisialisasi Faker
        $now = Carbon::now();
        $newData = []; // Array untuk data baru

        // Daftar contoh provinsi di Indonesia
        $daftarProvinsi = [
            'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Jambi', 'Sumatera Selatan', 
            'Bengkulu', 'Lampung', 'Kepulauan Bangka Belitung', 'Kepulauan Riau', 
            'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur', 'Banten', 
            'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur', 
            'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara', 
            'Sulawesi Utara', 'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Gorontalo', 'Sulawesi Barat', 
            'Maluku', 'Maluku Utara', 'Papua Barat', 'Papua'
        ];

        // Daftar contoh kode KBLI (menggunakan kategori huruf seperti di seeder asli atau kode lebih detail)
        // Anda bisa menggunakan daftar KBLI yang lebih detail seperti di seeder lain jika diperlukan
        $daftarKbli = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U'];
        // Atau daftar yang lebih detail:
        // $daftarKbli = [
        //     'A01', 'B05', 'C10', 'C13', 'C20', 'F41', 'G47', 'H49', 'I56', 'J61', 'K64', 'M71', 'Q86'
        // ];


        // Skala Perusahaan
        $skalaPerusahaanList = ['Mikro', 'Kecil', 'Menengah', 'Besar'];
        
        $tahunSekarang = (int) $now->year;

        for ($i = 0; $i < 20; $i++) {
            $tahun = rand($tahunSekarang - 2, $tahunSekarang); // Data untuk 3 tahun terakhir
            $bulan = rand(1, 12);

            $newData[] = [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'provinsi' => $faker->randomElement($daftarProvinsi),
                'kbli' => $faker->randomElement($daftarKbli), 
                'skala_perusahaan' => $faker->randomElement($skalaPerusahaanList),
                'jumlah_perusahaan_melapor' => rand(5, 250), // Jumlah perusahaan yang melapor
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Hapus data contoh statis yang ada sebelumnya ($data array)
        // Loop $newData untuk insert menggunakan create()
        if (!empty($newData)) {
            foreach ($newData as $item) {
                PelaporanWlkpOnline::create($item);
            }
            // Alternatif: PelaporanWlkpOnline::insert($newData); untuk bulk insert
        }
    }
}