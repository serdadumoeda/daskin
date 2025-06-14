<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JumlahPenempatanKemnaker;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker; // Tambahkan Faker

class JumlahPenempatanKemnakerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Opsional: Hapus data lama jika ada. Aktifkan jika ingin memulai dengan tabel bersih.
        // JumlahPenempatanKemnaker::truncate(); 
        
        $faker = Faker::create('id_ID'); // Inisialisasi Faker
        $now = Carbon::now();
        $newData = []; // Array untuk data baru

        // Opsi dari model
        $jenisKelaminOptions = array_keys(JumlahPenempatanKemnaker::getJenisKelaminOptions()); // [1, 2]
        $statusDisabilitasOptions = array_keys(JumlahPenempatanKemnaker::getStatusDisabilitasOptions()); // [1, 2]
        // Model getRagamDisabilitasOptions mengembalikan [Value => Text], kita ambil valuenya (Textnya)
        $ragamDisabilitasOptions = array_values(JumlahPenempatanKemnaker::getRagamDisabilitasOptions()); 

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
            'Industri Pengolahan', 'Jasa Keuangan dan Asuransi', 'Perdagangan Besar dan Eceran', 
            'Pertanian, Kehutanan dan Perikanan', 'Konstruksi', 'Transportasi dan Pergudangan',
            'Penyediaan Akomodasi dan Makan Minum', 'Informasi dan Komunikasi', 
            'Aktivitas Profesional, Ilmiah dan Teknis', 'Administrasi Pemerintahan dan Jaminan Sosial Wajib',
            'Pendidikan', 'Aktivitas Kesehatan Manusia dan Aktivitas Sosial', 'Kesenian, Hiburan dan Rekreasi'
        ];
        
        $tahunSekarang = (int) $now->year;

        for ($i = 0; $i < 20; $i++) {
            $tahun = rand($tahunSekarang - 2, $tahunSekarang); // Data untuk 3 tahun terakhir
            $bulan = rand(1, 12);
            $statusDisabilitas = $faker->randomElement($statusDisabilitasOptions);
            $ragamDisabilitas = null;

            if ($statusDisabilitas == 1) { // Jika status_disabilitas adalah 'Ya' (kode 1)
                $ragamDisabilitas = $faker->randomElement($ragamDisabilitasOptions);
            }

            $newData[] = [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'jenis_kelamin' => $faker->randomElement($jenisKelaminOptions),
                'provinsi_domisili' => $faker->randomElement($daftarProvinsi),
                'lapangan_usaha_kbli' => $faker->randomElement($lapanganUsahaList),
                'status_disabilitas' => $statusDisabilitas,
                'ragam_disabilitas' => $ragamDisabilitas,
                'jumlah' => rand(1, 75), // Jumlah orang yang ditempatkan
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Hapus data contoh statis yang ada sebelumnya ($data array dari seeder asli)
        // Loop $newData untuk insert menggunakan create()
        if (!empty($newData)) {
            foreach ($newData as $item) {
                JumlahPenempatanKemnaker::create($item);
            }
            // Alternatif: JumlahPenempatanKemnaker::insert($newData); untuk bulk insert, namun tidak memicu event model
        }
    }
}