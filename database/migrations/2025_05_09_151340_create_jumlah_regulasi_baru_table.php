<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jumlah_regulasi_baru', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan')->comment('1-12');
            $table->string('kode_satuan_kerja');
            $table->tinyInteger('jenis_regulasi')->comment('1: UU, 2: Peraturan Pemerintah (bukan Perusahaan), 3: Permen, 4: Kepmen');
            $table->integer('jumlah_regulasi')->default(0);
            $table->timestamps();

            $table->foreign('kode_satuan_kerja')
                  ->references('kode_sk')
                  ->on('satuan_kerja')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            
            $table->index(['tahun', 'bulan']);
            $table->index('kode_satuan_kerja');
            $table->index('jenis_regulasi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jumlah_regulasi_baru');
    }
};