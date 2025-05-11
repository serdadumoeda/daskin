<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PengaduanPelanggaranNorma;
use Illuminate\Support\Carbon;

class PengaduanPelanggaranNormaSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $data = [
            [
                'tahun_pengaduan' => 2023,
                'bulan_pengaduan' => 9,
                'tahun_tindak_lanjut' => 2023,
                'bulan_tindak_lanjut' => 10,
                'provinsi' => 'Banten',
                'kbli' => 'C10', // Pembuatan Makanan
                'jenis_pelanggaran' => 'Upah Lembur',
                'jenis_tindak_lanjut' => 'pemeriksaan',
                'hasil_tindak_lanjut' => 'NP1',
                'jumlah_kasus' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tahun_pengaduan' => 2023,
                'bulan_pengaduan' => 11,
                'tahun_tindak_lanjut' => null, // Belum ditindaklanjuti
                'bulan_tindak_lanjut' => null,
                'provinsi' => 'Sumatera Utara',
                'kbli' => 'A01', // Pertanian Tanaman Semusim
                'jenis_pelanggaran' => 'K3',
                'jenis_tindak_lanjut' => 'atensi',
                'hasil_tindak_lanjut' => 'rekomendasi',
                'jumlah_kasus' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        if (!empty($data)) {
            PengaduanPelanggaranNorma::insert($data);
        }
    }
}