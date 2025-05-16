<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JumlahPenangananKasus;
// SatuanKerja tidak lagi diperlukan untuk mengisi field substansi
// use App\Models\SatuanKerja; 
use Illuminate\Support\Carbon;

class JumlahPenangananKasusSeeder extends Seeder
{
    public function run(): void
    {
        // Contoh data SatuanKerja tidak lagi diambil dari model SatuanKerja untuk field ini
        // $satuanKerjaBiroHukum = SatuanKerja::where('kode_sk', 'SK-004')->first(); 
        
        $now = Carbon::now();
        $kasusData = [];

        // Jika Anda memiliki daftar substansi yang umum, Anda bisa definisikan di sini
        $contohSubstansi1 = "Hubungan Industrial dan Jaminan Sosial"; // Contoh dari PDF Tabel 2.2
        $contohSubstansi2 = "Pengawasan Ketenagakerjaan dan K3";  // Contoh dari PDF Tabel 2.2

        // if ($satuanKerjaBiroHukum) { // Kondisi ini tidak relevan lagi untuk substansi
            $kasusData[] = [
                'tahun' => 2023,
                'bulan' => 7,
                // 'kode_satuan_kerja' => $satuanKerjaBiroHukum->kode_sk, // Diganti
                'substansi' => $contohSubstansi1, 
                'jenis_perkara' => 'Putusan MA', // Sesuai PDF Tabel 2.3
                'jumlah_perkara' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $kasusData[] = [
                'tahun' => 2023,
                'bulan' => 8, // Bulan berbeda untuk variasi
                // 'kode_satuan_kerja' => $satuanKerjaBiroHukum->kode_sk, // Diganti
                'substansi' => $contohSubstansi2,
                'jenis_perkara' => 'Putusan MK', // Sesuai PDF Tabel 2.3
                'jumlah_perkara' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        // }

        if (!empty($kasusData)) {
            JumlahPenangananKasus::insert($kasusData);
        }
    }
}