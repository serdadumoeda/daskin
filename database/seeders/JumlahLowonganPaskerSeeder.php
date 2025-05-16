<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JumlahLowonganPasker;
use Illuminate\Support\Carbon;

class JumlahLowonganPaskerSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $data = [];

        $jenisKelaminKeys = array_keys(JumlahLowonganPasker::JENIS_KELAMIN_OPTIONS);
        $statusDisabilitasKeys = array_keys(JumlahLowonganPasker::STATUS_DISABILITAS_OPTIONS);
        
        $provinsiContoh = ['DKI Jakarta', 'Jawa Barat', 'Banten', 'Jawa Timur', 'Sumatera Utara', 'Sulawesi Selatan', 'Kalimantan Timur'];
        $lapanganUsahaContoh = [
            'Teknologi Informasi dan Komunikasi', 
            'Perdagangan Eceran', 
            'Industri Makanan dan Minuman', 
            'Jasa Keuangan dan Asuransi', 
            'Konstruksi Bangunan',
            'Pendidikan',
            'Transportasi dan Pergudangan'
        ];

        for ($i = 0; $i < 20; $i++) {
            $data[] = [
                'tahun' => 2024 + $i % 2,
                'bulan' => rand(1, 12),
                'jenis_kelamin' => $jenisKelaminKeys[array_rand($jenisKelaminKeys)],
                'provinsi_penempatan' => $provinsiContoh[array_rand($provinsiContoh)],
                'lapangan_usaha_kbli' => $lapanganUsahaContoh[array_rand($lapanganUsahaContoh)],
                'status_disabilitas' => $statusDisabilitasKeys[array_rand($statusDisabilitasKeys)],
                'jumlah_lowongan' => rand(5, 50),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($data)) {
            JumlahLowonganPasker::insert($data);
        }
    }
}