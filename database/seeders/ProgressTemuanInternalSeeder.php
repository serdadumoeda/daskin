<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProgressTemuanInternal;
use App\Models\UnitKerjaEselonI;
use App\Models\SatuanKerja;
use Illuminate\Support\Carbon;

class ProgressTemuanInternalSeeder extends Seeder
{
    public function run(): void
    {
        $unitKerja1 = UnitKerjaEselonI::where('kode_uke1', 'UKE1-001')->first();
        $satuanKerja1 = null;
        if ($unitKerja1) {
            $satuanKerja1 = SatuanKerja::where('kode_unit_kerja_eselon_i', $unitKerja1->kode_uke1)
                                       ->where('kode_sk', 'SK-004') // Biro Hukum
                                       ->first();
        }
        
        $now = Carbon::now();
        $progressData = [];

        if ($unitKerja1 && $satuanKerja1) {
            $progressData[] = [
                'tahun' => 2023,
                'bulan' => 5, // Mei
                'kode_unit_kerja_eselon_i' => $unitKerja1->kode_uke1,
                'kode_satuan_kerja' => $satuanKerja1->kode_sk,
                'temuan_administratif_kasus' => 7,
                'temuan_kerugian_negara_rp' => 15000000.00,
                'tindak_lanjut_administratif_kasus' => 7,
                'tindak_lanjut_kerugian_negara_rp' => 15000000.00,
                'persentase_tindak_lanjut_administratif' => (7 > 0) ? (7/7)*100 : 0,
                'persentase_tindak_lanjut_kerugian_negara' => (15000000.00 > 0) ? (15000000.00/15000000.00)*100 : 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        
        // Tambahkan data contoh lain jika diperlukan
        
        if (!empty($progressData)) {
            ProgressTemuanInternal::insert($progressData);
        }
    }
}