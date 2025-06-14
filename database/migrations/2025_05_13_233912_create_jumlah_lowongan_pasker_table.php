<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jumlah_lowongan_pasker', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan')->comment('1-12');
            $table->tinyInteger('jenis_kelamin')->comment('1: Laki-laki, 2: Perempuan');
            $table->string('provinsi_penempatan', 100)->comment('Nama provinsi atau Lintas Provinsi'); // String bebas
            $table->string('lapangan_usaha_kbli', 255)->comment('Deskripsi Lapangan Usaha KBLI'); // String bebas
            $table->tinyInteger('status_disabilitas')->comment('1: Ya, 2: Tidak');
            $table->integer('jumlah_lowongan')->default(0); // Kolom ini ada di controller, saya tambahkan di sini
            $table->timestamps();

            $table->index(['tahun', 'bulan']);
            $table->index('jenis_kelamin');
            $table->index('provinsi_penempatan');
            $table->index('lapangan_usaha_kbli');
            $table->index('status_disabilitas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jumlah_lowongan_pasker');
    }
};