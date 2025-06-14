<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('satuan_kerja', function (Blueprint $table) {
            $table->string('kode_sk')->primary(); // Primary Key sebagai string
            $table->string('kode_unit_kerja_eselon_i'); // Foreign Key (string)
            $table->string('nama_satuan_kerja');
            $table->timestamps();

            $table->foreign('kode_unit_kerja_eselon_i')
                  ->references('kode_uke1') // Merujuk ke kode_uke1 di tabel unit_kerja_eselon_i
                  ->on('unit_kerja_eselon_i')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('satuan_kerja');
    }
};