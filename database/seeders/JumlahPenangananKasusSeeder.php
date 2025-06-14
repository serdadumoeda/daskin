<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JumlahPenangananKasus;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker; // Tambahkan Faker

class JumlahPenangananKasusSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // Inisialisasi Faker
        $now = Carbon::now();
        $newData = []; // Array untuk data baru

        // Daftar contoh untuk substansi dan jenis perkara
        $substansiList = [
            "Hubungan Industrial dan Jaminan Sosial",
            "Pengawasan Ketenagakerjaan dan K3",
            "Perencanaan Tenaga Kerja dan Pengembangan SDM",
            "Pelatihan Vokasi dan Produktivitas",
            "Penempatan Tenaga Kerja Dalam Negeri",
            "Perlindungan Pekerja Migran Indonesia",
            "Hukum dan Regulasi Ketenagakerjaan Umum",
            "Kesekretariatan dan Dukungan Manajemen",
            "Mediasi dan Penyelesaian Perselisihan"
        ];

        $jenisPerkaraList = [
            'Putusan Mahkamah Agung (MA)',
            'Putusan Mahkamah Konstitusi (MK)',
            'Gugatan di Pengadilan Hubungan Industrial (PHI)',
            'Laporan Pengaduan ke Mediator HI',
            'Permohonan Uji Materiil Peraturan',
            'Sengketa Kewenangan Lembaga Negara (SKLN) terkait Ketenagakerjaan',
            'Banding atas Putusan PHI',
            'Kasasi atas Putusan Banding',
            'Peninjauan Kembali (PK) atas Putusan Kasasi',
            'Arbitrase Ketenagakerjaan'
        ];
        
        $tahunSekarang = (int) $now->year;

        for ($i = 0; $i < 20; $i++) {
            $tahun = rand($tahunSekarang - 2, $tahunSekarang); // Data untuk 3 tahun terakhir
            $bulan = rand(1, 12);

            $newData[] = [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'substansi' => $faker->randomElement($substansiList),
                'jenis_perkara' => $faker->randomElement($jenisPerkaraList),
                'jumlah_perkara' => rand(1, 12), // Jumlah perkara per record
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Hapus data contoh statis yang ada sebelumnya ($kasusData array)
        // Loop $newData untuk insert menggunakan create()
        if (!empty($newData)) {
            foreach ($newData as $item) {
                JumlahPenangananKasus::create($item);
            }
            // Alternatif: JumlahPenangananKasus::insert($newData); untuk bulk insert
        }
    }
}