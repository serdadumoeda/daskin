<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PersetujuanRptka;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker; // Tambahkan ini untuk menggunakan Faker

class PersetujuanRptkaSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create(); // Inisialisasi Faker
        $now = Carbon::now();
        $newData = []; // Ganti nama variabel agar jelas ini data baru

        // Mengambil kunci dari opsi yang ada di model
        $jenisKelaminKeys = array_keys(PersetujuanRptka::JENIS_KELAMIN_OPTIONS);
        $jabatanKeys = array_keys(PersetujuanRptka::JABATAN_OPTIONS);
        $statusKeys = array_keys(PersetujuanRptka::STATUS_PENGAJUAN_OPTIONS);

        // Daftar contoh yang sudah ada bisa tetap digunakan atau diperluas
        $negaraAsalContoh = ['Jepang', 'Korea Selatan', 'Tiongkok', 'India', 'Malaysia', 'Amerika Serikat', 'Australia', 'Singapura', 'Thailand', 'Filipina', 'Vietnam', 'Jerman', 'Inggris', 'Prancis', 'Kanada'];
        $provinsiContoh = [
            'DKI Jakarta', 'Jawa Barat', 'Banten', 'Jawa Timur', 'Sumatera Utara', 
            'Lintas Provinsi', 'Kalimantan Timur', 'Jawa Tengah', 'DI Yogyakarta', 
            'Kepulauan Riau', 'Bali', 'Sulawesi Selatan'
        ];
        // Contoh data Lapangan Usaha (KBLI) sebagai string deskriptif
        $lapanganUsahaContoh = [
            'Pertanian Tanaman Semusim', 
            'Industri Pengolahan Tembakau', 
            'Konstruksi Gedung', 
            'Perdagangan Besar Mobil Bekas', 
            'Angkutan Sungai dan Danau',
            'Penyediaan Akomodasi Jangka Pendek',
            'Kegiatan Profesional, Ilmiah Dan Teknis Lainnya Ytdl',
            'Industri Pakaian Jadi',
            'Aktivitas Jasa Informasi',
            'Perdagangan Eceran Melalui Media Untuk Komoditi Makanan, Minuman, Tembakau, Kimia, Farmasi, Kosmetik, Dan Alat Laboratorium',
            'Real Estat Atas Dasar Balas Jasa (Fee) Atau Kontrak'
        ];
        
        $currentYear = (int) $now->year;

        for ($i = 0; $i < 20; $i++) { 
            $tahunData = rand($currentYear - 2, $currentYear); // Tahun data dari 2023 hingga 2025 (berdasarkan tahun saat ini)

            $newData[] = [
                'tahun' => $tahunData,
                'bulan' => rand(1, 12),
                'jenis_kelamin' => $faker->randomElement($jenisKelaminKeys),
                'negara_asal' => $faker->randomElement($negaraAsalContoh), // Bisa juga menggunakan $faker->country() jika ingin lebih beragam
                'jabatan' => $faker->randomElement($jabatanKeys),
                'lapangan_usaha_kbli' => $faker->randomElement($lapanganUsahaContoh), 
                'provinsi_penempatan' => $faker->randomElement($provinsiContoh), 
                'status_pengajuan' => $faker->randomElement($statusKeys),
                'jumlah' => rand(1, 10), // Jumlah TKA per pengajuan
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Menggunakan create() dalam loop untuk memicu event model Eloquent
        if (!empty($newData)) {
            foreach ($newData as $dataRptka) {
                PersetujuanRptka::create($dataRptka);
            }
            // Baris PersetujuanRptka::insert($data); sebelumnya juga valid namun tidak memicu event model
        }
    }
}