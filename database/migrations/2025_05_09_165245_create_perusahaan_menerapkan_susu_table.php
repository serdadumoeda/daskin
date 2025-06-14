<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perusahaan_menerapkan_susu', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan')->comment('1-12');
            $table->string('provinsi');
            $table->string('kbli');
            $table->integer('jumlah_perusahaan_susu')->default(0)->comment('Jumlah Perusahaan yang Menerapkan Struktur dan Skala Upah');
            $table->timestamps();

            $table->index(['tahun', 'bulan']);
            $table->index('provinsi');
            $table->index('kbli');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perusahaan_menerapkan_susu');
    }
};