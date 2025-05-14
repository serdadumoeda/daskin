<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JumlahPenempatanKemnaker;
use Illuminate\Support\Carbon;

class JumlahPenempatanKemnakerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JumlahPenempatanKemnaker::truncate(); // Hapus data lama jika ada
        $now = Carbon::now();

        $data = [
            [
                'tahun' => 2023, 'bulan' => 1, 'jenis_kelamin' => 1, 'provinsi_domisili' => 'Jawa Barat', 
                'lapangan_usaha_kbli' => 'Industri Pengolahan', 'status_disabilitas' => 2, 'ragam_disabilitas' => null, 'jumlah' => 150,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2023, 'bulan' => 1, 'jenis_kelamin' => 2, 'provinsi_domisili' => 'Jawa Barat', 
                'lapangan_usaha_kbli' => 'Industri Pengolahan', 'status_disabilitas' => 2, 'ragam_disabilitas' => null, 'jumlah' => 120,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2023, 'bulan' => 2, 'jenis_kelamin' => 1, 'provinsi_domisili' => 'DKI Jakarta', 
                'lapangan_usaha_kbli' => 'Jasa Keuangan', 'status_disabilitas' => 1, 'ragam_disabilitas' => 'Disabilitas Fisik', 'jumlah' => 5,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2024, 'bulan' => 1, 'jenis_kelamin' => 2, 'provinsi_domisili' => 'Jawa Tengah', 
                'lapangan_usaha_kbli' => 'Perdagangan', 'status_disabilitas' => 2, 'ragam_disabilitas' => null, 'jumlah' => 80,
                'created_at' => $now, 'updated_at' => $now
            ],
             [
                'tahun' => 2024, 'bulan' => 1, 'jenis_kelamin' => 1, 'provinsi_domisili' => 'Jawa Tengah', 
                'lapangan_usaha_kbli' => 'Perdagangan', 'status_disabilitas' => 1, 'ragam_disabilitas' => 'Disabilitas Sensorik', 'jumlah' => 2,
                'created_at' => $now, 'updated_at' => $now
            ],
        ];

        JumlahPenempatanKemnaker::insert($data);
    }
}
