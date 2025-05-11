<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PenyelesaianBmn;
use App\Models\SatuanKerja;
use Illuminate\Support\Carbon;

class PenyelesaianBmnSeeder extends Seeder
{
    public function run(): void
    {
        $satuanKerjaBiroUmum = SatuanKerja::where('kode_sk', 'SK-005')->first(); // Biro Umum
        $now = Carbon::now();
        $bmnData = [];

        if ($satuanKerjaBiroUmum) {
            // Contoh Aset Digunakan, Sudah PSP
            $bmnData[] = [
                'tahun' => 2023,
                'bulan' => 8,
                'kode_satuan_kerja' => $satuanKerjaBiroUmum->kode_sk,
                'status_penggunaan_aset' => 1, // Aset Digunakan
                'status_aset_digunakan' => 1, // Sudah PSP
                'nup' => '10012345',
                'kuantitas' => 10,
                'nilai_aset_rp' => 5000000,
                'total_aset_rp' => 50000000, // Bisa juga dihitung
                'created_at' => $now,
                'updated_at' => $now,
            ];
            // Contoh Aset Digunakan, Belum PSP
            $bmnData[] = [
                'tahun' => 2023,
                'bulan' => 8,
                'kode_satuan_kerja' => $satuanKerjaBiroUmum->kode_sk,
                'status_penggunaan_aset' => 1, // Aset Digunakan
                'status_aset_digunakan' => 2, // Belum PSP
                'nup' => '20023456', // Wajib isi NUP
                'kuantitas' => 5,
                'nilai_aset_rp' => 10000000, // Wajib isi Nilai Aset
                'total_aset_rp' => 50000000,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            // Contoh Aset Tidak Digunakan
            $bmnData[] = [
                'tahun' => 2023,
                'bulan' => 9,
                'kode_satuan_kerja' => $satuanKerjaBiroUmum->kode_sk,
                'status_penggunaan_aset' => 2, // Aset Tidak Digunakan
                'status_aset_digunakan' => null,
                'nup' => null,
                'kuantitas' => 2,
                'nilai_aset_rp' => 75000000,
                'total_aset_rp' => 150000000,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($bmnData)) {
            PenyelesaianBmn::insert($bmnData);
        }
    }
}