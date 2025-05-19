<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProgressTemuanInternal;
// Tidak perlu lagi query ke UnitKerjaEselonI dan SatuanKerja secara langsung di sini
// karena kita akan menggunakan daftar kode yang sudah pasti ada dari SatuanKerjaSeeder
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB; // Untuk mengambil data SatuanKerja jika diperlukan, atau definisikan manual

class ProgressTemuanInternalSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lama jika ada (opsional, tergantung kebutuhan)
        // ProgressTemuanInternal::truncate(); // Hati-hati jika ada foreign key constraint

        $now = Carbon::now();
        $progressData = [];

        // Daftar contoh Satuan Kerja yang valid (kode_uke1 & kode_sk)
        // Ambil beberapa contoh dari SatuanKerjaSeeder Anda atau definisikan di sini
        // Ini lebih aman daripada query langsung saat seeder berjalan,
        // karena menjamin kode tersebut ada jika SatuanKerjaSeeder sudah dijalankan.
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

        // Jika Anda ingin mengambil langsung dari DB (pastikan SatuanKerjaSeeder sudah jalan):
        // $allSatuanKerja = SatuanKerja::select('kode_unit_kerja_eselon_i', 'kode_sk')->get()->toArray();
        // if (empty($allSatuanKerja)) {
        //     // Fallback jika tabel SatuanKerja kosong
        //     $allSatuanKerja = $availableSatuanKerja; // Gunakan daftar manual
        // }
        // Jika menggunakan $allSatuanKerja dari DB, pastikan App\Models\SatuanKerja di-import.

        $tahunSekarang = (int) $now->year;

        for ($i = 0; $i < 20; $i++) {
            // Pilih Satuan Kerja secara acak dari daftar
            $selectedSatuanKerja = $availableSatuanKerja[array_rand($availableSatuanKerja)];
            // Jika mengambil dari DB: $selectedSatuanKerja = $allSatuanKerja[array_rand($allSatuanKerja)];

            $tahun = rand($tahunSekarang - 2, $tahunSekarang); // Data untuk 3 tahun terakhir termasuk tahun ini
            $bulan = rand(1, 12);

            $temuan_administratif_kasus = rand(0, 15);
            // Pastikan nilai desimal disimpan dengan benar
            $temuan_kerugian_negara_rp = round(rand(0, 2500000000) / 100, 2); // Maks 25 juta, dengan 2 desimal

            $tindak_lanjut_administratif_kasus = rand(0, $temuan_administratif_kasus);
            $tindak_lanjut_kerugian_negara_rp = ($temuan_kerugian_negara_rp > 0) ? round(rand(0, (int)($temuan_kerugian_negara_rp * 100)) / 100, 2) : 0.00;

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
        
        // Insert data dalam batch untuk efisiensi
        if (!empty($progressData)) {
            // ProgressTemuanInternal::insert($progressData); // Gunakan ini jika tidak ada mutator/event yang perlu dipicu per model
            // Atau, jika Anda memiliki observer atau mutator yang perlu dijalankan untuk setiap record:
            foreach ($progressData as $data) {
                ProgressTemuanInternal::create($data);
            }
        }
    }
}