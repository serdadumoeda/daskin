<?php

namespace Database\Seeders;

use App\Models\IKPA;
use App\Models\UnitKerjaEselonI;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class IKPASeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $available_uke = [
            ['kode_uke1' => 'UKE1-001'], // Biro Perencanaan dan Manajemen Kinerja
            ['kode_uke1' => 'UKE1-002'], // Biro Hukum
            ['kode_uke1' => 'UKE1-003'], // Sekretariat Ditjen Binalavotas
            ['kode_uke1' => 'UKE1-004'], // BBPVP Medan
            ['kode_uke1' => 'UKE1-005'], // Direktorat Bina Penempatan Tenaga Kerja Dalam Negeri
            ['kode_uke1' => 'UKE1-006'], // Direktorat Bina Persyaratan Kerja...
            ['kode_uke1' => 'UKE1-007'], // Direktorat Bina Pengujian K3
            ['kode_uke1' => 'UKE1-008'], // Pusat Perencanaan Ketenagakerjaan
            ['kode_uke1' => 'UKE1-009'], // Inspektorat II
            ['kode_uke1' => 'UKE1-010'], // Inspektorat II
            ['kode_uke1' => 'UKE1-011'], // Inspektorat II
        ];

        $tahunSekarang = (int) $now->year;
        $aspekPelaksanaanAnggaranOptions = [
            'Kualitas Perencanaan Anggaran',
            'Kualitas Pelaksanaan Anggaran',
            'Kualitas Hasil Pelaksanaan Anggaran',
            'Total'
        ];

        $datas = [];

        for ($i=0; $i < 20; $i++) {
            $tahun = rand($tahunSekarang - 2, $tahunSekarang);
            $bulan = rand(1, 12);

            $datas[] = [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'kode_unit_kerja_eselon_i' => UnitKerjaEselonI::where('kode_uke1', $available_uke[rand(0, 10)]['kode_uke1'])->first()->kode_uke1,
                'aspek_pelaksanaan_anggaran' => $aspekPelaksanaanAnggaranOptions[rand(0, 3)],
                'nilai_aspek' => rand(0, 100),
                'konversi_bobot' => rand(0, 100),
                'dispensasi_spm' => rand(0, 100),
                'nilai_akhir' => rand(0, 100),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($datas)) {
            foreach ($datas as $data) {
                IKPA::create($data);
            }
        }
    }
}
