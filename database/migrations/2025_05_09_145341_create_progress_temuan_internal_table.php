<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress_temuan_internal', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan')->comment('1-12 mewakili Januari-Desember');
            
            $table->string('kode_unit_kerja_eselon_i');
            $table->string('kode_satuan_kerja');
            
            $table->integer('temuan_administratif_kasus')->default(0);
            $table->decimal('temuan_kerugian_negara_rp', 19, 2)->default(0.00);
            
            $table->integer('tindak_lanjut_administratif_kasus')->default(0);
            $table->decimal('tindak_lanjut_kerugian_negara_rp', 19, 2)->default(0.00);
            
            $table->float('persentase_tindak_lanjut_administratif')->default(0.0);
            $table->float('persentase_tindak_lanjut_kerugian_negara')->default(0.0);
            
            $table->timestamps();

            $table->foreign('kode_unit_kerja_eselon_i')
                  ->references('kode_uke1')
                  ->on('unit_kerja_eselon_i')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('kode_satuan_kerja')
                  ->references('kode_sk')
                  ->on('satuan_kerja')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->index(['tahun', 'bulan']);
            $table->index('kode_unit_kerja_eselon_i');
            $table->index('kode_satuan_kerja');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_temuan_internal');
    }
};