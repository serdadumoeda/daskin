<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress_mou', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan')->comment('1-12');
            $table->text('judul_mou');
            $table->date('tanggal_mulai_perjanjian');
            $table->date('tanggal_selesai_perjanjian')->nullable();
            $table->text('pihak_terlibat')->nullable();
            $table->timestamps();

            $table->index(['tahun', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_mou');
    }
};