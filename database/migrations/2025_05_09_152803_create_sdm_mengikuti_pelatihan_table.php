<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sdm_mengikuti_pelatihan', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan')->comment('1-12');
            $table->string('kode_unit_kerja_eselon_i');
            $table->string('kode_satuan_kerja');
            $table->tinyInteger('jenis_pelatihan')->comment('1: Diklat Dasar, 2: Diklat Kepemimpinan, 3: Diklat Fungsional');
            $table->integer('jumlah_peserta')->default(0);
            $table->timestamps();

            $table->foreign('kode_unit_kerja_eselon_i')
                  ->references('kode_uke1')
                  ->on('unit_kerja_eselon_i')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            
            $table->foreign('kode_satuan_kerja')
                  ->references('kode_sk')
                  ->on('satuan_kerja')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->index(['tahun', 'bulan']);
            $table->index('kode_unit_kerja_eselon_i');
            $table->index('kode_satuan_kerja');
            $table->index('jenis_pelatihan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sdm_mengikuti_pelatihan');
    }
};