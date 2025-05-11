<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);


        $this->call(KemnakerDataSeeder::class); 


        $this->call([
            UnitKerjaEselonISeeder::class,
            SatuanKerjaSeeder::class,
            // Tambahkan seeder baru di sini:
            ProgressTemuanBpkSeeder::class,
            ProgressTemuanInternalSeeder::class,
        ]);

        $this->call([
            // Seeder yang sudah ada
            UnitKerjaEselonISeeder::class,
            SatuanKerjaSeeder::class,
            ProgressTemuanBpkSeeder::class,
            ProgressTemuanInternalSeeder::class,

            // Seeder baru dari PDF ini
            ProgressMouSeeder::class,
            JumlahRegulasiBaruSeeder::class,
            JumlahPenangananKasusSeeder::class,
            PenyelesaianBmnSeeder::class,
            // IKPA Seeder (dilewati)
            PersentaseKehadiranSeeder::class,
            MonevMonitoringMediaSeeder::class,
            LulusanPolteknakerBekerjaSeeder::class,
            SdmMengikutiPelatihanSeeder::class,
        ]);

        $this->call([
            // Seeder yang sudah ada sebelumnya
            UnitKerjaEselonISeeder::class,
            SatuanKerjaSeeder::class,
            ProgressTemuanBpkSeeder::class,
            ProgressTemuanInternalSeeder::class,
            ProgressMouSeeder::class,
            JumlahRegulasiBaruSeeder::class,
            JumlahPenangananKasusSeeder::class,
            PenyelesaianBmnSeeder::class,
            PersentaseKehadiranSeeder::class,
            MonevMonitoringMediaSeeder::class,
            LulusanPolteknakerBekerjaSeeder::class,
            SdmMengikutiPelatihanSeeder::class,

            // Seeder baru dari PDF ini
            PelaporanWlkpOnlineSeeder::class,
            PengaduanPelanggaranNormaSeeder::class,
            PenerapanSmk3Seeder::class,
            SelfAssessmentNorma100Seeder::class,
        ]);

        $this->call([
            // Seeder yang sudah ada sebelumnya
            UnitKerjaEselonISeeder::class,
            SatuanKerjaSeeder::class,
            ProgressTemuanBpkSeeder::class,
            ProgressTemuanInternalSeeder::class,
            ProgressMouSeeder::class,
            JumlahRegulasiBaruSeeder::class,
            JumlahPenangananKasusSeeder::class,
            PenyelesaianBmnSeeder::class,
            PersentaseKehadiranSeeder::class,
            MonevMonitoringMediaSeeder::class,
            LulusanPolteknakerBekerjaSeeder::class,
            SdmMengikutiPelatihanSeeder::class,
            PelaporanWlkpOnlineSeeder::class,
            PengaduanPelanggaranNormaSeeder::class,
            PenerapanSmk3Seeder::class,
            SelfAssessmentNorma100Seeder::class,

            // Seeder baru dari PDF ini
            JumlahPhkSeeder::class,
            PerselisihanDitindaklanjutiSeeder::class,
            MediasiBerhasilSeeder::class,
            PerusahaanMenerapkanSusuSeeder::class,
        ]);

        $this->call([
            
            ProgressMouSeeder::class, 
            
        ]);
    }
    
}
