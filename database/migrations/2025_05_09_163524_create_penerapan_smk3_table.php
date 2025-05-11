<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penerapan_smk3', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan')->comment('1-12');
            $table->string('provinsi');
            $table->string('kbli');
            $table->string('kategori_penilaian')->comment('awal, transisi, lanjutan');
            $table->string('tingkat_pencapaian')->comment('baik, memuaskan');
            $table->string('jenis_penghargaan');
            $table->integer('jumlah_perusahaan')->default(0);
            $table->timestamps();

            $table->index(['tahun', 'bulan']);
            $table->index('provinsi');
            $table->index('kbli');
            $table->index('kategori_penilaian');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penerapan_smk3');
    }
};