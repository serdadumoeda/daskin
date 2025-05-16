<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PenyelesaianBmn;
use App\Models\SatuanKerja; // Tambahkan ini
use Illuminate\Support\Carbon;

class PenyelesaianBmnSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $data = [];

        // Ambil beberapa kode satuan kerja yang valid
        $satuanKerjaKodes = SatuanKerja::inRandomOrder()->limit(5)->pluck('kode_sk')->toArray();
        if (empty($satuanKerjaKodes)) {
            // Jika tidak ada Satuan Kerja, buat satu dummy atau hentikan seeder
            // Untuk contoh ini, kita bisa membuat SatuanKerja dummy jika tabelnya kosong
            // atau Anda bisa memastikan SatuanKerjaSeeder sudah dijalankan sebelumnya.
            // SatuanKerja::factory()->create(['kode_sk' => 'SK-DUMMY', 'nama_satuan_kerja' => 'Satuan Kerja Dummy']);
            // $satuanKerjaKodes = ['SK-DUMMY'];
            echo "Tidak ada data Satuan Kerja untuk PenyelesaianBmnSeeder. Harap jalankan SatuanKerjaSeeder terlebih dahulu.\n";
            return;
        }


        $jenisBmnKeys = array_keys(PenyelesaianBmn::JENIS_BMN_OPTIONS);
        $statusPenggunaanKeys = array_keys(PenyelesaianBmn::STATUS_PENGGUNAAN_OPTIONS);

        for ($i = 0; $i < 5; $i++) {
            $data[] = [
                'tahun' => 2023 + $i % 2,
                'bulan' => rand(1, 12),
                'kode_satuan_kerja' => $satuanKerjaKodes[array_rand($satuanKerjaKodes)], // Gunakan kode_sk yang valid
                'jenis_bmn' => $jenisBmnKeys[array_rand($jenisBmnKeys)],
                'henti_guna' => (bool)rand(0, 1),
                'status_penggunaan' => $statusPenggunaanKeys[array_rand($statusPenggunaanKeys)],
                'penetapan_status_penggunaan' => 'SK-' . rand(100, 999) . '/PSP/' . (2023 + $i % 2),
                'kuantitas' => rand(1, 20),
                'nilai_aset' => rand(1000000, 500000000) / 100,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($data)) {
            PenyelesaianBmn::insert($data);
        }
    }
}