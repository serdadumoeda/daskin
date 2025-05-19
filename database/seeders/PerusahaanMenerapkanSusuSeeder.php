<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PerusahaanMenerapkanSusu;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker; // Tambahkan ini untuk menggunakan Faker

class PerusahaanMenerapkanSusuSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // Inisialisasi Faker untuk data Indonesia
        $now = Carbon::now();
        $newData = []; // Ganti nama variabel agar tidak konflik dengan $data lama

        // Daftar contoh provinsi di Indonesia
        $daftarProvinsi = [
            'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Jambi', 'Sumatera Selatan', 
            'Bengkulu', 'Lampung', 'Kepulauan Bangka Belitung', 'Kepulauan Riau', 
            'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur', 'Banten', 
            'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur', 
            'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara', 
            'Sulawesi Utara', 'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Gorontalo', 'Sulawesi Barat', 
            'Maluku', 'Maluku Utara', 'Papua Barat', 'Papua'
        ];

        // Daftar contoh kode KBLI (Klasifikasi Baku Lapangan Usaha Indonesia)
        // Anda bisa menambahkan lebih banyak atau menyesuaikan ini
        // Formatnya bisa berupa kode kategori (misal 'C') atau lebih spesifik ('C10')
        $daftarKbli = [
            'A01', // Pertanian Tanaman, Peternakan, Perburuan Dan Kegiatan YBDI
            'B05', // Pertambangan Batu Bara Dan Lignit
            'C10', // Industri Makanan
            'C13', // Industri Tekstil
            'C22', // Industri Karet, Barang Dari Karet Dan Plastik
            'C24', // Industri Logam Dasar
            'F41', // Konstruksi Gedung
            'G45', // Perdagangan Besar Dan Eceran, Reparasi Dan Perawatan Mobil Dan Sepeda Motor
            'G47', // Perdagangan Eceran, Bukan Mobil Dan Motor
            'H49', // Angkutan Darat Dan Angkutan Melalui Saluran Pipa
            'I56', // Penyediaan Akomodasi Dan Penyediaan Makan Minum
            'J61', // Telekomunikasi
            'J62', // Kegiatan Pemrograman, Konsultasi Komputer Dan Kegiatan YBDI
            'K64', // Aktivitas Jasa Keuangan, Selain Asuransi Dan Dana Pensiun
            'L68', // Real Estat
            'M71', // Aktivitas Arsitektur Dan Keinsinyuran; Analisis Dan Uji Teknis
            'N82', // Aktivitas Administrasi Kantor, Aktivitas Penunjang Kantor Dan Aktivitas Penunjang Usaha Lainnya
            'Q86', // Aktivitas Kesehatan Manusia
            'R90', // Aktivitas Hiburan, Kesenian Dan Rekreasi
            'S94'  // Kegiatan Keanggotaan Organisasi
        ];
        
        $tahunSekarang = (int) $now->year;

        for ($i = 0; $i < 20; $i++) {
            $tahun = rand($tahunSekarang - 2, $tahunSekarang); // Data untuk 3 tahun terakhir
            $bulan = rand(1, 12);

            $newData[] = [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'provinsi' => $faker->randomElement($daftarProvinsi),
                'kbli' => $faker->randomElement($daftarKbli),
                'jumlah_perusahaan_susu' => rand(10, 350), // Jumlah perusahaan acak
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Hapus data statis lama jika ada (kode $data = [...] sebelumnya)
        // Loop $newData untuk insert menggunakan create()
        if (!empty($newData)) {
            foreach ($newData as $item) {
                PerusahaanMenerapkanSusu::create($item);
            }
            // Alternatif: PerusahaanMenerapkanSusu::insert($newData); untuk bulk insert
            // namun tidak memicu event/mutator model.
        }
    }
}