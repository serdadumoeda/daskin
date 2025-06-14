<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JumlahSertifikasiKompetensi;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker; // Tambahkan Faker

class JumlahSertifikasiKompetensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Opsional: Hapus data lama. Jika diaktifkan, semua data lama akan hilang.
        // JumlahSertifikasiKompetensi::truncate(); 

        $faker = Faker::create('id_ID'); // Inisialisasi Faker
        $now = Carbon::now();
        $newData = []; // Array untuk menampung data baru

        // Opsi dari model
        $jenisLspOptions = array_keys(JumlahSertifikasiKompetensi::getJenisLspOptions()); // [1, 2, 3]
        $jenisKelaminOptions = array_keys(JumlahSertifikasiKompetensi::getJenisKelaminOptions()); // [1, 2]

        // Daftar contoh untuk data acak
        $daftarProvinsi = [
            'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Jambi', 'Sumatera Selatan', 
            'Bengkulu', 'Lampung', 'Kepulauan Bangka Belitung', 'Kepulauan Riau', 
            'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur', 'Banten', 
            'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur', 
            'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara', 
            'Sulawesi Utara', 'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Gorontalo', 'Sulawesi Barat', 
            'Maluku', 'Maluku Utara', 'Papua Barat', 'Papua'
        ];
        
        $lapanganUsahaList = [
            'Manufaktur', 'Teknologi Informasi dan Komunikasi', 'Konstruksi Bangunan', 'Pariwisata dan Perhotelan', 
            'Pertanian dan Perkebunan', 'Jasa Keuangan dan Asuransi', 'Pertambangan dan Penggalian', 
            'Logistik dan Transportasi', 'Jasa Kesehatan', 'Pendidikan dan Pelatihan', 
            'Perdagangan Besar dan Eceran', 'Industri Otomotif', 'Energi Terbarukan', 
            'Industri Kreatif dan Media', 'Jasa Profesional Lainnya', 'Pengolahan Makanan dan Minuman'
        ];
        
        $tahunSekarang = (int) $now->year;

        for ($i = 0; $i < 20; $i++) {
            $tahun = rand($tahunSekarang - 2, $tahunSekarang); // Data untuk 3 tahun terakhir
            $bulan = rand(1, 12);

            $newData[] = [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'jenis_lsp' => $faker->randomElement($jenisLspOptions),
                'jenis_kelamin' => $faker->randomElement($jenisKelaminOptions),
                'provinsi' => $faker->randomElement($daftarProvinsi),
                'lapangan_usaha_kbli' => $faker->randomElement($lapanganUsahaList),
                'jumlah_sertifikasi' => rand(15, 350), // Jumlah sertifikasi per record
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Mengganti penggunaan firstOrCreate dengan create dalam loop untuk data dummy
        if (!empty($newData)) {
            foreach ($newData as $item) {
                JumlahSertifikasiKompetensi::create($item);
            }
        }
    }
}