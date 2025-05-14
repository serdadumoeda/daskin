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
        Schema::create('jumlah_kajian_rekomendasi', function (Blueprint $table) {
            $table->id(); // No (1)
            $table->year('tahun'); // (2) Tahun
            $table->tinyInteger('bulan')->comment('1-12'); // (3) Bulan
            $table->tinyInteger('substansi')->comment('1: Pelatihan Vokasi..., 2: Penempatan..., 3: Hubungan Industrial..., 4: Pengawasan..., 5: Lainnya'); // (4) Substansi
            $table->tinyInteger('jenis_output')->comment('1: Kajian, 2: Rekomendasi'); // (5) Kajian/Rekomendasi
            $table->integer('jumlah')->default(0); // (6) Jumlah
            $table->timestamps();

            // Indexes
            $table->index(['tahun', 'bulan']);
            $table->index('substansi');
            $table->index('jenis_output');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jumlah_kajian_rekomendasi');
    }
};
