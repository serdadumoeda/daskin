<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JumlahTkaDisetujui;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker; // Tambahkan Faker

class JumlahTkaDisetujuiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Opsional: Hapus data lama. Jika diaktifkan, semua data lama akan hilang.
        // JumlahTkaDisetujui::truncate(); 

        $faker = Faker::create('id_ID'); // Inisialisasi Faker, id_ID untuk data Indonesia jika relevan
        $now = Carbon::now();
        $newData = []; // Array untuk menampung data baru

        // Daftar contoh untuk data acak
        $jenisKelaminOptions = [1, 2]; // 1: Laki-laki, 2: Perempuan

        $negaraAsalList = ['Jepang', 'Korea Selatan', 'Tiongkok', 'India', 'Malaysia', 'Amerika Serikat', 'Australia', 'Singapura', 'Thailand', 'Filipina', 'Vietnam', 'Jerman', 'Inggris', 'Prancis', 'Kanada', 'Belanda', 'Italia', 'Spanyol', 'Rusia', 'Brasil', 'Taiwan', 'Hong Kong', 'Selandia Baru', 'Afrika Selatan', 'Uni Emirat Arab'];

        $jabatanList = ['Technical Advisor', 'Marketing Director', 'IT Consultant', 'Chief Engineer', 'General Manager', 'Project Manager', 'Financial Controller', 'Operations Manager', 'Language Teacher', 'Senior Developer', 'Research Scientist', 'Sales Executive', 'Quality Control Manager', 'Production Supervisor', 'Logistics Coordinator', 'HSE Manager', 'Drilling Supervisor', 'Geologist', 'Chef', 'Pilot'];

        $lapanganUsahaList = [
            'Industri Otomotif (C29)', 'Perdagangan Besar (G46)', 'Jasa Konsultasi Manajemen (M70)', 
            'Pertambangan Batu Bara (B05)', 'Manufaktur Elektronik (C26)', 'Konstruksi Gedung (F41)', 
            'Teknologi Informasi dan Komunikasi (J62)', 'Jasa Keuangan (K64)', 'Pendidikan (P85)', 
            'Hotel dan Akomodasi (I55)', 'Industri Makanan dan Minuman (C10)', 'Pertanian (A01)',
            'Transportasi dan Pergudangan (H49)', 'Energi (D35)', 'Real Estat (L68)'
        ];
        
        $daftarProvinsi = [
            'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Jambi', 'Sumatera Selatan', 
            'Bengkulu', 'Lampung', 'Kepulauan Bangka Belitung', 'Kepulauan Riau', 
            'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur', 'Banten', 
            'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur', 
            'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara', 
            'Sulawesi Utara', 'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Gorontalo', 'Sulawesi Barat', 
            'Maluku', 'Maluku Utara', 'Papua Barat', 'Papua'
        ];
        
        $tahunSekarang = (int) $now->year;

        for ($i = 0; $i < 20; $i++) {
            $tahun = rand($tahunSekarang - 2, $tahunSekarang); // Data untuk 3 tahun terakhir
            $bulan = rand(1, 12);

            $newData[] = [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'jenis_kelamin' => $faker->randomElement($jenisKelaminOptions),
                'negara_asal' => $faker->randomElement($negaraAsalList),
                'jabatan' => $faker->randomElement($jabatanList),
                'lapangan_usaha_kbli' => $faker->randomElement($lapanganUsahaList),
                'provinsi_penempatan' => $faker->randomElement($daftarProvinsi),
                'jumlah_tka' => rand(1, 8), // Jumlah TKA yang disetujui per record
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Mengganti penggunaan firstOrCreate dengan create dalam loop untuk data dummy
        // Jika Anda memerlukan logika firstOrCreate untuk menghindari duplikasi berdasarkan kriteria tertentu,
        // Anda dapat menyesuaikannya kembali. Untuk data dummy murni, create lebih sederhana.
        if (!empty($newData)) {
            foreach ($newData as $item) {
                JumlahTkaDisetujui::create($item);
            }
        }
    }
}