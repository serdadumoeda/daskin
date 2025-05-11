<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class KemnakerDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UnitKerjaEselonISeeder::class,
            SatuanKerjaSeeder::class,
        ]);
    }
}