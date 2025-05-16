<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JumlahRegulasiBaru;
use Illuminate\Support\Carbon;

class JumlahRegulasiBaruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $regulasiData = [
            [
                'tahun' => 2023,
                'bulan' => 1,
                // (4) Substansi: 1) Perencanaan dan Pengembangan, 2) Pelatihan Vokasi dan Produktivitas, ...
                'substansi' => 2, // Contoh: Pelatihan Vokasi dan Produktivitas
                // (5) Jenis Regulasi: ..., 6) Peraturan Menteri, ...
                'jenis_regulasi' => 6, // Contoh: Peraturan Menteri
                'jumlah_regulasi' => 5, // Pastikan nama kolom 'jumlah_regulasi'
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tahun' => 2023,
                'bulan' => 6,
                'substansi' => 7, // Contoh: Kesekretariatan
                'jenis_regulasi' => 6, // Contoh: Peraturan Menteri
                'jumlah_regulasi' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tahun' => 2023,
                'bulan' => 6,
                'substansi' => 7, // Contoh: Kesekretariatan
                'jenis_regulasi' => 7, // Contoh: Keputusan Menteri
                'jumlah_regulasi' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tahun' => 2024,
                'bulan' => 2,
                'substansi' => 1, // Contoh: Perencanaan dan Pengembangan
                'jenis_regulasi' => 1, // Contoh: Undang-Undang
                'jumlah_regulasi' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tahun' => 2024,
                'bulan' => 3,
                'substansi' => 4, // Contoh: Hubungan Industrial dan Jaminan Sosial
                'jenis_regulasi' => 2, // Contoh: Peraturan Pemerintah
                'jumlah_regulasi' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Tambahkan data lain sesuai kebutuhan
            // Contoh:
            // [
            //     'tahun' => 2024,
            //     'bulan' => 5,
            //     'substansi' => 8, // Lainnya
            //     'jenis_regulasi' => 10, // Peraturan Terkait
            //     'jumlah_regulasi' => 10,
            //     'created_at' => $now,
            //     'updated_at' => $now,
            // ],
        ];

        // Hapus data lama jika diperlukan (opsional, tergantung kebutuhan)
        // JumlahRegulasiBaru::truncate(); 

        // Insert data baru
        if (!empty($regulasiData)) {
            JumlahRegulasiBaru::insert($regulasiData);
        }

        $this->command->info('Seeder JumlahRegulasiBaru berhasil dijalankan.');
    }
}