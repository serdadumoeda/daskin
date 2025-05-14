<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AplikasiIntegrasiSiapkerja;
use Illuminate\Support\Carbon;

class AplikasiIntegrasiSiapkerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // AplikasiIntegrasiSiapkerja::truncate(); // Opsional
        $now = Carbon::now();

        $data = [
            [
                'tahun' => 2023, 'bulan' => 5, 'jenis_instansi' => 1, 'nama_instansi' => 'Kementerian Koperasi dan UKM',
                'nama_aplikasi_website' => 'SISKOP UKM', 'status_integrasi' => 1, // Terintegrasi
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2023, 'bulan' => 6, 'jenis_instansi' => 3, 'nama_instansi' => 'Dinas Tenaga Kerja Provinsi Jawa Barat',
                'nama_aplikasi_website' => 'JabarKarier', 'status_integrasi' => 1, // Terintegrasi
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2024, 'bulan' => 1, 'jenis_instansi' => 4, 'nama_instansi' => 'Dinas Ketenagakerjaan Kota Semarang',
                'nama_aplikasi_website' => 'Semaker Mobile', 'status_integrasi' => 2, // Belum Terintegrasi
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'tahun' => 2024, 'bulan' => 2, 'jenis_instansi' => 2, 'nama_instansi' => 'Badan Pelindungan Pekerja Migran Indonesia (BP2MI)',
                'nama_aplikasi_website' => 'SISKOP2MI', 'status_integrasi' => 1, // Terintegrasi
                'created_at' => $now, 'updated_at' => $now
            ],
        ];

        foreach ($data as $item) {
            AplikasiIntegrasiSiapkerja::firstOrCreate(
                [ // Kunci untuk firstOrCreate
                    'tahun' => $item['tahun'],
                    'bulan' => $item['bulan'],
                    'nama_instansi' => $item['nama_instansi'],
                    'nama_aplikasi_website' => $item['nama_aplikasi_website'],
                ],
                $item // Data lengkap untuk create atau update
            );
        }
    }
}
