<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jumlah_aplikasi_integrasi_siapkerja', function (Blueprint $table) {
            $table->id(); // No (1)
            $table->year('tahun'); // (2) Tahun
            $table->tinyInteger('bulan')->comment('1-12'); // (3) Bulan
            $table->tinyInteger('jenis_instansi')->comment('1: Kementerian, 2: Lembaga, 3: Daerah Provinsi, 4: Daerah Kabupaten/Kota'); // (4) Jenis Instansi
            $table->string('nama_instansi'); // (5) Nama Instansi
            $table->string('nama_aplikasi_website'); // (6) Nama Aplikasi/Website
            $table->tinyInteger('status_integrasi')->comment('1: Terintegrasi, 2: Belum terintegrasi'); // (7) Status Integrasi
            // Kolom "Jumlah" tidak ada di tabel PDF ini, jadi kita tidak menambahkannya.
            // Jika maksudnya adalah jumlah aplikasi, maka setiap baris adalah 1 aplikasi.
            $table->timestamps();

            // Indexes
            $table->index(['tahun', 'bulan']);
            $table->index('jenis_instansi');
            $table->index('nama_instansi');
            $table->index('status_integrasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jumlah_aplikasi_integrasi_siapkerja');
    }
};
