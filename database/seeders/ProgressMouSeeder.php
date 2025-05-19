<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProgressMou;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker; // Tambahkan ini untuk menggunakan Faker

class ProgressMouSeeder extends Seeder
{
    public function run(): void
    {
        // Inisialisasi Faker, 'id_ID' untuk data berbahasa Indonesia jika relevan
        $faker = Faker::create('id_ID'); 
        $now = Carbon::now();
        $mouData = []; // Kosongkan array data lama

        $tahunSekarang = (int) $now->year;

        for ($i = 0; $i < 20; $i++) {
            $tahun = rand($tahunSekarang - 2, $tahunSekarang); // Data untuk 3 tahun terakhir termasuk tahun ini
            $bulan = rand(1, 12);

            // Tentukan hari secara acak dalam bulan dan tahun yang dipilih
            // Pastikan tanggal valid (misalnya, tidak ada 31 Februari)
            $hariRandom = rand(1, Carbon::create($tahun, $bulan)->daysInMonth);
            $tanggalMulai = Carbon::createFromDate($tahun, $bulan, $hariRandom);

            // Tanggal selesai bisa null atau beberapa tahun setelah tanggal mulai
            $tanggalSelesai = null;
            if ($faker->boolean(75)) { // 75% kemungkinan memiliki tanggal selesai
                $tanggalSelesai = $tanggalMulai->copy()->addYears(rand(1, 4))->addMonths(rand(0, 11))->addDays(rand(0, 28));
            }

            // Contoh judul MoU dan pihak terlibat menggunakan Faker
            $daftarTopik = ['Pelatihan Vokasi', 'Perlindungan Pekerja Migran', 'Pengembangan SDM', 'Keselamatan dan Kesehatan Kerja (K3)', 'Hubungan Industrial Harmonis', 'Digitalisasi Layanan Ketenagakerjaan', 'Penempatan Tenaga Kerja'];
            $topikMou = $faker->randomElement($daftarTopik);
            
            $jenisInstitusi = ['Industri', 'Universitas', 'Lembaga Pelatihan', 'Pemerintah Daerah', 'Organisasi Internasional', 'Asosiasi Pengusaha'];
            $pihakEksternal1 = $faker->company() . ' (' . $faker->randomElement($jenisInstitusi) . ')';
            
            $pihakTerlibat = 'Kementerian Ketenagakerjaan RI, ' . $pihakEksternal1;
            if ($faker->boolean(50)) { // 50% kemungkinan ada pihak ketiga
                $pihakEksternal2 = $faker->company() . ' (' . $faker->randomElement($jenisInstitusi) . ')';
                $pihakTerlibat .= ', ' . $pihakEksternal2;
            }


            $mouData[] = [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'judul_mou' => 'Nota Kesepahaman tentang ' . $topikMou . ' antara ' . $pihakTerlibat,
                'tanggal_mulai_perjanjian' => $tanggalMulai->toDateString(), // Format YYYY-MM-DD
                'tanggal_selesai_perjanjian' => $tanggalSelesai ? $tanggalSelesai->toDateString() : null,
                'pihak_terlibat' => $pihakTerlibat,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Hapus data yang ada di $mouData dari contoh seeder sebelumnya (jika ada)
        // Kode di atas ($mouData = [];) sudah menangani ini.

        if (!empty($mouData)) {
            // Menggunakan create() dalam loop untuk memastikan mutator dan event model terpanggil
            foreach ($mouData as $data) {
                ProgressMou::create($data);
            }
            // Alternatif: ProgressMou::insert($mouData); untuk performa lebih baik pada data besar,
            // namun tidak memicu event/mutator model.
        }
    }
}