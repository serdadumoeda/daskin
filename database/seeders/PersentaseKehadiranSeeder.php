<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PersentaseKehadiran;
// Tidak perlu lagi UnitKerjaEselonI dan SatuanKerja jika menggunakan daftar kode manual
use Illuminate\Support\Carbon;
use Faker\Factory as Faker; // Opsional, tapi bisa digunakan untuk randomElement

class PersentaseKehadiranSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create(); // Inisialisasi Faker
        $now = Carbon::now();
        $newData = []; // Array untuk menampung data baru

        // Daftar contoh Satuan Kerja yang valid (kode_uke1 & kode_sk)
        // Ambil dari SatuanKerjaSeeder atau definisikan di sini
        $availableSatuanKerja = [
            ['kode_uke1' => 'UKE1-001', 'kode_sk' => 'SK-001'], // Biro Perencanaan dan Manajemen Kinerja
            ['kode_uke1' => 'UKE1-001', 'kode_sk' => 'SK-003'], // Biro Organisasi dan SDM Aparatur
            ['kode_uke1' => 'UKE1-001', 'kode_sk' => 'SK-004'], // Biro Hukum
            ['kode_uke1' => 'UKE1-002', 'kode_sk' => 'SK-100'], // Sekretariat Ditjen Binalavotas
            ['kode_uke1' => 'UKE1-002', 'kode_sk' => 'SK-106'], // BBPVP Medan
            ['kode_uke1' => 'UKE1-003', 'kode_sk' => 'SK-201'], // Direktorat Bina Penempatan Tenaga Kerja Dalam Negeri
            ['kode_uke1' => 'UKE1-004', 'kode_sk' => 'SK-302'], // Direktorat Bina Persyaratan Kerja...
            ['kode_uke1' => 'UKE1-005', 'kode_sk' => 'SK-403'], // Direktorat Bina Pengujian K3
            ['kode_uke1' => 'UKE1-006', 'kode_sk' => 'SK-501'], // Pusat Perencanaan Ketenagakerjaan
            ['kode_uke1' => 'UKE1-007', 'kode_sk' => 'SK-602'], // Inspektorat II
        ];

        // Opsi untuk status_asn dan status_kehadiran (berdasarkan model/migrasi)
        $statusAsnOptions = [1, 2]; // 1: ASN, 2: Non ASN
        $statusKehadiranOptions = [1, 2, 3, 4, 5, 6]; // 1: WFO, 2: Cuti, ..., 6: Tanpa Keterangan

        $tahunSekarang = (int) $now->year;

        for ($i = 0; $i < 20; $i++) {
            // Pilih Satuan Kerja secara acak dari daftar
            $selectedSatuanKerja = $faker->randomElement($availableSatuanKerja);

            $tahun = rand($tahunSekarang - 2, $tahunSekarang); // Data untuk 3 tahun terakhir
            $bulan = rand(1, 12);
            $statusAsn = $faker->randomElement($statusAsnOptions);
            $statusKehadiran = $faker->randomElement($statusKehadiranOptions);

            // Jumlah orang bisa disesuaikan, misal lebih kecil untuk cuti/sakit
            $jumlahOrang = 0;
            switch ($statusKehadiran) {
                case 1: // WFO
                    $jumlahOrang = rand(20, 100);
                    break;
                case 2: // Cuti
                case 4: // Sakit
                    $jumlahOrang = rand(0, 5);
                    break;
                case 3: // Dinas Luar
                case 5: // Tugas Belajar
                    $jumlahOrang = rand(0, 10);
                    break;
                case 6: // Tanpa Keterangan
                    $jumlahOrang = rand(0, 2);
                    break;
            }
            
            // Pastikan kombinasi unik per bulan per satker per status jika diperlukan,
            // namun untuk seeder 20 data acak, ini mungkin tidak krusial.
            // Jika ingin memastikan data lebih terdistribusi per bulan/satker,
            // Anda bisa membuat loop yang lebih kompleks.

            $newData[] = [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'kode_unit_kerja_eselon_i' => $selectedSatuanKerja['kode_uke1'],
                'kode_satuan_kerja' => $selectedSatuanKerja['kode_sk'],
                'status_asn' => $statusAsn,
                'status_kehadiran' => $statusKehadiran,
                'jumlah_orang' => $jumlahOrang,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        
        // Hapus logika pengisian $kehadiranData yang lama

        // Insert data menggunakan create() untuk memicu event model
        if (!empty($newData)) {
            foreach ($newData as $data) {
                PersentaseKehadiran::create($data);
            }
            // Alternatif: PersentaseKehadiran::insert($newData);
        }
    }
}