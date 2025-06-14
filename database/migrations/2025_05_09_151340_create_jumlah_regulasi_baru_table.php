<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jumlah_regulasi_baru', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan')->comment('1-12');
            $table->tinyInteger('substansi')->comment('1: Perencanaan dan Pengembangan, 2: Pelatihan Vokasi dan Produktivitas, 3: Penempatan Tenaga Kerja dan Perluasan Kesempatan Kerja, 4: Hubungan Industrial dan Jaminan Sosial, 5: Pengawasan Ketenagakerjaan dan K3, 6: Pengawasan Internal, 7: Kesekretariatan, 8: Lainnya');
            $table->tinyInteger('jenis_regulasi')->comment('1: UU, 2: PP, 3: Perpres, 4: Keppres, 5: Inpres, 6: Permen, 7: Kepmen, 8: SE/Instruksi Menteri, 9: Peraturan/Keputusan Pejabat Eselon I, 10: Peraturan Terkait');
            $table->integer('jumlah_regulasi')->default(0);
            $table->timestamps();

            $table->index(['tahun', 'bulan']);
            $table->index('substansi');
            $table->index('jenis_regulasi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jumlah_regulasi_baru');
    }
};