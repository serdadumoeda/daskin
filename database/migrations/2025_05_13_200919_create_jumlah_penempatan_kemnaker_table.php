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
        Schema::create('jumlah_penempatan_kemnaker', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan')->comment('1-12');
            $table->tinyInteger('jenis_kelamin')->comment('1: Laki-laki, 2: Perempuan');
            $table->string('provinsi_domisili');
            $table->string('lapangan_usaha_kbli'); // Bisa berupa kode kategori KBLI atau deskripsi
            $table->tinyInteger('status_disabilitas')->comment('1: Ya, 2: Tidak');
            $table->string('ragam_disabilitas')->nullable()->comment('Fisik, Intelektual, Mental, Sensorik, Lebih dari 1');
            $table->integer('jumlah')->default(0)->comment('Jumlah orang yang ditempatkan');
            $table->timestamps();

            $table->index(['tahun', 'bulan']);
            $table->index('provinsi_domisili');
            $table->index('jenis_kelamin');
            $table->index('status_disabilitas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jumlah_penempatan_kemnaker');
    }
};
