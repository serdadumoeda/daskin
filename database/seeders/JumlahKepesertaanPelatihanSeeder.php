<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JumlahKepesertaanPelatihan;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker; // Tambahkan Faker

class JumlahKepesertaanPelatihanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Opsional: Hapus data lama. Aktifkan jika ingin memulai dengan tabel bersih.
        // JumlahKepesertaanPelatihan::truncate(); 

        $faker = Faker::create('id_ID'); // Inisialisasi Faker
        $now = Carbon::now();
        $newData = []; // Array untuk data baru

        // Mengambil kunci (integer) dari opsi yang ada di model
        $penyelenggaraOptions = array_keys(JumlahKepesertaanPelatihan::getPenyelenggaraPelatihanOptions());
        $tipeLembagaOptions = array_keys(JumlahKepesertaanPelatihan::getTipeLembagaOptions());
        $jenisKelaminOptions = array_keys(JumlahKepesertaanPelatihan::getJenisKelaminOptions());
        $statusKelulusanOptions = array_keys(JumlahKepesertaanPelatihan::getStatusKelulusanOptions());

        // Daftar contoh untuk data acak
        $daftarProvinsi = [
            'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Jambi', 'Sumatera Selatan', 
            'Bengkulu', 'Lampung', 'Kepulauan Bangka Belitung', 'Kepulauan Riau', 
            'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur', 'Banten', 
            'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur', 
            'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara', 
            'Sulawesi Utara', 'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Gorontalo', 'Sulawesi Barat', 
            'Maluku', 'Maluku Utara', 'Papua Barat', 'Papua'
        ];
        
        $kejuruanList = [
            'Operator Mesin CNC', 'Digital Marketing', 'Menjahit Pakaian Wanita', 'Teknik Las GTAW', 
            'Desain Grafis Multimedia', 'Tata Boga Kontinental', 'Barista Profesional', 
            'Teknisi Jaringan Komputer', 'Bahasa Inggris untuk Perhotelan', 'Servis Sepeda Motor Injeksi', 
            'Instalasi Listrik Bangunan Sederhana', 'Teknik Pendingin dan Tata Udara (AC Split)', 
            'Front Office Hotel', 'Welder SMAW Posisi 3G', 'Pemrograman Web Dasar', 
            'Mobile Programming (Android)', 'Pengelasan Pelat 2F', 'Practical Office', 'Bahasa Jepang Dasar',
            'Tata Rias Kecantikan Rambut'
        ];
        
        $tahunSekarang = (int) $now->year;

        for ($i = 0; $i < 20; $i++) {
            $tahun = rand($tahunSekarang - 2, $tahunSekarang); // Data untuk 3 tahun terakhir
            $bulan = rand(1, 12);

            $newData[] = [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'penyelenggara_pelatihan' => $faker->randomElement($penyelenggaraOptions),
                'tipe_lembaga' => $faker->randomElement($tipeLembagaOptions),
                'jenis_kelamin' => $faker->randomElement($jenisKelaminOptions),
                'provinsi_tempat_pelatihan' => $faker->randomElement($daftarProvinsi),
                'kejuruan' => $faker->randomElement($kejuruanList),
                'status_kelulusan' => $faker->randomElement($statusKelulusanOptions),
                'jumlah' => rand(10, 45), // Jumlah peserta per pelatihan
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Mengganti penggunaan firstOrCreate dengan create dalam loop untuk data dummy
        if (!empty($newData)) {
            foreach ($newData as $item) {
                JumlahKepesertaanPelatihan::create($item);
            }
        }
    }
}