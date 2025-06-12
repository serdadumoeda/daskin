<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perselisihan_ditindaklanjuti', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan')->comment('1-12'); // Pastikan kolom ini ada
            $table->string('provinsi');
            $table->string('jenis_perselisihan')->comment('Perselisihan Hak, Kepentingan, PHK, SP/SB');
            $table->string('cara_penyelesaian')->comment('Bipartit, Mediasi, Konsoliasi, Arbitrasi');
            $table->integer('jumlah_perselisihan')->default(0);
            $table->integer('jumlah_ditindaklanjuti')->default(0);
            $table->timestamps();

            $table->index(['tahun', 'bulan']);
            $table->index('provinsi');
            $table->index('jenis_perselisihan');
            $table->index('cara_penyelesaian');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perselisihan_ditindaklanjuti');
    }
};
