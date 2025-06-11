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
        Schema::create('pelaporan_wlkp_online', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan')->comment('1-12');
            $table->string('provinsi');
            // Ini adalah kolom yang menyebabkan error, pastikan namanya benar
            $table->integer('jumlah_perusahaan_melapor')->default(0)->comment('Jumlah perusahaan yang melaporkan WLKP');
            $table->timestamps();

            $table->index(['tahun', 'bulan']);
            $table->index('provinsi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelaporan_wlkp_online');
    }
};
