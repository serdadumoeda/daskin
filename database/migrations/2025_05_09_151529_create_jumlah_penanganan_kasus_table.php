<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jumlah_penanganan_kasus', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan')->comment('1-12');
            $table->string('kode_satuan_kerja');
            $table->string('jenis_perkara'); // Sesuai PDF, tidak ada kode numerik
            $table->integer('jumlah_perkara')->default(0);
            $table->timestamps();

            $table->foreign('kode_satuan_kerja')
                  ->references('kode_sk')
                  ->on('satuan_kerja')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->index(['tahun', 'bulan']);
            $table->index('kode_satuan_kerja');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jumlah_penanganan_kasus');
    }
};