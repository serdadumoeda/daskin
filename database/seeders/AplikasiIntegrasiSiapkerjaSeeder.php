<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AplikasiIntegrasiSiapkerja;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker; // Tambahkan Faker

class AplikasiIntegrasiSiapkerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Opsional: Hapus data lama. Aktifkan jika ingin memulai dengan tabel bersih.
        // AplikasiIntegrasiSiapkerja::truncate(); 

        $faker = Faker::create('id_ID'); // Inisialisasi Faker
        $now = Carbon::now();
        $newData = []; // Array untuk data baru

        // Mengambil kunci (integer) dari opsi yang ada di model
        $jenisInstansiOptions = array_keys(AplikasiIntegrasiSiapkerja::getJenisInstansiOptions()); // [1, 2, 3, 4]
        $statusIntegrasiOptions = array_keys(AplikasiIntegrasiSiapkerja::getStatusIntegrasiOptions()); // [1, 2]

        // Daftar contoh untuk nama instansi (bisa diperluas)
        $kementerianList = ['Kementerian Koperasi dan UKM', 'Kementerian Perindustrian', 'Kementerian Perdagangan', 'Kementerian Pariwisata dan Ekonomi Kreatif', 'Kementerian Pertanian', 'Kementerian Kelautan dan Perikanan'];
        $lembagaList = ['Badan Pusat Statistik (BPS)', 'Badan Pelindungan Pekerja Migran Indonesia (BP2MI)', 'Lembaga Kebijakan Pengadaan Barang/Jasa Pemerintah (LKPP)', 'Badan Koordinasi Penanaman Modal (BKPM)'];
        $provinsiNamesForDinas = ['Jawa Barat', 'Jawa Tengah', 'Jawa Timur', 'DKI Jakarta', 'Banten', 'Sumatera Utara', 'Sulawesi Selatan', 'Kalimantan Timur', 'Bali', 'DI Yogyakarta'];
        $kabKotaNamesForDinas = ['Kota Bandung', 'Kab. Bogor', 'Kota Surabaya', 'Kota Medan', 'Kota Semarang', 'Kota Makassar', 'Kab. Sleman', 'Kota Tangerang', 'Kota Bekasi', 'Kab. Badung'];


        $tahunSekarang = (int) $now->year;

        for ($i = 0; $i < 20; $i++) {
            $tahun = rand($tahunSekarang - 2, $tahunSekarang); // Data untuk 3 tahun terakhir
            $bulan = rand(1, 12);
            $jenisInstansi = $faker->randomElement($jenisInstansiOptions);
            $namaInstansi = '';
            $namaAplikasi = '';

            switch ($jenisInstansi) {
                case 1: // Kementerian
                    $namaInstansi = $faker->randomElement($kementerianList);
                    $namaAplikasi = 'e-' . $faker->word . ' ' . $faker->randomElement(['Gov', 'Layanan', 'Data']);
                    break;
                case 2: // Lembaga
                    $namaInstansi = $faker->randomElement($lembagaList);
                    $namaAplikasi = 'Portal ' . $faker->word . ' Nasional';
                    break;
                case 3: // Daerah Provinsi
                    $prov = $faker->randomElement($provinsiNamesForDinas);
                    $namaInstansi = 'Dinas Tenaga Kerja Provinsi ' . $prov;
                    $namaAplikasi = $faker->randomElement(['Si Lancar', 'Jabar Karier', 'Jateng Gayeng Kerja', 'InfoKerja ' . $prov]);
                    break;
                case 4: // Daerah Kabupaten/Kota
                    $kabkota = $faker->randomElement($kabKotaNamesForDinas);
                    $namaInstansi = 'Dinas Ketenagakerjaan ' . $kabkota;
                    $namaAplikasi = $faker->randomElement(['Lowker ', 'Simnaker ', 'Pasker ']) . str_replace(['Kota ', 'Kab. '], '', $kabkota);
                    break;
            }
            // Tambah variasi untuk nama aplikasi
            $namaAplikasi .= ' ' . $faker->randomElement(['Online', 'Digital', 'Terpadu', 'Mobile', '']);


            $newData[] = [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'jenis_instansi' => $jenisInstansi,
                'nama_instansi' => $namaInstansi,
                'nama_aplikasi_website' => trim($namaAplikasi),
                'status_integrasi' => $faker->randomElement($statusIntegrasiOptions),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Mengganti penggunaan firstOrCreate dengan create dalam loop untuk data dummy
        if (!empty($newData)) {
            foreach ($newData as $item) {
                AplikasiIntegrasiSiapkerja::create($item);
            }
        }
    }
}