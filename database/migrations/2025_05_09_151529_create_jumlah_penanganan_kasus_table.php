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
            // Kolom kode_satuan_kerja dihapus dan diganti dengan substansi
            $table->string('substansi'); // Kolom baru sesuai permintaan
            $table->string('jenis_perkara'); // Sesuai PDF, tidak ada kode numerik spesifik di tabel 2.3
            $table->integer('jumlah_perkara')->default(0);
            $table->timestamps();

            // Foreign key ke satuan_kerja dihapus
            // $table->foreign('kode_satuan_kerja')
            //       ->references('kode_sk')
            //       ->on('satuan_kerja')
            //       ->onDelete('cascade')
            //       ->onUpdate('cascade');

            $table->index(['tahun', 'bulan']);
            // Index untuk kode_satuan_kerja dihapus, bisa ditambahkan index untuk substansi jika sering difilter
            $table->index('substansi'); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jumlah_penanganan_kasus');
    }
};