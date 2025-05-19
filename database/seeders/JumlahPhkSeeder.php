<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JumlahPhk;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker; // Tambahkan Faker

class JumlahPhkSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // Inisialisasi Faker
        $now = Carbon::now();
        $newData = []; // Array untuk data baru

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
        // Menggunakan format seperti di seeder asli (misal 'C28') dan beberapa tambahan
        $daftarKbli = [
            'A01', // Pertanian Tanaman Semusim
            'B06', // Pertambangan Minyak Bumi Dan Gas Alam Dan Panas Bumi
            'C10', // Industri Makanan
            'C13', // Industri Tekstil
            'C20', // Industri Bahan Kimia Dan Barang Dari Bahan Kimia
            'C28', // Industri Mesin Dan Perlengkapan Ytdl
            'C29', // Industri Kendaraan Bermotor, Trailer Dan Semi-Trailer
            'F41', // Konstruksi Gedung
            'G46', // Perdagangan Besar, Bukan Mobil Dan Motor
            'G47', // Perdagangan Eceran, Bukan Mobil Dan Motor
            'H49', // Angkutan Darat Dan Angkutan Melalui Saluran Pipa
            'I55', // Penyediaan Akomodasi Jangka Pendek (Hotel)
            'J61', // Aktivitas Telekomunikasi
            'K64', // Aktivitas Jasa Keuangan, Selain Asuransi Dan Dana Pensiun
            'M72', // Aktivitas Penelitian Dan Pengembangan Ilmu Pengetahuan
            'N82', // Aktivitas Administrasi Kantor, Aktivitas Penunjang Kantor Dan Aktivitas Penunjang Usaha Lainnya
        ];
        
        $tahunSekarang = (int) $now->year;

        for ($i = 0; $i < 20; $i++) {
            $tahun = rand($tahunSekarang - 2, $tahunSekarang); // Data untuk 3 tahun terakhir
            $bulan = rand(1, 12);
            $jumlahPerusahaanPhk = rand(1, 7); // Jumlah perusahaan yang melakukan PHK
            // Jumlah TK yang di-PHK, minimal sama dengan jumlah perusahaan, maksimal 25x jumlah perusahaan
            $jumlahTkPhk = rand($jumlahPerusahaanPhk, $jumlahPerusahaanPhk * 25); 

            $newData[] = [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'provinsi' => $faker->randomElement($daftarProvinsi),
                'kbli' => $faker->randomElement($daftarKbli),
                'jumlah_perusahaan_phk' => $jumlahPerusahaanPhk,
                'jumlah_tk_phk' => $jumlahTkPhk,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Hapus data contoh statis yang ada sebelumnya ($data array)
        // Loop $newData untuk insert menggunakan create()
        if (!empty($newData)) {
            foreach ($newData as $item) {
                JumlahPhk::create($item);
            }
            // Alternatif: JumlahPhk::insert($newData); untuk bulk insert
        }
    }
}