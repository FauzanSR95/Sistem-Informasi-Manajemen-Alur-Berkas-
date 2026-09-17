<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pengajuan;
use App\Models\RiwayatAlur;
use Carbon\Carbon;

class UpdateOverdueStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-overdue-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek semua pengajuan yang aktif dan ubah statusnya menjadi Terlambat jika melewati tenggat waktu ASLI';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mengecek pengajuan yang terlambat berdasarkan KUNCI WAKTU...');

        // Mengambil pengajuan yang statusnya aktif
        // sudah melewati 'original_tenggat_waktu' (kunci waktu)
        $overduePengajuans = Pengajuan::whereIn('status', ['Di Arsip', 'Di Seksi 1', 'Di Seksi 2'])
                                      ->whereNotNull('original_tenggat_waktu')
                                      ->where('original_tenggat_waktu', '<', Carbon::now())
                                      ->get();

        if ($overduePengajuans->isEmpty()) {
            $this->info('Tidak ada pengajuan yang terlambat ditemukan.');
            return;
        }

        foreach ($overduePengajuans as $pengajuan) {
            $this->warn("Pengajuan #{$pengajuan->id} terdeteksi melewati batas waktu awal.");
        }

        $this->info('Pengecekan selesai.');
    }
}
