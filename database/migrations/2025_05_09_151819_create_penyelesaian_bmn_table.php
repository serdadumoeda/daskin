<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyelesaian_bmn', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan')->comment('1-12');
            
            // Mengganti 'unit_kerja' string dengan foreign key ke tabel 'satuan_kerja'
            $table->string('kode_satuan_kerja'); // Pastikan tipe data ini sama dengan 'kode_sk' di tabel 'satuan_kerja'
            $table->foreign('kode_satuan_kerja')
                  ->references('kode_sk')->on('satuan_kerja') // Mengacu pada tabel 'satuan_kerja' dan kolom 'kode_sk'
                  ->onUpdate('cascade')->onDelete('restrict'); // atau onDelete('cascade') sesuai kebutuhan

            $table->tinyInteger('jenis_bmn')->comment('Refers to predefined list');
            $table->boolean('henti_guna')->comment('1: Ya, 0: Tidak');
            $table->tinyInteger('status_penggunaan')->comment('Refers to predefined list');
            $table->string('penetapan_status_penggunaan')->nullable();
            $table->integer('kuantitas');
            $table->decimal('nilai_aset', 15, 2);
            $table->timestamps();

            $table->index(['tahun', 'bulan']);
            $table->index('kode_satuan_kerja'); // Index untuk foreign key
            $table->index('jenis_bmn');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyelesaian_bmn');
    }
};