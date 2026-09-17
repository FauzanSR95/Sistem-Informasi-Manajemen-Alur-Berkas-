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
        Schema::create('arsip_raks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_hm');
            
            // UPDATE: Mengubah tipe enum menjadi string agar bisa menampung "SU - BT"
            // dan lebih fleksibel jika ada penambahan jenis sertifikat di masa depan
            $table->string('jenis_sertifikat'); 
            
            $table->string('desa');
            $table->string('kecamatan');
            $table->string('lokasi_rak');
            $table->string('lokasi_baris');
            $table->foreignId('user_arsip_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arsip_raks');
    }
};