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
        Schema::create('jumlah_kepesertaan_pelatihan', function (Blueprint $table) {
            $table->id(); // No (1)
            $table->year('tahun'); // (2) Tahun
            $table->tinyInteger('bulan')->comment('1-12'); // (3) Bulan
            $table->tinyInteger('penyelenggara_pelatihan')->comment('1: Internal, 2: Eksternal'); // (4)
            $table->tinyInteger('tipe_lembaga')->comment('1: UPTP, 2: UPTD, 3:BLKLN, 4: Lembaga Pelatihan K/L, 5:SKPD, 6:LPK Swasta, 7:BLK Komunitas'); // (5)
            $table->tinyInteger('jenis_kelamin')->comment('1: Laki-laki, 2: Perempuan'); // (6)
            $table->string('provinsi_tempat_pelatihan'); // (7) Teks nama provinsi
            $table->string('kejuruan'); // (8) Teks nama kejuruan
            $table->tinyInteger('status_kelulusan')->comment('1: Lulus, 2: Tidak Lulus'); // (9)
            $table->integer('jumlah')->default(0)->comment('Jumlah peserta'); // (10) Jumlah
            $table->timestamps();

            // Indexes
            $table->index(['tahun', 'bulan']);
            $table->index('penyelenggara_pelatihan');
            $table->index('tipe_lembaga');
            $table->index('jenis_kelamin');
            $table->index('provinsi_tempat_pelatihan');
            $table->index('kejuruan');
            $table->index('status_kelulusan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jumlah_kepesertaan_pelatihan');
    }
};
