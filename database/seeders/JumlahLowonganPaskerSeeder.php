<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JumlahLowonganPasker;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker; // Tambahkan Faker

class JumlahLowonganPaskerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // Inisialisasi Faker
        $now = Carbon::now();
        $newData = []; // Mengganti nama variabel $data menjadi $newData

        // Mengambil kunci dari opsi yang ada di model
        $jenisKelaminKeys = array_keys(JumlahLowonganPasker::JENIS_KELAMIN_OPTIONS);
        $statusDisabilitasKeys = array_keys(JumlahLowonganPasker::STATUS_DISABILITAS_OPTIONS);
        
        // Daftar contoh yang diperluas untuk variasi lebih baik
        $provinsiList = [
            'DKI Jakarta', 'Jawa Barat', 'Banten', 'Jawa Timur', 'Sumatera Utara', 'Sulawesi Selatan', 
            'Kalimantan Timur', 'Jawa Tengah', 'DI Yogyakarta', 'Riau', 'Kepulauan Riau', 'Bali', 
            'Sumatera Selatan', 'Lampung', 'Kalimantan Selatan', 'Sulawesi Utara', 'Papua', 'Papua Barat'
        ];
        $lapanganUsahaList = [
            'Teknologi Informasi dan Komunikasi', 
            'Perdagangan Eceran dan Besar', 
            'Industri Makanan dan Minuman', 
            'Jasa Keuangan dan Asuransi', 
            'Konstruksi Bangunan dan Sipil',
            'Pendidikan dan Pelatihan',
            'Transportasi dan Pergudangan',
            'Aktivitas Kesehatan Manusia',
            'Penyediaan Akomodasi (Perhotelan)',
            'Manufaktur Umum',
            'Pertanian, Kehutanan, dan Perikanan',
            'Jasa Profesional, Ilmiah, dan Teknis',
            'Kesenian, Hiburan, dan Rekreasi'
        ];
        
        $tahunSekarang = (int) $now->year;

        for ($i = 0; $i < 20; $i++) {
            $tahunData = rand($tahunSekarang - 2, $tahunSekarang); // Tahun acak dari 2023-2025 (berdasarkan tahun saat ini)

            $newData[] = [
                'tahun' => $tahunData,
                'bulan' => rand(1, 12),
                'jenis_kelamin' => $faker->randomElement($jenisKelaminKeys),
                'provinsi_penempatan' => $faker->randomElement($provinsiList),
                'lapangan_usaha_kbli' => $faker->randomElement($lapanganUsahaList),
                'status_disabilitas' => $faker->randomElement($statusDisabilitasKeys),
                'jumlah_lowongan' => rand(3, 60), // Jumlah lowongan per record
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Menggunakan create() dalam loop untuk memicu event model Eloquent
        if (!empty($newData)) {
            foreach ($newData as $item) {
                JumlahLowonganPasker::create($item);
            }
            // Baris JumlahLowonganPasker::insert($data); sebelumnya juga valid namun tidak memicu event model
        }
    }
}