<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PenerapanSmk3;
use Illuminate\Support\Carbon;

class PenerapanSmk3Seeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $data = [
            [
                'tahun' => 2023,
                'bulan' => 12,
                'provinsi' => 'Kalimantan Timur',
                'kbli' => 'B05', // Pertambangan Batu Bara Dan Lignit
                'kategori_penilaian' => 'lanjutan',
                'tingkat_pencapaian' => 'memuaskan',
                'jenis_penghargaan' => 'sertifikat emas dan bendera emas',
                'jumlah_perusahaan' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tahun' => 2023,
                'bulan' => 12,
                'provinsi' => 'Riau',
                'kbli' => 'C19', // Industri Produk Dari Batu Bara Dan Pengilangan Minyak Bumi
                'kategori_penilaian' => 'transisi',
                'tingkat_pencapaian' => 'baik',
                'jenis_penghargaan' => 'sertifikat perak',
                'jumlah_perusahaan' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        if (!empty($data)) {
            PenerapanSmk3::insert($data);
        }
    }
}