<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MediasiBerhasil;
use Illuminate\Support\Carbon;

class MediasiBerhasilSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $data = [
            [
                'tahun' => 2023,
                'bulan' => 11,
                'provinsi' => 'Jawa Barat',
                'kbli' => 'C13', // Industri Tekstil
                'jenis_perselisihan' => 'Perselisihan PHK',
                'hasil_mediasi' => 'PB', // Perjanjian Bersama
                'jumlah_mediasi' => 15,
                'jumlah_mediasi_berhasil' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tahun' => 2024,
                'bulan' => 3,
                'provinsi' => 'Jawa Timur',
                'kbli' => 'H52', // Pergudangan Dan Aktivitas Penunjang Angkutan
                'jenis_perselisihan' => 'Perselisihan Kepentingan',
                'hasil_mediasi' => 'anjuran',
                'jumlah_mediasi' => 8,
                'jumlah_mediasi_berhasil' => 6,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        if (!empty($data)) {
            MediasiBerhasil::insert($data);
        }
    }
}