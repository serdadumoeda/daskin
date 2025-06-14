<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PenyelesaianBmn;
use App\Models\SatuanKerja; // Diperlukan jika ingin query langsung, tapi kita akan pakai daftar statis
use Illuminate\Support\Carbon;
use Faker\Factory as Faker; // Tambahkan Faker

class PenyelesaianBmnSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // Inisialisasi Faker
        $now = Carbon::now();
        $newData = []; // Mengganti nama variabel $data menjadi $newData

        // Daftar contoh Satuan Kerja yang valid (ambil kode_sk nya)
        // Ini adalah daftar yang sama yang digunakan untuk seeder lain
        $availableSatuanKerjaForForeignKey = [
            ['kode_uke1' => 'UKE1-001', 'kode_sk' => 'SK-001'],
            ['kode_uke1' => 'UKE1-001', 'kode_sk' => 'SK-003'],
            ['kode_uke1' => 'UKE1-001', 'kode_sk' => 'SK-004'],
            ['kode_uke1' => 'UKE1-002', 'kode_sk' => 'SK-100'],
            ['kode_uke1' => 'UKE1-002', 'kode_sk' => 'SK-106'],
            ['kode_uke1' => 'UKE1-003', 'kode_sk' => 'SK-201'],
            ['kode_uke1' => 'UKE1-004', 'kode_sk' => 'SK-302'],
            ['kode_uke1' => 'UKE1-005', 'kode_sk' => 'SK-403'],
            ['kode_uke1' => 'UKE1-006', 'kode_sk' => 'SK-501'],
            ['kode_uke1' => 'UKE1-007', 'kode_sk' => 'SK-602'],
        ];
        
        // Ekstrak hanya kode_sk
        $satuanKerjaKodes = array_column($availableSatuanKerjaForForeignKey, 'kode_sk');

        if (empty($satuanKerjaKodes)) {
            // Fallback jika daftar $availableSatuanKerjaForForeignKey kosong (seharusnya tidak terjadi jika didefinisikan di atas)
            // Atau jika Anda ingin tetap query langsung dari DB:
            // $satuanKerjaKodes = SatuanKerja::pluck('kode_sk')->toArray();
            // if (empty($satuanKerjaKodes)) {
            //     echo "Peringatan: Tidak ada data Satuan Kerja di database untuk PenyelesaianBmnSeeder. Harap jalankan SatuanKerjaSeeder.\n";
            //     // Anda bisa membuat SatuanKerja dummy di sini jika perlu, atau return.
            //     // Contoh: SatuanKerja::factory()->create(['kode_sk' => 'SK-DUMMY-SEED', 'nama_satuan_kerja' => 'Satuan Kerja Dummy Seeder', 'kode_unit_kerja_eselon_i' => 'UKE1-DUMMY']);
            //     // $satuanKerjaKodes = ['SK-DUMMY-SEED'];
            //     return; // Hentikan jika tidak ada kode satker
            // }
             echo "Peringatan: Daftar satuanKerjaKodes kosong dalam seeder. Pastikan $availableSatuanKerjaForForeignKey terisi.\n";
             return;
        }


        $jenisBmnKeys = array_keys(PenyelesaianBmn::JENIS_BMN_OPTIONS);
        $statusPenggunaanKeys = array_keys(PenyelesaianBmn::STATUS_PENGGUNAAN_OPTIONS);
        
        $tahunSekarang = (int) $now->year;

        for ($i = 0; $i < 20; $i++) { // Ubah loop menjadi 20
            $tahunData = rand($tahunSekarang - 2, $tahunSekarang); // Tahun acak dari 2023-2025 (berdasarkan tahun saat ini)
            $nilaiAsetNumerik = $faker->numberBetween(1000000, 50000000000); // Nilai antara 1 juta hingga 50 milyar (sebelum dibagi 100)
            
            $newData[] = [
                'tahun' => $tahunData,
                'bulan' => $faker->numberBetween(1, 12),
                'kode_satuan_kerja' => $faker->randomElement($satuanKerjaKodes),
                'jenis_bmn' => $faker->randomElement($jenisBmnKeys),
                'henti_guna' => $faker->boolean(30), // 30% kemungkinan true
                'status_penggunaan' => $faker->randomElement($statusPenggunaanKeys),
                'penetapan_status_penggunaan' => 'SK-' . $faker->unique()->randomNumber(4) . '/PSP/' . $tahunData,
                'kuantitas' => $faker->numberBetween(1, 100),
                'nilai_aset' => $nilaiAsetNumerik / 100, // Simpan sebagai desimal dengan 2 angka di belakang koma
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Hapus data lama yang di-generate oleh seeder asli jika ada (kode $data = [] sebelumnya)

        if (!empty($newData)) {
            // Gunakan create() dalam loop untuk memicu event/mutator model
            foreach ($newData as $item) {
                PenyelesaianBmn::create($item);
            }
            // Alternatif: PenyelesaianBmn::insert($newData); untuk performa lebih jika tidak ada event/mutator
        }
    }
}