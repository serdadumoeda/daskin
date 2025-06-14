<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lulusan_polteknaker_bekerja', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan')->comment('1-12, atau bisa merepresentasikan periode kelulusan jika bukan bulanan');
            $table->tinyInteger('program_studi')->comment('1: Relasi Industri, 2: K3, 3: MSDM');
            $table->integer('jumlah_lulusan')->default(0);
            $table->integer('jumlah_lulusan_bekerja')->default(0);
            $table->timestamps();

            $table->index(['tahun', 'bulan']);
            $table->index('program_studi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lulusan_polteknaker_bekerja');
    }
};