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
            $table->id(); // No
            $table->year('tahun'); // (2) Tahun
            $table->tinyInteger('bulan')->comment('1-12'); // (3) Bulan
            $table->string('provinsi_perusahaan'); // (4) Provinsi Perusahaan (Teks nama provinsi)
            $table->string('lapangan_usaha_kbli'); // (5) Lapangan Usaha (KBLI) - Teks atau Kode Kategori
            $table->string('jabatan'); // (6) Jabatan
            $table->tinyInteger('jenis_kelamin_dibutuhkan')->comment('1: Laki-laki, 2: Perempuan, 3: Laki-laki/Perempuan'); // (7) Jenis Kelamin yang Dibutuhkan
            $table->tinyInteger('status_disabilitas_dibutuhkan')->comment('1: Ya, 2: Tidak'); // (8) Status Disabilitas yang Dibutuhkan
            $table->integer('jumlah_lowongan')->default(0)->comment('Jumlah lowongan'); // (9) Jumlah
            $table->timestamps();

            // Indexes for better query performance
            $table->index(['tahun', 'bulan']);
            $table->index('provinsi_perusahaan');
            $table->index('lapangan_usaha_kbli');
            $table->index('jabatan');
            $table->index('jenis_kelamin_dibutuhkan');
            $table->index('status_disabilitas_dibutuhkan');
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
