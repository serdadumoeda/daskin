<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JumlahKajianRekomendasi;
use Illuminate\Support\Carbon;

class JumlahKajianRekomendasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // JumlahKajianRekomendasi::truncate(); // Opsional
        $now = Carbon::now();

        $data = [
            [
                'tahun' => 2023, 'bulan' => 3, 'substansi' => 1, 'jenis_output' => 1, // Pelatihan Vokasi, Kajian
                'jumlah' => 5, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2023, 'bulan' => 4, 'substansi' => 2, 'jenis_output' => 2, // Penempatan, Rekomendasi
                'jumlah' => 3, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2024, 'bulan' => 1, 'substansi' => 3, 'jenis_output' => 1, // Hubungan Industrial, Kajian
                'jumlah' => 2, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2024, 'bulan' => 2, 'substansi' => 4, 'jenis_output' => 2, // Pengawasan, Rekomendasi
                'jumlah' => 4, 'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2024, 'bulan' => 3, 'substansi' => 5, 'jenis_output' => 1, // Lainnya, Kajian
                'jumlah' => 1, 'created_at' => $now, 'updated_at' => $now
            ],
        ];

        foreach ($data as $item) {
            JumlahKajianRekomendasi::firstOrCreate(
                [ // Kunci untuk firstOrCreate
                    'tahun' => $item['tahun'],
                    'bulan' => $item['bulan'],
                    'substansi' => $item['substansi'],
                    'jenis_output' => $item['jenis_output'],
                ],
                $item // Data lengkap untuk create atau update
            );
        }
    }
}
