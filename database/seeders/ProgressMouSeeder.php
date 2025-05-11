<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProgressMou;
use Illuminate\Support\Carbon;

class ProgressMouSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $mouData = [
            [
                'tahun' => 2023,
                'bulan' => 5,
                'judul_mou' => 'MoU Kerjasama Pelatihan Vokasi dengan Industri A',
                'tanggal_mulai_perjanjian' => '2023-05-15',
                'tanggal_selesai_perjanjian' => '2025-05-14',
                'pihak_terlibat' => 'Kemnaker, Industri A, Polteknaker X',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tahun' => 2024,
                'bulan' => 1,
                'judul_mou' => 'Nota Kesepahaman Perlindungan Pekerja Migran',
                'tanggal_mulai_perjanjian' => '2024-01-20',
                'tanggal_selesai_perjanjian' => null, // Bisa jadi tidak ada tanggal selesai
                'pihak_terlibat' => 'Kemnaker, BP2MI, Kementerian Luar Negeri',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        if (!empty($mouData)) {
            ProgressMou::insert($mouData);
        }
    }
}