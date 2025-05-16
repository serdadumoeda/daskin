<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PersetujuanRptka;
use Illuminate\Support\Carbon;

class PersetujuanRptkaSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $data = [];

        $jenisKelaminKeys = array_keys(PersetujuanRptka::JENIS_KELAMIN_OPTIONS);
        $jabatanKeys = array_keys(PersetujuanRptka::JABATAN_OPTIONS);
        // Hapus $lapanganUsahaKeys karena lapangan_usaha_kbli sekarang string bebas
        // $lapanganUsahaKeys = array_keys(PersetujuanRptka::LAPANGAN_USAHA_KBLI_OPTIONS); 
        $statusKeys = array_keys(PersetujuanRptka::STATUS_PENGAJUAN_OPTIONS);

        $negaraAsalContoh = ['Jepang', 'Korea Selatan', 'Tiongkok', 'India', 'Malaysia', 'Amerika Serikat', 'Australia'];
        $provinsiContoh = ['DKI Jakarta', 'Jawa Barat', 'Banten', 'Jawa Timur', 'Sumatera Utara', 'Lintas Provinsi', 'Kalimantan Timur'];
        // Contoh data Lapangan Usaha (KBLI) sebagai string
        $lapanganUsahaContoh = [
            'Pertanian Tanaman Semusim', 
            'Industri Pengolahan Tembakau', 
            'Konstruksi Gedung', 
            'Perdagangan Besar Mobil Bekas', 
            'Angkutan Sungai dan Danau',
            'Penyediaan Akomodasi Jangka Pendek',
            'Kegiatan Profesional, Ilmiah Dan Teknis Lainnya Ytdl'
        ];


        for ($i = 0; $i < 20; $i++) { 
            $data[] = [
                'tahun' => 2024 + $i % 2,
                'bulan' => rand(1, 12),
                'jenis_kelamin' => $jenisKelaminKeys[array_rand($jenisKelaminKeys)],
                'negara_asal' => $negaraAsalContoh[array_rand($negaraAsalContoh)],
                'jabatan' => $jabatanKeys[array_rand($jabatanKeys)],
                // Mengisi lapangan_usaha_kbli dengan string contoh
                'lapangan_usaha_kbli' => $lapanganUsahaContoh[array_rand($lapanganUsahaContoh)], 
                'provinsi_penempatan' => $provinsiContoh[array_rand($provinsiContoh)], 
                'status_pengajuan' => $statusKeys[array_rand($statusKeys)],
                'jumlah' => rand(1, 5),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($data)) {
            PersetujuanRptka::insert($data);
        }
    }
}