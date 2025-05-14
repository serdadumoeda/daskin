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
        Schema::create('data_ketenagakerjaan', function (Blueprint $table) {
            $table->id(); // No (1)
            $table->year('tahun'); // (2) Tahun
            $table->tinyInteger('bulan')->comment('1-12'); // (3) Bulan
            
            $table->decimal('penduduk_15_atas', 15, 3)->nullable()->comment('Penduduk Berumur 15 Tahun Ke Atas (Ribu Jiwa)');
            $table->decimal('angkatan_kerja', 15, 3)->nullable()->comment('Angkatan Kerja (Ribu Jiwa)');
            
            $table->decimal('tpak', 6, 2)->nullable()->comment('Tingkat Partisipasi Angkatan Kerja (%)');
            $table->decimal('bekerja', 15, 3)->nullable()->comment('Bekerja (Ribu Jiwa)');
            $table->decimal('pengangguran_terbuka', 15, 3)->nullable()->comment('Pengangguran Terbuka (Ribu Jiwa)');
            $table->decimal('tpt', 6, 2)->nullable()->comment('Tingkat Pengangguran Terbuka (%)');

            // Kolom BARU / Sesuai Permintaan
            $table->decimal('bukan_angkatan_kerja', 15, 3)->nullable()->comment('Bukan Angkatan Kerja (Ribu Jiwa)');
            $table->decimal('sekolah', 15, 3)->nullable()->comment('Sekolah (Ribu Jiwa, bagian dari Bukan Angkatan Kerja)');
            $table->decimal('mengurus_rumah_tangga', 15, 3)->nullable()->comment('Mengurus Rumah Tangga (Ribu Jiwa, bagian dari Bukan Angkatan Kerja)');
            $table->decimal('lainnya_bak', 15, 3)->nullable()->comment('Lainnya Bukan Angkatan Kerja (Ribu Jiwa)'); 
            $table->decimal('tingkat_kesempatan_kerja', 6, 2)->nullable()->comment('Tingkat Kesempatan Kerja (%)');
            
            $table->timestamps();

            $table->unique(['tahun', 'bulan']); 
            $table->index(['tahun', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_ketenagakerjaan');
    }
};
