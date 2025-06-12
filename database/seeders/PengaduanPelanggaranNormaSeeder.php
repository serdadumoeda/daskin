<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PengaduanPelanggaranNorma;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker; // Tambahkan Faker

class PengaduanPelanggaranNormaSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // Inisialisasi Faker
        $now = Carbon::now();
        $newData = []; // Array untuk data baru

        // Daftar contoh provinsi di Indonesia
        // $daftarProvinsi = [
        //     'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Jambi', 'Sumatera Selatan',
        //     'Bengkulu', 'Lampung', 'Kepulauan Bangka Belitung', 'Kepulauan Riau',
        //     'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur', 'Banten',
        //     'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur',
        //     'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara',
        //     'Sulawesi Utara', 'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Gorontalo', 'Sulawesi Barat',
        //     'Maluku', 'Maluku Utara', 'Papua Barat', 'Papua'
        // ];

        // Daftar contoh untuk field terkait pengaduan
        $jenisTindakLanjutList = ['Pemeriksaan Langsung ke Perusahaan', 'Surat Panggilan Klarifikasi', 'Proses Mediasi', 'Nota Pemeriksaan (NP) Tahap 1', 'Nota Pemeriksaan (NP) Tahap 2', 'Rekomendasi ke Pengadilan Hubungan Industrial', 'Pembinaan Kepatuhan Norma', 'Koordinasi dengan Instansi Terkait'];

        $tahunSekarang = (int) $now->year;

        for ($i = 0; $i < 20; $i++) {
            $tahunTindakLanjut = rand($tahunSekarang - 2, $tahunSekarang); // 3 tahun terakhir
            $bulanTindakLanjut = rand(1, 12);

            // 75% kemungkinan kasus sudah ditindaklanjuti
            // if ($faker->boolean(75)) {
            //     // Tindak lanjut bisa terjadi beberapa hari hingga beberapa bulan setelah pengaduan
            //     $tanggalTindakLanjut = $tanggalPengaduan->copy()->addDays($faker->numberBetween(7, 180));
            //     // Pastikan tahun tindak lanjut tidak melebihi tahun sekarang jika tanggal pengaduan juga di tahun sekarang
            //     if ($tanggalTindakLanjut->year > $tahunSekarang) {
            //         $tanggalTindakLanjut = Carbon::create($tahunSekarang, rand($bulanPengaduan, 12), $faker->numberBetween(1,28));
            //          // Pastikan bulan tindak lanjut tidak kurang dari bulan pengaduan jika tahunnya sama
            //         if($tanggalTindakLanjut->year == $tahunPengaduan && $tanggalTindakLanjut->month < $bulanPengaduan){
            //             $tanggalTindakLanjut->month = $bulanPengaduan;
            //         }
            //     }
            //     $tahunTindakLanjut = $tanggalTindakLanjut->year;
            //     $bulanTindakLanjut = $tanggalTindakLanjut->month;
            // }

            // Jika tahun tindak lanjut lebih kecil dari tahun pengaduan (tidak logis), set null
            // if ($tahunTindakLanjut !== null && $tahunTindakLanjut < $tahunPengaduan) {
            //     $tahunTindakLanjut = null;
            //     $bulanTindakLanjut = null;
            // }
            // // Jika tahun sama, bulan tindak lanjut tidak boleh lebih kecil dari bulan pengaduan
            // if ($tahunTindakLanjut === $tahunPengaduan && $bulanTindakLanjut !== null && $bulanTindakLanjut < $bulanPengaduan) {
            //     $bulanTindakLanjut = $bulanPengaduan; // Samakan dengan bulan pengaduan atau set sedikit lebih besar
            // }


            $newData[] = [
                'tahun_tindak_lanjut' => $tahunTindakLanjut,
                'bulan_tindak_lanjut' => $bulanTindakLanjut,
                'jenis_tindak_lanjut' => $faker->randomElement($jenisTindakLanjutList),
                'jumlah_pengaduan_tindak_lanjut' => rand(1, 100),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Hapus data contoh statis yang ada sebelumnya
        // Loop $newData untuk insert menggunakan create()
        if (!empty($newData)) {
            foreach ($newData as $item) {
                PengaduanPelanggaranNorma::create($item);
            }
            // Alternatif: PengaduanPelanggaranNorma::insert($newData); untuk bulk insert
        }
    }
}
