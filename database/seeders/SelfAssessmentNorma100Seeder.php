<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SelfAssessmentNorma100;
use Illuminate\Support\Carbon;

class SelfAssessmentNorma100Seeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $data = [
            [
                'bulan' => 11,
                'tahun' => 2023,
                'provinsi' => 'Jawa Tengah',
                'kbli' => 'C22', // Industri Karet Dan Plastik
                'skala_perusahaan' => 'Menengah',
                'hasil_assessment' => 'Tinggi (91-100)',
                'jumlah_perusahaan' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'bulan' => 11,
                'tahun' => 2023,
                'provinsi' => 'Jawa Tengah',
                'kbli' => 'C22',
                'skala_perusahaan' => 'Kecil',
                'hasil_assessment' => 'Sedang (71-90)',
                'jumlah_perusahaan' => 50,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        if (!empty($data)) {
            SelfAssessmentNorma100::insert($data);
        }
    }
}