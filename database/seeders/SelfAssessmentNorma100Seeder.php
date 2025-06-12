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
        $newData = [];
        $tahunSekarang = (int) $now->year;

        for ($i=0; $i < 20; $i++) {
            $tahun = rand($tahunSekarang - 2, $tahunSekarang);
            $bulan = rand(1, 12);

            $newData[] = [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'jumlah_perusahaan' => rand(1, 100),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($newData)) {
            foreach ($newData as $item) {
                SelfAssessmentNorma100::insert($item);
            }
        }
    }
}
