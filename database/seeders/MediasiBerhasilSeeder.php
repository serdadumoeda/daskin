<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MediasiBerhasil;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker; // Tambahkan Faker

class MediasiBerhasilSeeder extends Seeder
{
    public function run(): void
    {
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

        // Daftar contoh kode KBLI
        $daftarKbli = [
            'A01', 'B05', 'C10', 'C13', 'C20', 'C22', 'C24', 'F41', 'G45', 'G47', 
            'H49', 'H52', 'I56', 'J61', 'J62', 'K64', 'L68', 'M71', 'N82', 'Q86', 'R90', 'S94'
        ];

        // Mengambil opsi dari model
        // Model getJenisPerselisihanOptions mengembalikan [Value => Text], kita ambil value-nya.
        $jenisPerselisihanOptions = array_values(MediasiBerhasil::getJenisPerselisihanOptions()); 
        // Model getHasilMediasiOptions mengembalikan [Key => Text], kita ambil Key-nya ('PB', 'Anjuran').
        $hasilMediasiOptions = array_keys(MediasiBerhasil::getHasilMediasiOptions()); 
        
        $tahunSekarang = (int) $now->year;

        for ($i = 0; $i < 20; $i++) {
            $tahun = rand($tahunSekarang - 2, $tahunSekarang); // Data untuk 3 tahun terakhir
            $bulan = rand(1, 12);
            $jumlahMediasi = rand(1, 40);
            $jumlahMediasiBerhasil = rand(0, $jumlahMediasi); // Pastikan tidak lebih besar dari jumlah mediasi

            $newData[] = [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'provinsi' => $faker->randomElement($daftarProvinsi),
                'kbli' => $faker->randomElement($daftarKbli),
                'jenis_perselisihan' => $faker->randomElement($jenisPerselisihanOptions),
                'hasil_mediasi' => $faker->randomElement($hasilMediasiOptions), // Akan menghasilkan 'PB' atau 'Anjuran'
                'jumlah_mediasi' => $jumlahMediasi,
                'jumlah_mediasi_berhasil' => $jumlahMediasiBerhasil,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Hapus data contoh statis yang ada sebelumnya
        // Loop $newData untuk insert menggunakan create()
        if (!empty($newData)) {
            foreach ($newData as $item) {
                MediasiBerhasil::create($item);
            }
            // Alternatif: MediasiBerhasil::insert($newData); untuk bulk insert
        }
    }
}