<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaduan_pelanggaran_norma', function (Blueprint $table) {
            $table->id();
            $table->year('tahun_tindak_lanjut');
            $table->tinyInteger('bulan_tindak_lanjut')->comment('1-12');
            $table->string('jenis_tindak_lanjut');
            $table->integer('jumlah_pengaduan_tindak_lanjut');
            $table->timestamps();

            $table->index(['tahun_tindak_lanjut', 'bulan_tindak_lanjut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaduan_pelanggaran_norma');
    }
};
