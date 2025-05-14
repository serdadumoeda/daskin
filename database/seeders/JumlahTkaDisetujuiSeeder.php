<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JumlahTkaDisetujui;
use Illuminate\Support\Carbon;

class JumlahTkaDisetujuiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // JumlahTkaDisetujui::truncate(); // Opsional
        $now = Carbon::now();

        $data = [
            [
                'tahun' => 2023, 'bulan' => 10, 'jenis_kelamin' => 1, 'negara_asal' => 'Jepang', 
                'jabatan' => 'Technical Advisor', 'lapangan_usaha_kbli' => 'Industri Otomotif (C29)', 
                'provinsi_penempatan' => 'Jawa Barat', 'jumlah_tka' => 3,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2023, 'bulan' => 11, 'jenis_kelamin' => 2, 'negara_asal' => 'Korea Selatan', 
                'jabatan' => 'Marketing Director', 'lapangan_usaha_kbli' => 'Perdagangan Besar (G46)', 
                'provinsi_penempatan' => 'DKI Jakarta', 'jumlah_tka' => 1,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2024, 'bulan' => 1, 'jenis_kelamin' => 1, 'negara_asal' => 'India', 
                'jabatan' => 'IT Consultant', 'lapangan_usaha_kbli' => 'Jasa Konsultasi Manajemen (M70)', 
                'provinsi_penempatan' => 'DKI Jakarta', 'jumlah_tka' => 5,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2024, 'bulan' => 2, 'jenis_kelamin' => 1, 'negara_asal' => 'Australia', 
                'jabatan' => 'Chief Engineer', 'lapangan_usaha_kbli' => 'Pertambangan (B07)', 
                'provinsi_penempatan' => 'Kalimantan Timur', 'jumlah_tka' => 2,
                'created_at' => $now, 'updated_at' => $now
            ],
        ];

        foreach ($data as $item) {
            JumlahTkaDisetujui::firstOrCreate(
                [ // Kunci untuk firstOrCreate
                    'tahun' => $item['tahun'],
                    'bulan' => $item['bulan'],
                    'negara_asal' => $item['negara_asal'],
                    'jabatan' => $item['jabatan'],
                    'lapangan_usaha_kbli' => $item['lapangan_usaha_kbli'],
                    'provinsi_penempatan' => $item['provinsi_penempatan'],
                    'jenis_kelamin' => $item['jenis_kelamin'],
                ],
                $item // Data lengkap untuk create atau update
            );
        }
    }
}
