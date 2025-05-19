<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PenerapanSmk3;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker; // Tambahkan Faker

class PenerapanSmk3Seeder extends Seeder
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

        // Daftar contoh kode KBLI
        $daftarKbli = [
            'A01', 'B05', 'B06', 'C10', 'C13', 'C19', 'C20', 'C22', 'C24', 'D35', 'F41', 'F42', 'G45', 'G47', 
            'H49', 'H52', 'I56', 'J61', 'J62', 'K64', 'L68', 'M71', 'N82', 'Q86', 'R90', 'S94'
        ];

        // Daftar contoh untuk field terkait SMK3
        $kategoriPenilaianList = ['awal', 'transisi', 'lanjutan'];
        $tingkatPencapaianList = ['kurang', 'cukup', 'baik', 'memuaskan', 'sangat memuaskan'];
        $jenisPenghargaanList = [
            'Sertifikat Perak', 
            'Sertifikat Emas', 
            'Bendera Perak', 
            'Bendera Emas', 
            'Sertifikat Perak dan Bendera Perak', 
            'Sertifikat Emas dan Bendera Emas', 
            'Tidak Ada Penghargaan', 
            'Piagam Penghargaan K3',
            'Penghargaan Zero Accident'
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
                'kategori_penilaian' => $faker->randomElement($kategoriPenilaianList),
                'tingkat_pencapaian' => $faker->randomElement($tingkatPencapaianList),
                'jenis_penghargaan' => $faker->randomElement($jenisPenghargaanList),
                'jumlah_perusahaan' => rand(1, 30), // Jumlah perusahaan yang mendapatkan penghargaan
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Hapus data contoh statis yang ada sebelumnya
        // Loop $newData untuk insert menggunakan create()
        if (!empty($newData)) {
            foreach ($newData as $item) {
                PenerapanSmk3::create($item);
            }
            // Alternatif: PenerapanSmk3::insert($newData); untuk bulk insert
        }
    }
}