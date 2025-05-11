<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SdmMengikutiPelatihan;
use App\Models\UnitKerjaEselonI;
use App\Models\SatuanKerja;
use Illuminate\Support\Carbon;

class SdmMengikutiPelatihanSeeder extends Seeder
{
    public function run(): void
    {
        $uke1_pusdiklat = UnitKerjaEselonI::where('kode_uke1', 'UKE1-001')->first(); // Misal Sekjen membawahi Pusdiklat
        $sk_pusdiklat = null;
        if($uke1_pusdiklat){
            $sk_pusdiklat = SatuanKerja::where('kode_unit_kerja_eselon_i', $uke1_pusdiklat->kode_uke1)
                                    ->where('kode_sk', 'SK-009')->first(); // Pusat Pelatihan SDM Ketenagakerjaan
        }
        
        $uke1_itjen = UnitKerjaEselonI::where('kode_uke1', 'UKE1-007')->first(); // Inspektorat Jenderal
        $sk_itjen_sekretariat = null;
        if ($uke1_itjen) {
            $sk_itjen_sekretariat = SatuanKerja::where('kode_unit_kerja_eselon_i', $uke1_itjen->kode_uke1)
                                    ->where('kode_sk', 'SK-600')->first(); // Sekretariat Inspektorat Jenderal
        }


        $now = Carbon::now();
        $pelatihanData = [];

        if ($uke1_pusdiklat && $sk_pusdiklat) {
            $pelatihanData[] = [
                'tahun' => 2024,
                'bulan' => 3,
                'kode_unit_kerja_eselon_i' => $uke1_pusdiklat->kode_uke1,
                'kode_satuan_kerja' => $sk_pusdiklat->kode_sk,
                'jenis_pelatihan' => 1, // Diklat Dasar
                'jumlah_peserta' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        if ($uke1_itjen && $sk_itjen_sekretariat) {
             $pelatihanData[] = [
                'tahun' => 2024,
                'bulan' => 4,
                'kode_unit_kerja_eselon_i' => $uke1_itjen->kode_uke1,
                'kode_satuan_kerja' => $sk_itjen_sekretariat->kode_sk,
                'jenis_pelatihan' => 3, // Diklat Fungsional
                'jumlah_peserta' => 15,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }


        if (!empty($pelatihanData)) {
            SdmMengikutiPelatihan::insert($pelatihanData);
        }
    }
}