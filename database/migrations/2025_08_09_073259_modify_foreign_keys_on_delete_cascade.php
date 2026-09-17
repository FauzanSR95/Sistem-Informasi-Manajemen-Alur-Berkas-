<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Aturan untuk tabel 'arsip_raks'
        Schema::table('arsip_raks', function (Blueprint $table) {
            $table->dropForeign(['user_arsip_id']);
            $table->foreign('user_arsip_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 2. Aturan untuk tabel 'berita_acaras'
        Schema::table('berita_acaras', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 3. Aturan untuk tabel 'pengajuans'
        Schema::table('pengajuans', function (Blueprint $table) {
            $table->dropForeign(['user_loket_id']);
            // Ubah kolom agar bisa NULL sebelum menambahkan aturan baru
            $table->unsignedBigInteger('user_loket_id')->nullable()->change();
            $table->foreign('user_loket_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('riwayat_alurs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            // Ubah kolom agar bisa NULL sebelum menambahkan aturan baru
            $table->foreignId('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        //
    }
};
