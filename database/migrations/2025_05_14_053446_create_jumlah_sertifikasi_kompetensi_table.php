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
        Schema::create('jumlah_sertifikasi_kompetensi', function (Blueprint $table) {
            $table->id(); // No (1)
            $table->year('tahun'); // (2) Tahun
            $table->tinyInteger('bulan')->comment('1-12'); // (3) Bulan
            $table->tinyInteger('jenis_lsp')->comment('1: P1, 2: P2, 3: P3'); // (4) Jenis LSP
            $table->tinyInteger('jenis_kelamin')->comment('1: Laki-laki, 2: Perempuan'); // (5) Jenis Kelamin
            $table->string('provinsi'); // (6) Provinsi (Teks nama provinsi)
            $table->string('lapangan_usaha_kbli'); // (7) Lapangan Usaha (KBLI) - Teks atau Kode Kategori
            $table->integer('jumlah_sertifikasi')->default(0)->comment('Jumlah sertifikasi'); // (8) Jumlah
            $table->timestamps();

            // Indexes for better query performance
            $table->index(['tahun', 'bulan']);
            $table->index('jenis_lsp');
            $table->index('jenis_kelamin');
            $table->index('provinsi');
            $table->index('lapangan_usaha_kbli');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jumlah_sertifikasi_kompetensi');
    }
};
