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
            $table->string('provinsi');
            $table->string('kbli');
            $table->string('skala_perusahaan')->comment('Mikro, Kecil, Menengah, Besar');
            $table->string('hasil_assessment')->comment('Rendah (<70), Sedang (71-90), Tinggi (91-100)');
            $table->integer('jumlah_perusahaan')->default(0);
            $table->timestamps();

            $table->index(['tahun', 'bulan']);
            $table->index('provinsi');
            $table->index('kbli');
            $table->index('skala_perusahaan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('self_assessment_norma100');
    }
};