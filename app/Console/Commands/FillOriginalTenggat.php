<?php

namespace App\Console\Commands;

use App\Models\Pengajuan;
use Illuminate\Console\Command;

class FillOriginalTenggat extends Command
{
    protected $signature = 'app:fill-original-tenggat';
    protected $description = 'Mengisi kolom original_tenggat_waktu yang kosong pada data lama';

    public function handle()
    {
        $this->info('Memulai proses pengisian data tenggat waktu original...');

        // Ambil semua pengajuan di mana original_tenggat_waktu masih NULL
        $pengajuans = Pengajuan::whereNull('original_tenggat_waktu')->get();

        if ($pengajuans->isEmpty()) {
            $this->info('Tidak ada data lama yang perlu diperbarui. Semua sudah terisi.');
            return;
        }

        $bar = $this->output->createProgressBar(count($pengajuans));
        $bar->start();

        foreach ($pengajuans as $pengajuan) {
            // Salin nilai dari tenggat_waktu ke original_tenggat_waktu
            $pengajuan->original_tenggat_waktu = $pengajuan->tenggat_waktu;
            $pengajuan->save();
            $bar->advance();
        }

        $bar->finish();
        $this->info("\nBerhasil memperbarui {$pengajuans->count()} data pengajuan lama.");
    }
}
