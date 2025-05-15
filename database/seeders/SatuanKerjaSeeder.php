<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SatuanKerja;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Carbon; // <-- Tambahkan ini

class SatuanKerjaSeeder extends Seeder
{
    public function run(): void
    {
        $connection = Config::get('database.default');
        $driver = Config::get("database.connections.{$connection}.driver");

        if ($driver === 'pgsql') {
            DB::statement('TRUNCATE TABLE satuan_kerja CASCADE;');
        } elseif ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('satuan_kerja')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } else {
            DB::table('satuan_kerja')->truncate();
        }
        
        $now = Carbon::now(); // <-- Dapatkan waktu saat ini
        $satuanKerjaData = [];

        //======================================================================
        // 1. SEKRETARIAT JENDERAL (UKE1-001)
        //======================================================================
        $kodeUKE1Sekjen = 'UKE1-001';
        $dataSekjen = [
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Sekjen, 'kode_sk' => 'SK-001', 'nama_satuan_kerja' => 'Biro Perencanaan dan Manajemen Kinerja', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Sekjen, 'kode_sk' => 'SK-002', 'nama_satuan_kerja' => 'Biro Keuangan dan Barang Milik Negara', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Sekjen, 'kode_sk' => 'SK-003', 'nama_satuan_kerja' => 'Biro Organisasi dan Sumber Daya Manusia Aparatur', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Sekjen, 'kode_sk' => 'SK-004', 'nama_satuan_kerja' => 'Biro Hukum', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Sekjen, 'kode_sk' => 'SK-005', 'nama_satuan_kerja' => 'Biro Umum', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Sekjen, 'kode_sk' => 'SK-006', 'nama_satuan_kerja' => 'Biro Kerja Sama', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Sekjen, 'kode_sk' => 'SK-007', 'nama_satuan_kerja' => 'Biro Hubungan Masyarakat', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Sekjen, 'kode_sk' => 'SK-008', 'nama_satuan_kerja' => 'Pusat Pelatihan Sumber Daya Manusia Ketenagakerjaan', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Sekjen, 'kode_sk' => 'SK-009', 'nama_satuan_kerja' => 'Pusat Pasar Kerja', 'created_at' => $now, 'updated_at' => $now],
        ];
        $satuanKerjaData = array_merge($satuanKerjaData, $dataSekjen);

        //======================================================================
        // 2. DIREKTORAT JENDERAL PEMBINAAN PELATIHAN VOKASI DAN PRODUKTIVITAS (UKE1-002)
        //======================================================================
        $kodeUKE1Binalavotas = 'UKE1-002';
        $dataDitjenBinalavotas = [
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-100', 'nama_satuan_kerja' => 'Sekretariat Direktorat Jenderal Pembinaan Pelatihan Vokasi dan Produktivitas', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-101', 'nama_satuan_kerja' => 'Direktorat Bina Standardisasi Kompetensi dan Program Pelatihan', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-102', 'nama_satuan_kerja' => 'Direktorat Bina Kelembagaan Pelatihan Vokasi', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-103', 'nama_satuan_kerja' => 'Direktorat Bina Penyelenggaraan Pelatihan Vokasi dan Pemagangan', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-104', 'nama_satuan_kerja' => 'Direktorat Bina Instruktur dan Tenaga Pelatihan', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-105', 'nama_satuan_kerja' => 'Direktorat Bina Peningkatan Produktivitas', 'created_at' => $now, 'updated_at' => $now],
            // UPTs
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-106', 'nama_satuan_kerja' => 'Balai Besar Pelatihan Vokasi dan Produktivitas Medan', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-107', 'nama_satuan_kerja' => 'Balai Besar Pelatihan Vokasi dan Produktivitas Serang', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-108', 'nama_satuan_kerja' => 'Balai Besar Pelatihan Vokasi dan Produktivitas Bekasi', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-109', 'nama_satuan_kerja' => 'Balai Besar Pelatihan Vokasi dan Produktivitas Bandung', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-110', 'nama_satuan_kerja' => 'Balai Besar Pelatihan Vokasi dan Produktivitas Semarang', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-111', 'nama_satuan_kerja' => 'Balai Besar Pelatihan Vokasi dan Produktivitas Surakarta', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-112', 'nama_satuan_kerja' => 'Balai Besar Pelatihan Vokasi dan Produktivitas Makassar', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-113', 'nama_satuan_kerja' => 'Balai Pelatihan Vokasi dan Produktivitas Banda Aceh', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-114', 'nama_satuan_kerja' => 'Balai Pelatihan Vokasi dan Produktivitas Padang', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-115', 'nama_satuan_kerja' => 'Balai Pelatihan Vokasi dan Produktivitas Pekanbaru', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-116', 'nama_satuan_kerja' => 'Balai Pelatihan Vokasi dan Produktivitas Jambi', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-117', 'nama_satuan_kerja' => 'Balai Pelatihan Vokasi dan Produktivitas Palembang', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-118', 'nama_satuan_kerja' => 'Balai Pelatihan Vokasi dan Produktivitas Lampung', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-119', 'nama_satuan_kerja' => 'Balai Pelatihan Vokasi dan Produktivitas Lombok Timur', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-120', 'nama_satuan_kerja' => 'Balai Pelatihan Vokasi dan Produktivitas Samarinda', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-121', 'nama_satuan_kerja' => 'Balai Pelatihan Vokasi dan Produktivitas Banyuwangi', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-122', 'nama_satuan_kerja' => 'Balai Pelatihan Vokasi dan Produktivitas Sidoarjo', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-123', 'nama_satuan_kerja' => 'Balai Pelatihan Vokasi dan Produktivitas Kendari', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-124', 'nama_satuan_kerja' => 'Balai Pelatihan Vokasi dan Produktivitas Ternate', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-125', 'nama_satuan_kerja' => 'Balai Pelatihan Vokasi dan Produktivitas Ambon', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-126', 'nama_satuan_kerja' => 'Balai Pelatihan Vokasi dan Produktivitas Sorong', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binalavotas, 'kode_sk' => 'SK-127', 'nama_satuan_kerja' => 'Sekretariat Badan Nasional Sertifikasi Profesi', 'created_at' => $now, 'updated_at' => $now],
        ];
        $satuanKerjaData = array_merge($satuanKerjaData, $dataDitjenBinalavotas);

        //======================================================================
        // 3. DIREKTORAT JENDERAL PEMBINAAN PENEMPATAN TENAGA KERJA DAN PERLUASAN KESEMPATAN KERJA (UKE1-003)
        //======================================================================
        $kodeUKE1Binapenta = 'UKE1-003';
        $dataDitjenBinapenta = [
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binapenta, 'kode_sk' => 'SK-200', 'nama_satuan_kerja' => 'Sekretariat Direktorat Jenderal Pembinaan Penempatan Tenaga Kerja dan Perluasan Kesempatan Kerja', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binapenta, 'kode_sk' => 'SK-201', 'nama_satuan_kerja' => 'Direktorat Bina Penempatan Tenaga Kerja Dalam Negeri', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binapenta, 'kode_sk' => 'SK-202', 'nama_satuan_kerja' => 'Direktorat Bina Pelindungan Pekerja Migran Indonesia', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binapenta, 'kode_sk' => 'SK-203', 'nama_satuan_kerja' => 'Direktorat Bina Penempatan dan Pelindungan Pekerja Migran Indonesia', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binapenta, 'kode_sk' => 'SK-204', 'nama_satuan_kerja' => 'Direktorat Bina Perluasan Kesempatan Kerja', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binapenta, 'kode_sk' => 'SK-205', 'nama_satuan_kerja' => 'Direktorat Bina Pengembangan Pasar Kerja', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binapenta, 'kode_sk' => 'SK-206', 'nama_satuan_kerja' => 'Balai Pelayanan Pelindungan Pekerja Migran Indonesia (BP3MI) Jakarta', 'created_at' => $now, 'updated_at' => $now],
        ];
        $satuanKerjaData = array_merge($satuanKerjaData, $dataDitjenBinapenta);

        //======================================================================
        // 4. DIREKTORAT JENDERAL PEMBINAAN HUBUNGAN INDUSTRIAL DAN JAMINAN SOSIAL TENAGA KERJA (UKE1-004)
        //======================================================================
        $kodeUKE1Phijsk = 'UKE1-004';
        $dataDitjenPhijsk = [
             ['kode_unit_kerja_eselon_i' => $kodeUKE1Phijsk, 'kode_sk' => 'SK-300', 'nama_satuan_kerja' => 'Sekretariat Direktorat Jenderal Pembinaan Hubungan Industrial dan Jaminan Sosial Tenaga Kerja', 'created_at' => $now, 'updated_at' => $now],
             ['kode_unit_kerja_eselon_i' => $kodeUKE1Phijsk, 'kode_sk' => 'SK-301', 'nama_satuan_kerja' => 'Direktorat Bina Kelembagaan Hubungan Industrial', 'created_at' => $now, 'updated_at' => $now],
             ['kode_unit_kerja_eselon_i' => $kodeUKE1Phijsk, 'kode_sk' => 'SK-302', 'nama_satuan_kerja' => 'Direktorat Bina Persyaratan Kerja, Kesejahteraan, dan Analisis Diskriminasi', 'created_at' => $now, 'updated_at' => $now],
             ['kode_unit_kerja_eselon_i' => $kodeUKE1Phijsk, 'kode_sk' => 'SK-303', 'nama_satuan_kerja' => 'Direktorat Bina Pengupahan', 'created_at' => $now, 'updated_at' => $now],
             ['kode_unit_kerja_eselon_i' => $kodeUKE1Phijsk, 'kode_sk' => 'SK-304', 'nama_satuan_kerja' => 'Direktorat Bina Penyelesaian Perselisihan Hubungan Industrial', 'created_at' => $now, 'updated_at' => $now],
             ['kode_unit_kerja_eselon_i' => $kodeUKE1Phijsk, 'kode_sk' => 'SK-305', 'nama_satuan_kerja' => 'Direktorat Bina Jaminan Sosial Tenaga Kerja', 'created_at' => $now, 'updated_at' => $now],
        ];
        $satuanKerjaData = array_merge($satuanKerjaData, $dataDitjenPhijsk);

        //======================================================================
        // 5. DIREKTORAT JENDERAL PEMBINAAN PENGAWASAN KETENAGAKERJAAN DAN KESELAMATAN DAN KESEHATAN KERJA (UKE1-005)
        //======================================================================
        $kodeUKE1Binwasnaker = 'UKE1-005';
        $dataDitjenBinwasnaker = [
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binwasnaker, 'kode_sk' => 'SK-400', 'nama_satuan_kerja' => 'Sekretariat Direktorat Jenderal Pembinaan Pengawasan Ketenagakerjaan dan Keselamatan dan Kesehatan Kerja', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binwasnaker, 'kode_sk' => 'SK-401', 'nama_satuan_kerja' => 'Direktorat Bina Sistem Pengawasan Ketenagakerjaan', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binwasnaker, 'kode_sk' => 'SK-402', 'nama_satuan_kerja' => 'Direktorat Bina Pemeriksaan Norma Ketenagakerjaan', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binwasnaker, 'kode_sk' => 'SK-403', 'nama_satuan_kerja' => 'Direktorat Bina Pengujian Keselamatan dan Kesehatan Kerja', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binwasnaker, 'kode_sk' => 'SK-404', 'nama_satuan_kerja' => 'Direktorat Bina Kelembagaan Keselamatan dan Kesehatan Kerja', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binwasnaker, 'kode_sk' => 'SK-405', 'nama_satuan_kerja' => 'Direktorat Bina Pengawasan Norma Kerja Perempuan dan Anak', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binwasnaker, 'kode_sk' => 'SK-406', 'nama_satuan_kerja' => 'Balai Besar Keselamatan dan Kesehatan Kerja Jakarta', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binwasnaker, 'kode_sk' => 'SK-407', 'nama_satuan_kerja' => 'Balai Keselamatan dan Kesehatan Kerja Medan', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binwasnaker, 'kode_sk' => 'SK-408', 'nama_satuan_kerja' => 'Balai Keselamatan dan Kesehatan Kerja Bandung', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binwasnaker, 'kode_sk' => 'SK-409', 'nama_satuan_kerja' => 'Balai Keselamatan dan Kesehatan Kerja Samarinda', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Binwasnaker, 'kode_sk' => 'SK-410', 'nama_satuan_kerja' => 'Balai Besar Pengembangan Keselamatan dan Kesehatan Kerja Makassar', 'created_at' => $now, 'updated_at' => $now],
        ];
        $satuanKerjaData = array_merge($satuanKerjaData, $dataDitjenBinwasnaker);
        
        //======================================================================
        // 6. BADAN PERENCANAAN DAN PENGEMBANGAN KETENAGAKERJAAN (UKE1-006)
        //======================================================================
        $kodeUKE1Barenbang = 'UKE1-006';
        $dataBarenbang = [
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Barenbang, 'kode_sk' => 'SK-500', 'nama_satuan_kerja' => 'Sekretariat Badan Perencanaan dan Pengembangan Ketenagakerjaan', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Barenbang, 'kode_sk' => 'SK-501', 'nama_satuan_kerja' => 'Pusat Perencanaan Ketenagakerjaan', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Barenbang, 'kode_sk' => 'SK-502', 'nama_satuan_kerja' => 'Pusat Data dan Teknologi Informasi Ketenagakerjaan', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Barenbang, 'kode_sk' => 'SK-503', 'nama_satuan_kerja' => 'Pusat Pengembangan Kebijakan Ketenagakerjaan', 'created_at' => $now, 'updated_at' => $now],
        ];
        $satuanKerjaData = array_merge($satuanKerjaData, $dataBarenbang);

        //======================================================================
        // 7. INSPEKTORAT JENDERAL (UKE1-007)
        //======================================================================
        $kodeUKE1Itjen = 'UKE1-007';
        $dataItjen = [
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Itjen, 'kode_sk' => 'SK-600', 'nama_satuan_kerja' => 'Sekretariat Inspektorat Jenderal', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Itjen, 'kode_sk' => 'SK-601', 'nama_satuan_kerja' => 'Inspektorat I', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Itjen, 'kode_sk' => 'SK-602', 'nama_satuan_kerja' => 'Inspektorat II', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Itjen, 'kode_sk' => 'SK-603', 'nama_satuan_kerja' => 'Inspektorat III', 'created_at' => $now, 'updated_at' => $now],
            ['kode_unit_kerja_eselon_i' => $kodeUKE1Itjen, 'kode_sk' => 'SK-604', 'nama_satuan_kerja' => 'Inspektorat IV', 'created_at' => $now, 'updated_at' => $now],

        ];
        $satuanKerjaData = array_merge($satuanKerjaData, $dataItjen);

        
        if (!empty($satuanKerjaData)) {
            foreach (array_chunk($satuanKerjaData, 200) as $chunk) {
                SatuanKerja::insert($chunk); 
            }
        }
    }
}