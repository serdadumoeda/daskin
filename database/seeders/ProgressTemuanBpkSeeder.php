<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProgressTemuanBpk;
use App\Models\UnitKerjaEselonI; // Untuk mengambil kode_uke1 yang valid
use App\Models\SatuanKerja;      // Untuk mengambil kode_sk yang valid
use Illuminate\Support\Carbon;

class ProgressTemuanBpkSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil beberapa Unit Kerja Eselon I dan Satuan Kerja yang sudah ada untuk data contoh
        $unitKerja1 = UnitKerjaEselonI::where('kode_uke1', 'UKE1-001')->first(); // Sekretariat Jenderal
        $satuanKerja1 = null;
        if ($unitKerja1) {
            $satuanKerja1 = SatuanKerja::where('kode_unit_kerja_eselon_i', $unitKerja1->kode_uke1)
                                       ->where('kode_sk', 'SK-001') // Biro Perencanaan dan Anggaran
                                       ->first();
        }

        $unitKerja2 = UnitKerjaEselonI::where('kode_uke1', 'UKE1-002')->first(); // Ditjen Binalavotas
        $satuanKerja2 = null;
        if ($unitKerja2) {
            $satuanKerja2 = SatuanKerja::where('kode_unit_kerja_eselon_i', $unitKerja2->kode_uke1)
                                       ->where('kode_sk', 'SK-100') // Sekretariat Ditjen Binalavotas
                                       ->first();
        }
        
        $now = Carbon::now();
        $progressData = [];

        if ($unitKerja1 && $satuanKerja1) {
            $progressData[] = [
                'tahun' => 2023,
                'bulan' => 1, // Januari
                'kode_unit_kerja_eselon_i' => $unitKerja1->kode_uke1,
                'kode_satuan_kerja' => $satuanKerja1->kode_sk,
                'temuan_administratif_kasus' => 10,
                'temuan_kerugian_negara_rp' => 50000000.00,
                'tindak_lanjut_administratif_kasus' => 8,
                'tindak_lanjut_kerugian_negara_rp' => 40000000.00,
                'persentase_tindak_lanjut_administratif' => ($satuanKerja1->temuan_administratif_kasus > 0) ? (8/10)*100 : 0,
                'persentase_tindak_lanjut_kerugian_negara' => (50000000.00 > 0) ? (40000000.00/50000000.00)*100 : 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $progressData[] = [
                'tahun' => 2023,
                'bulan' => 2, // Februari
                'kode_unit_kerja_eselon_i' => $unitKerja1->kode_uke1,
                'kode_satuan_kerja' => $satuanKerja1->kode_sk,
                'temuan_administratif_kasus' => 5,
                'temuan_kerugian_negara_rp' => 0.00,
                'tindak_lanjut_administratif_kasus' => 5,
                'tindak_lanjut_kerugian_negara_rp' => 0.00,
                'persentase_tindak_lanjut_administratif' => (5 > 0) ? (5/5)*100 : 0,
                'persentase_tindak_lanjut_kerugian_negara' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($unitKerja2 && $satuanKerja2) {
            $progressData[] = [
                'tahun' => 2024,
                'bulan' => 3, // Maret
                'kode_unit_kerja_eselon_i' => $unitKerja2->kode_uke1,
                'kode_satuan_kerja' => $satuanKerja2->kode_sk,
                'temuan_administratif_kasus' => 20,
                'temuan_kerugian_negara_rp' => 120000000.00,
                'tindak_lanjut_administratif_kasus' => 15,
                'tindak_lanjut_kerugian_negara_rp' => 100000000.00,
                'persentase_tindak_lanjut_administratif' => (20 > 0) ? (15/20)*100 : 0,
                'persentase_tindak_lanjut_kerugian_negara' => (120000000.00 > 0) ? (100000000.00/120000000.00)*100 : 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        
        if (!empty($progressData)) {
            ProgressTemuanBpk::insert($progressData);
        }
    }
}