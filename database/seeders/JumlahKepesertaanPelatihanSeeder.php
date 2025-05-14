<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JumlahKepesertaanPelatihan;
use Illuminate\Support\Carbon;

class JumlahKepesertaanPelatihanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // JumlahKepesertaanPelatihan::truncate(); // Opsional
        $now = Carbon::now();

        $data = [
            [
                'tahun' => 2023, 'bulan' => 1, 'penyelenggara_pelatihan' => 1, 'tipe_lembaga' => 1, // Internal, UPTP
                'jenis_kelamin' => 1, 'provinsi_tempat_pelatihan' => 'Jawa Barat', 'kejuruan' => 'Operator Mesin CNC',
                'status_kelulusan' => 1, 'jumlah' => 20, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2023, 'bulan' => 1, 'penyelenggara_pelatihan' => 1, 'tipe_lembaga' => 1, // Internal, UPTP
                'jenis_kelamin' => 2, 'provinsi_tempat_pelatihan' => 'Jawa Barat', 'kejuruan' => 'Operator Mesin CNC',
                'status_kelulusan' => 1, 'jumlah' => 15, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2023, 'bulan' => 2, 'penyelenggara_pelatihan' => 2, 'tipe_lembaga' => 6, // Eksternal, LPK Swasta
                'jenis_kelamin' => 1, 'provinsi_tempat_pelatihan' => 'DKI Jakarta', 'kejuruan' => 'Digital Marketing',
                'status_kelulusan' => 1, 'jumlah' => 30, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2024, 'bulan' => 1, 'penyelenggara_pelatihan' => 1, 'tipe_lembaga' => 7, // Internal, BLK Komunitas
                'jenis_kelamin' => 2, 'provinsi_tempat_pelatihan' => 'Jawa Tengah', 'kejuruan' => 'Menjahit Pakaian Wanita',
                'status_kelulusan' => 2, 'jumlah' => 5, 'created_at' => $now, 'updated_at' => $now // Tidak Lulus
            ],
             [
                'tahun' => 2024, 'bulan' => 1, 'penyelenggara_pelatihan' => 1, 'tipe_lembaga' => 7, // Internal, BLK Komunitas
                'jenis_kelamin' => 2, 'provinsi_tempat_pelatihan' => 'Jawa Tengah', 'kejuruan' => 'Menjahit Pakaian Wanita',
                'status_kelulusan' => 1, 'jumlah' => 25, 'created_at' => $now, 'updated_at' => $now // Lulus
            ],
        ];

        foreach ($data as $item) {
            JumlahKepesertaanPelatihan::firstOrCreate(
                [ // Kunci untuk firstOrCreate
                    'tahun' => $item['tahun'],
                    'bulan' => $item['bulan'],
                    'penyelenggara_pelatihan' => $item['penyelenggara_pelatihan'],
                    'tipe_lembaga' => $item['tipe_lembaga'],
                    'jenis_kelamin' => $item['jenis_kelamin'],
                    'provinsi_tempat_pelatihan' => $item['provinsi_tempat_pelatihan'],
                    'kejuruan' => $item['kejuruan'],
                    'status_kelulusan' => $item['status_kelulusan'],
                ],
                $item // Data lengkap untuk create atau update
            );
        }
    }
}
