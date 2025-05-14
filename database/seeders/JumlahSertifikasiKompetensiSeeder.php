<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JumlahSertifikasiKompetensi;
use Illuminate\Support\Carbon;

class JumlahSertifikasiKompetensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // JumlahSertifikasiKompetensi::truncate(); // Opsional
        $now = Carbon::now();

        $data = [
            [
                'tahun' => 2023, 'bulan' => 1, 'jenis_lsp' => 1, // P1
                'jenis_kelamin' => 1, 'provinsi' => 'Jawa Barat', 
                'lapangan_usaha_kbli' => 'Manufaktur', 'jumlah_sertifikasi' => 120,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2023, 'bulan' => 1, 'jenis_lsp' => 2, // P2
                'jenis_kelamin' => 2, 'provinsi' => 'DKI Jakarta', 
                'lapangan_usaha_kbli' => 'Teknologi Informasi', 'jumlah_sertifikasi' => 85,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2024, 'bulan' => 2, 'jenis_lsp' => 3, // P3
                'jenis_kelamin' => 1, 'provinsi' => 'Jawa Timur', 
                'lapangan_usaha_kbli' => 'Konstruksi', 'jumlah_sertifikasi' => 200,
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2024, 'bulan' => 2, 'jenis_lsp' => 1, // P1
                'jenis_kelamin' => 2, 'provinsi' => 'Sumatera Utara', 
                'lapangan_usaha_kbli' => 'Pariwisata', 'jumlah_sertifikasi' => 55,
                'created_at' => $now, 'updated_at' => $now
            ],
        ];

        foreach ($data as $item) {
            JumlahSertifikasiKompetensi::firstOrCreate(
                [ // Kunci untuk firstOrCreate
                    'tahun' => $item['tahun'],
                    'bulan' => $item['bulan'],
                    'jenis_lsp' => $item['jenis_lsp'],
                    'jenis_kelamin' => $item['jenis_kelamin'],
                    'provinsi' => $item['provinsi'],
                    'lapangan_usaha_kbli' => $item['lapangan_usaha_kbli'],
                ],
                $item // Data lengkap untuk create atau update
            );
        }
    }
}
