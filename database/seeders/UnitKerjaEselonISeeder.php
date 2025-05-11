<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UnitKerjaEselonI;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class UnitKerjaEselonISeeder extends Seeder
{
    public function run(): void
    {
        $connection = Config::get('database.default');
        $driver = Config::get("database.connections.{$connection}.driver");

        if ($driver === 'pgsql') {
            // Untuk PostgreSQL, RESTART IDENTITY tidak relevan jika PK bukan serial.
            // CASCADE akan menangani foreign keys.
            DB::statement('TRUNCATE TABLE unit_kerja_eselon_i CASCADE;');
        } elseif ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('unit_kerja_eselon_i')->truncate(); // truncate() Laravel seharusnya aman
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } else {
            DB::table('unit_kerja_eselon_i')->truncate();
        }

        // Data sudah memiliki kode_uke1 sebagai primary key
        $unitKerja = [
            ['kode_uke1' => 'UKE1-001', 'nama_unit_kerja_eselon_i' => 'Sekretariat Jenderal'],
            ['kode_uke1' => 'UKE1-002', 'nama_unit_kerja_eselon_i' => 'Direktorat Jenderal Pembinaan Pelatihan Vokasi dan Produktivitas'],
            ['kode_uke1' => 'UKE1-003', 'nama_unit_kerja_eselon_i' => 'Direktorat Jenderal Pembinaan Penempatan Tenaga Kerja dan Perluasan Kesempatan Kerja'],
            ['kode_uke1' => 'UKE1-004', 'nama_unit_kerja_eselon_i' => 'Direktorat Jenderal Pembinaan Hubungan Industrial dan Jaminan Sosial Tenaga Kerja'],
            ['kode_uke1' => 'UKE1-005', 'nama_unit_kerja_eselon_i' => 'Direktorat Jenderal Pembinaan Pengawasan Ketenagakerjaan dan Keselamatan dan Kesehatan Kerja'],
            ['kode_uke1' => 'UKE1-006', 'nama_unit_kerja_eselon_i' => 'Badan Perencanaan dan Pengembangan Ketenagakerjaan'],
            ['kode_uke1' => 'UKE1-007', 'nama_unit_kerja_eselon_i' => 'Inspektorat Jenderal'],
            ['kode_uke1' => 'UKE1-008', 'nama_unit_kerja_eselon_i' => 'Staf Ahli Bidang Ekonomi Ketenagakerjaan'],
            ['kode_uke1' => 'UKE1-009', 'nama_unit_kerja_eselon_i' => 'Staf Ahli Bidang Hubungan Internasional'],
            ['kode_uke1' => 'UKE1-010', 'nama_unit_kerja_eselon_i' => 'Staf Ahli Bidang Hubungan Antar Lembaga'],
            ['kode_uke1' => 'UKE1-011', 'nama_unit_kerja_eselon_i' => 'Staf Ahli Bidang Sosial, Politik, dan Kebijakan Publik'],
        ];

        foreach ($unitKerja as $unit) {
            UnitKerjaEselonI::create($unit);
        }
    }
}