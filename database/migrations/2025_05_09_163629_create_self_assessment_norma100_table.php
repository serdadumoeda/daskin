<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('self_assessment_norma100', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('bulan')->comment('1-12');
            $table->year('tahun');
            $table->integer('jumlah_perusahaan')->default(0);
            $table->timestamps();

            $table->index(['tahun', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('self_assessment_norma100');
    }
};
