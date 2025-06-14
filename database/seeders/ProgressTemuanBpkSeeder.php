<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProgressTemuanBpk; // Model yang akan digunakan
// Tidak perlu lagi query ke UnitKerjaEselonI dan SatuanKerja secara langsung jika menggunakan daftar kode manual
use Illuminate\Support\Carbon;

class ProgressTemuanBpkSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lama jika ada (opsional, tergantung kebutuhan)
        // ProgressTemuanBpk::truncate(); // Hati-hati jika ada foreign key constraint

        $now = Carbon::now();
        $progressData = [];

        // Daftar contoh Satuan Kerja yang valid (kode_uke1 & kode_sk)
        // Ini adalah daftar yang sama yang digunakan untuk ProgressTemuanInternalSeeder
        // untuk kemudahan dan konsistensi.
        $availableSatuanKerja = [
            ['kode_uke1' => 'UKE1-001', 'kode_sk' => 'SK-001'], // Biro Perencanaan dan Manajemen Kinerja
            ['kode_uke1' => 'UKE1-001', 'kode_sk' => 'SK-004'], // Biro Hukum
            ['kode_uke1' => 'UKE1-002', 'kode_sk' => 'SK-100'], // Sekretariat Ditjen Binalavotas
            ['kode_uke1' => 'UKE1-002', 'kode_sk' => 'SK-106'], // BBPVP Medan
            ['kode_uke1' => 'UKE1-003', 'kode_sk' => 'SK-201'], // Direktorat Bina Penempatan Tenaga Kerja Dalam Negeri
            ['kode_uke1' => 'UKE1-004', 'kode_sk' => 'SK-302'], // Direktorat Bina Persyaratan Kerja...
            ['kode_uke1' => 'UKE1-005', 'kode_sk' => 'SK-403'], // Direktorat Bina Pengujian K3
            ['kode_uke1' => 'UKE1-006', 'kode_sk' => 'SK-501'], // Pusat Perencanaan Ketenagakerjaan
            ['kode_uke1' => 'UKE1-007', 'kode_sk' => 'SK-602'], // Inspektorat II
        ];
        
        // Anda juga bisa uncomment baris di bawah ini jika ingin mengambil dari DB langsung
        // dan pastikan model SatuanKerja sudah di-import: use App\Models\SatuanKerja;
        // $allSatuanKerja = SatuanKerja::select('kode_unit_kerja_eselon_i', 'kode_sk')->get()->toArray();
        // if (empty($allSatuanKerja)) {
        //     $allSatuanKerja = $availableSatuanKerja; // Fallback
        // }

        $tahunSekarang = (int) $now->year;

        for ($i = 0; $i < 20; $i++) {
            // Pilih Satuan Kerja secara acak
            $selectedSatuanKerja = $availableSatuanKerja[array_rand($availableSatuanKerja)];
            // Jika menggunakan $allSatuanKerja dari DB:
            // $selectedSatuanKerja = $allSatuanKerja[array_rand($allSatuanKerja)];

            $tahun = rand($tahunSekarang - 2, $tahunSekarang); // Data untuk 3 tahun terakhir
            $bulan = rand(1, 12);

            $temuan_administratif_kasus = rand(0, 25); // Jumlah kasus sedikit lebih banyak untuk BPK
            $temuan_kerugian_negara_rp = round(rand(0, 10000000000) / 100, 2); // Maks 100 juta untuk BPK

            $tindak_lanjut_administratif_kasus = rand(0, $temuan_administratif_kasus);
            $tindak_lanjut_kerugian_negara_rp = ($temuan_kerugian_negara_rp > 0) ? round(rand(0, (int)($temuan_kerugian_negara_rp * 100)) / 100, 2) : 0.00;

            // Perhitungan persentase yang benar
            $persentase_tindak_lanjut_administratif = ($temuan_administratif_kasus > 0) ? ($tindak_lanjut_administratif_kasus / $temuan_administratif_kasus) * 100 : 0;
            $persentase_tindak_lanjut_kerugian_negara = ($temuan_kerugian_negara_rp > 0) ? ($tindak_lanjut_kerugian_negara_rp / $temuan_kerugian_negara_rp) * 100 : 0;

            $progressData[] = [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'kode_unit_kerja_eselon_i' => $selectedSatuanKerja['kode_uke1'],
                'kode_satuan_kerja' => $selectedSatuanKerja['kode_sk'],
                'temuan_administratif_kasus' => $temuan_administratif_kasus,
                'temuan_kerugian_negara_rp' => $temuan_kerugian_negara_rp,
                'tindak_lanjut_administratif_kasus' => $tindak_lanjut_administratif_kasus,
                'tindak_lanjut_kerugian_negara_rp' => $tindak_lanjut_kerugian_negara_rp,
                'persentase_tindak_lanjut_administratif' => round($persentase_tindak_lanjut_administratif, 2),
                'persentase_tindak_lanjut_kerugian_negara' => round($persentase_tindak_lanjut_kerugian_negara, 2),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        
        // Hapus data lama yang mungkin ada di $progressData dari contoh seeder sebelumnya
        // (Jika Anda menjalankan ini setelah seeder original, baris $progressData = [] di atas sudah menangani ini)

        if (!empty($progressData)) {
            // Gunakan create() dalam loop untuk memastikan mutator dan event model terpanggil
            foreach ($progressData as $data) {
                ProgressTemuanBpk::create($data);
            }
            // Atau gunakan insert() untuk performa lebih baik pada data besar,
            // namun tidak memicu event/mutator model:
            // ProgressTemuanBpk::insert($progressData);
        }
    }
}