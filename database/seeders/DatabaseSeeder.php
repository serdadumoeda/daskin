<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Master Data Awal
            UnitKerjaEselonISeeder::class,
            SatuanKerjaSeeder::class,
            UserSeeder::class, // Seeder untuk pengguna dan peran

            // Seeder (Temuan BPK & Internal) - Inspektorat Jenderal
            ProgressTemuanBpkSeeder::class,
            ProgressTemuanInternalSeeder::class,

            // Seeder (MoU, Regulasi, Kasus, BMN, Kehadiran, dll.) - Sekretariat Jenderal
            ProgressMouSeeder::class,
            JumlahRegulasiBaruSeeder::class,
            JumlahPenangananKasusSeeder::class,
            PenyelesaianBmnSeeder::class,
            // IKPASeeder::class, // Dilewati
            PersentaseKehadiranSeeder::class,
            MonevMonitoringMediaSeeder::class,
            LulusanPolteknakerBekerjaSeeder::class,
            SdmMengikutiPelatihanSeeder::class,

            // Seeder (WLKP, Pengaduan Norma, SMK3, Self Assessment) - Binwasnaker
            PelaporanWlkpOnlineSeeder::class,
            PengaduanPelanggaranNormaSeeder::class,
            PenerapanSmk3Seeder::class,
            SelfAssessmentNorma100Seeder::class,

            // Seeder (PHK, Perselisihan TL, Mediasi Berhasil, SUSU) - PHI
            JumlahPhkSeeder::class,
            PerselisihanDitindaklanjutiSeeder::class,
            MediasiBerhasilSeeder::class,
            PerusahaanMenerapkanSusuSeeder::class,

            // Seeder  Binapenta
            JumlahPenempatanKemnakerSeeder::class, 
            JumlahLowonganPaskerSeeder::class, 
            JumlahTkaDisetujuiSeeder::class, 

            // Tambahkan seeder untuk Tabel 3.2 (Jumlah lowongan pekerjaan baru di Pasker) dan Tabel 3.3 (Persetujuan RPTKA) di sini jika sudah dibuat.
            // Contoh:
            // JumlahLowonganPaskerSeeder::class,
            // PersetujuanRptkaSeeder::class,
        ]); // Akhir dari array $this->call
    } // Akhir dari method run()
} // Akhir dari class DatabaseSeeder
