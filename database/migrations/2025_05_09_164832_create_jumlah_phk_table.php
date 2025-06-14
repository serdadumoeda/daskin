<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jumlah_phk', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan')->comment('1-12');
            $table->string('provinsi');
            $table->integer('jumlah_perusahaan_phk')->default(0)->comment('Jumlah Perusahaan yang melakukan PHK');
            $table->integer('jumlah_tk_phk')->default(0)->comment('Jumlah Tenaga Kerja yang di PHK');
            $table->timestamps();

            $table->index(['tahun', 'bulan']);
            $table->index('provinsi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jumlah_phk');
    }
};
