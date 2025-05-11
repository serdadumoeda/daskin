<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PersentaseKehadiran;
use App\Models\UnitKerjaEselonI;
use App\Models\SatuanKerja;
use Illuminate\Support\Carbon;

class PersentaseKehadiranSeeder extends Seeder
{
    public function run(): void
    {
        $uke1_sekjen = UnitKerjaEselonI::where('kode_uke1', 'UKE1-001')->first();
        $sk_biro_sdm = null;
        if ($uke1_sekjen) {
            $sk_biro_sdm = SatuanKerja::where('kode_unit_kerja_eselon_i', $uke1_sekjen->kode_uke1)
                                      ->where('kode_sk', 'SK-003')->first(); // Biro Organisasi dan SDM Aparatur
        }

        $now = Carbon::now();
        $kehadiranData = [];

        if ($uke1_sekjen && $sk_biro_sdm) {
            $kehadiranData[] = [
                'tahun' => 2024,
                'bulan' => 4,
                'kode_unit_kerja_eselon_i' => $uke1_sekjen->kode_uke1,
                'kode_satuan_kerja' => $sk_biro_sdm->kode_sk,
                'status_asn' => 1, // ASN
                'status_kehadiran' => 1, // WFO
                'jumlah_orang' => 50,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $kehadiranData[] = [
                'tahun' => 2024,
                'bulan' => 4,
                'kode_unit_kerja_eselon_i' => $uke1_sekjen->kode_uke1,
                'kode_satuan_kerja' => $sk_biro_sdm->kode_sk,
                'status_asn' => 1, // ASN
                'status_kehadiran' => 2, // Cuti
                'jumlah_orang' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $kehadiranData[] = [
                'tahun' => 2024,
                'bulan' => 4,
                'kode_unit_kerja_eselon_i' => $uke1_sekjen->kode_uke1,
                'kode_satuan_kerja' => $sk_biro_sdm->kode_sk,
                'status_asn' => 2, // Non ASN
                'status_kehadiran' => 1, // WFO
                'jumlah_orang' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($kehadiranData)) {
            PersentaseKehadiran::insert($kehadiranData);
        }
    }
}