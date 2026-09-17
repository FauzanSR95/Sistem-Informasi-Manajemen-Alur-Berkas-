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
        Schema::create('pengajuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_loket_id')->constrained('users');
            $table->string('nama_pemohon');
            $table->string('nomor_hak');
            $table->string('desa');
            $table->string('kecamatan');
            $table->text('catatan_loket')->nullable();
            $table->string('status')->default('Di Loket');
            $table->foreignId('current_user_id')->nullable()->constrained('users');
            $table->timestamp('tenggat_waktu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};
