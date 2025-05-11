<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mediasi_berhasil', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan')->comment('1-12');
            $table->string('provinsi');
            $table->string('kbli');
            $table->string('jenis_perselisihan')->comment('Perselisihan Hak, Kepentingan, PHK, SP/SB');
            $table->string('hasil_mediasi')->comment('PB (Perjanjian Bersama), anjuran');
            $table->integer('jumlah_mediasi')->default(0);
            $table->integer('jumlah_mediasi_berhasil')->default(0);
            $table->timestamps();

            $table->index(['tahun', 'bulan']);
            $table->index('provinsi');
            $table->index('kbli');
            $table->index('jenis_perselisihan');
            $table->index('hasil_mediasi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mediasi_berhasil');
    }
};