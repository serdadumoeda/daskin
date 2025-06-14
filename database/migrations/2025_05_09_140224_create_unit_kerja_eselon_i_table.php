<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_kerja_eselon_i', function (Blueprint $table) {
            $table->string('kode_uke1')->primary(); // Primary Key sebagai string
            $table->string('nama_unit_kerja_eselon_i');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_kerja_eselon_i');
    }
};