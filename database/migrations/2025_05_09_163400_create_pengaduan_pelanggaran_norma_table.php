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
            $table->year('tahun_pengaduan');
            $table->tinyInteger('bulan_pengaduan')->comment('1-12');
            $table->year('tahun_tindak_lanjut')->nullable();
            $table->tinyInteger('bulan_tindak_lanjut')->nullable()->comment('1-12');
            $table->string('provinsi');
            $table->string('kbli')->comment('Klasifikasi Baku Lapangan Usaha Indonesia');
            $table->string('jenis_pelanggaran');
            $table->string('jenis_tindak_lanjut');
            $table->string('hasil_tindak_lanjut');
            $table->integer('jumlah_kasus')->default(0);
            $table->timestamps();

            $table->index(['tahun_pengaduan', 'bulan_pengaduan']);
            $table->index('provinsi');
            $table->index('kbli');
            $table->index('jenis_pelanggaran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaduan_pelanggaran_norma');
    }
};