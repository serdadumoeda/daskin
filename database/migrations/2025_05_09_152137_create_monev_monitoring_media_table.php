<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monev_monitoring_media', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan')->comment('1-12');
            $table->tinyInteger('jenis_media')->comment('1: Media Cetak, 2: Media Online, 3: Media Elektronik');
            $table->tinyInteger('sentimen_publik')->comment('1: Sentimen Positif, 2: Sentimen Negatif');
            $table->integer('jumlah_berita')->default(0); // Kolom "Jumlah" di PDF, dinamai ulang
            $table->timestamps();

            $table->index(['tahun', 'bulan']);
            $table->index('jenis_media');
            $table->index('sentimen_publik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monev_monitoring_media');
    }
};