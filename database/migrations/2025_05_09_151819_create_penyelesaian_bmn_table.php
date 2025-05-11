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
            $table->string('kode_satuan_kerja');
            $table->tinyInteger('status_penggunaan_aset')->comment('1: Aset Digunakan, 2: Aset Tetap Tidak Digunakan');
            $table->tinyInteger('status_aset_digunakan')->nullable()->comment('Jika status_penggunaan_aset=1; 1: Sudah PSP, 2: Belum PSP');
            $table->string('nup')->nullable()->comment('Nomor Urut Pendaftaran, wajib jika status_aset_digunakan=2');
            $table->integer('kuantitas')->default(0);
            $table->decimal('nilai_aset_rp', 19, 2)->default(0.00);
            $table->decimal('total_aset_rp', 19, 2)->default(0.00)->comment('Untuk Aset Tidak Digunakan, atau bisa juga Kuantitas*Nilai Aset');
            $table->timestamps();

            $table->foreign('kode_satuan_kerja')
                  ->references('kode_sk')
                  ->on('satuan_kerja')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->index(['tahun', 'bulan']);
            $table->index('kode_satuan_kerja');
            $table->index('status_penggunaan_aset');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyelesaian_bmn');
    }
};