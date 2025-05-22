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
        Schema::create('ikpa', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan')->comment('1-12');
            $table->string('kode_unit_kerja_eselon_i');
            $table->string('aspek_pelaksanaan_anggaran')->comment('1: Kualitas Perencanaan Anggaran, 2: Kualitas Pelaksanaan Anggaran, 3: Kualitas Hasil Pelaksanaan Anggaran, 4: Total');
            $table->integer('nilai_aspek');
            $table->integer('konversi_bobot');
            $table->integer('dispensasi_spm');
            $table->integer('nilai_akhir')->comment('Nilai/Konversi Bobot');
            $table->timestamps();

            $table->foreign('kode_unit_kerja_eselon_i')
                ->references('kode_uke1')
                ->on('unit_kerja_eselon_i')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->index(['tahun', 'bulan']);
            $table->index('kode_unit_kerja_eselon_i');
            $table->index('aspek_pelaksanaan_anggaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ikpa');
    }
};
