<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('persentase_kehadiran', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan')->comment('1-12');
            $table->string('kode_unit_kerja_eselon_i'); // Diasumsikan dari PDF kolom (4)
            $table->tinyInteger('status_asn')->comment('1: ASN, 2: Non ASN');
            $table->tinyInteger('status_kehadiran')->comment('1: WFO, 2: Cuti, 3: Dinas Luar, 4: Sakit, 5: Tugas Belajar, 6: Tanpa Keterangan');
            $table->integer('jumlah_orang')->default(0);
            $table->timestamps();

            $table->foreign('kode_unit_kerja_eselon_i')
                  ->references('kode_uke1')
                  ->on('unit_kerja_eselon_i')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->index(['tahun', 'bulan']);
            $table->index('kode_unit_kerja_eselon_i');
            $table->index('status_asn');
            $table->index('status_kehadiran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('persentase_kehadiran');
    }
};
