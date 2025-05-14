<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JumlahLowonganPasker;
use Illuminate\Support\Carbon;

class JumlahLowonganPaskerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // JumlahLowonganPasker::truncate(); // Opsional
        $now = Carbon::now();

        $data = [
            [
                'tahun' => 2023, 'bulan' => 10, 'provinsi_perusahaan' => 'DKI Jakarta', 
                'lapangan_usaha_kbli' => 'Teknologi Informasi (J)', 'jabatan' => 'Software Engineer', 
                'jenis_kelamin_dibutuhkan' => 3, 'status_disabilitas_dibutuhkan' => 2, 'jumlah_lowongan' => 15,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2023, 'bulan' => 11, 'provinsi_perusahaan' => 'Jawa Barat', 
                'lapangan_usaha_kbli' => 'Industri Pengolahan (C)', 'jabatan' => 'Operator Produksi', 
                'jenis_kelamin_dibutuhkan' => 1, 'status_disabilitas_dibutuhkan' => 2, 'jumlah_lowongan' => 50,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2024, 'bulan' => 1, 'provinsi_perusahaan' => 'DKI Jakarta', 
                'lapangan_usaha_kbli' => 'Jasa Profesional (M)', 'jabatan' => 'Staf Administrasi', 
                'jenis_kelamin_dibutuhkan' => 2, 'status_disabilitas_dibutuhkan' => 1, 'jumlah_lowongan' => 3,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2024, 'bulan' => 2, 'provinsi_perusahaan' => 'Jawa Timur', 
                'lapangan_usaha_kbli' => 'Perdagangan (G)', 'jabatan' => 'Sales Marketing', 
                'jenis_kelamin_dibutuhkan' => 3, 'status_disabilitas_dibutuhkan' => 2, 'jumlah_lowongan' => 20,
                'created_at' => $now, 'updated_at' => $now
            ],
        ];

        foreach ($data as $item) {
            JumlahLowonganPasker::firstOrCreate(
                [ // Kunci untuk firstOrCreate
                    'tahun' => $item['tahun'],
                    'bulan' => $item['bulan'],
                    'provinsi_perusahaan' => $item['provinsi_perusahaan'],
                    'lapangan_usaha_kbli' => $item['lapangan_usaha_kbli'],
                    'jabatan' => $item['jabatan'],
                    'jenis_kelamin_dibutuhkan' => $item['jenis_kelamin_dibutuhkan'],
                    'status_disabilitas_dibutuhkan' => $item['status_disabilitas_dibutuhkan'],
                ],
                $item // Data lengkap untuk create atau update
            );
        }
    }
}
