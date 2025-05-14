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
        Schema::create('jumlah_tka_disetujui', function (Blueprint $table) {
            $table->id(); // No (1)
            $table->year('tahun'); // (2) Tahun
            $table->tinyInteger('bulan')->comment('1-12'); // (3) Bulan
            $table->tinyInteger('jenis_kelamin')->comment('1: Laki-laki, 2: Perempuan'); // (4) Jenis Kelamin
            $table->string('negara_asal'); // (5) Negara Asal
            $table->string('jabatan'); // (6) Jabatan
            $table->string('lapangan_usaha_kbli'); // (7) Lapangan Usaha (KBLI)
            $table->string('provinsi_penempatan'); // (8) Provinsi Penempatan
            // Kolom (9) Status Pengajuan RPTKA dihilangkan karena tabel ini khusus untuk yang disetujui
            $table->integer('jumlah_tka')->default(0)->comment('Jumlah TKA yang disetujui'); // (10) Jumlah
            $table->timestamps();

            // Indexes
            $table->index(['tahun', 'bulan']);
            $table->index('negara_asal');
            $table->index('jabatan');
            $table->index('lapangan_usaha_kbli');
            $table->index('provinsi_penempatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jumlah_tka_disetujui');
    }
};
