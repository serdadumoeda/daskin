<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PerselisihanDitindaklanjuti;
use Illuminate\Support\Carbon;

class PerselisihanDitindaklanjutiSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $data = [
            [
                'tahun' => 2023,
                'bulan' => 7,
                'provinsi' => 'DKI Jakarta',
                'kbli' => 'J61', // Aktivitas Telekomunikasi
                'jenis_perselisihan' => 'Perselisihan PHK',
                'cara_penyelesaian' => 'Mediasi',
                'jumlah_perselisihan' => 10,
                'jumlah_ditindaklanjuti' => 8,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tahun' => 2024,
                'bulan' => 2,
                'provinsi' => 'Banten',
                'kbli' => 'C20', // Industri Bahan Kimia Dan Barang Dari Bahan Kimia
                'jenis_perselisihan' => 'Perselisihan Hak',
                'cara_penyelesaian' => 'Bipartit',
                'jumlah_perselisihan' => 5,
                'jumlah_ditindaklanjuti' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        if (!empty($data)) {
            PerselisihanDitindaklanjuti::insert($data);
        }
    }
}