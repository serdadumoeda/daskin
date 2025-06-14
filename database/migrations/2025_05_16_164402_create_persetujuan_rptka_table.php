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
        Schema::create('persetujuan_rptka', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan')->comment('1-12');
            $table->tinyInteger('jenis_kelamin')->comment('1: Laki-laki, 2: Perempuan');
            $table->string('negara_asal', 100); // Misal ISO Alpha-3 atau nama negara
            $table->tinyInteger('jabatan')->comment('Refers to predefined list: 1: Advisor/Consultant, 2: Direksi, 3: Komisaris, 4: Manager, 5: Profesional');
            $table->string('lapangan_usaha_kbli', 255)->comment('Deskripsi Lapangan Usaha KBLI');
            $table->string('provinsi_penempatan', 100)->comment('Nama provinsi atau Lintas Provinsi'); 
            $table->tinyInteger('status_pengajuan')->comment('1: Diterima, 2: Ditolak');
            $table->integer('jumlah')->default(0);
            $table->timestamps();

            $table->index(['tahun', 'bulan']);
            $table->index('jenis_kelamin');
            $table->index('negara_asal');
            $table->index('jabatan');
            $table->index('lapangan_usaha_kbli');
            $table->index('provinsi_penempatan');
            $table->index('status_pengajuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persetujuan_rptka');
    }
};