<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajuan;
use App\Models\RiwayatAlur;
use App\Models\Setting;
use App\Models\BeritaAcara;
use Illuminate\Support\Facades\Auth;

class Seksi2Controller extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Mengambil berkas yang 'Di Seksi 2' ATAU 'Berita Acara' yang dibuat oleh user Seksi 2 ini
        $pengajuans = Pengajuan::with('latestRiwayat.user')
            ->where(function($query) use ($userId) {
                $query->where('status', 'Di Seksi 2')
                      ->orWhere(function ($qBA) use ($userId) {
                          $qBA->where('status', 'Berita Acara')
                              ->whereHas('latestRiwayat', function ($qRiwayat) use ($userId) {
                                  // Memastikan BA terakhir dibuat oleh user ini
                                  $qRiwayat->where('user_id', $userId)
                                           ->where('status_baru', 'Berita Acara');
                              });
                      });
            })
            ->latest()
            ->paginate(10);
            
        return view('seksi2.dashboard', compact('pengajuans'));
    }

// METHOD BARU: Mengirim berkas yang sudah selesai ke Loket
    public function kirimKeLoket(Pengajuan $pengajuan)
    {
        $pengajuan->update([
            'status' => 'Selesai (di Loket)',
            'tenggat_waktu' => null, // Cukup argometernya saja yang dimatikan
            // original_tenggat_waktu TIDAK BOLEH di-null-kan agar terbaca oleh statistik
        ]);

        RiwayatAlur::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'status_lama' => 'Di Seksi 2',
            'status_baru' => 'Selesai (di Loket)',
            'catatan' => 'Proses selesai dan berkas dikirim ke Loket untuk penyerahan.',
        ]);

        return redirect()->route('seksi2.dashboard')->with('success', 'Berkas berhasil dikirim ke Loket.');
    }

    // METHOD BARU: Mengembalikan berkas ke Seksi 1
    public function kembalikanKeSeksi1(Pengajuan $pengajuan)
    {
        $newDeadline = now()->addWeekdays((int) (Setting::where('key', 'deadline_seksi1')->first()->value ?? 3));
        $pengajuan->update([
            'status' => 'Di Seksi 1',
            'tenggat_waktu' => $newDeadline,
            'original_tenggat_waktu' => $newDeadline,
        ]);

        RiwayatAlur::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'status_lama' => 'Di Seksi 2',
            'status_baru' => 'Di Seksi 1',
            'catatan' => 'Berkas dikembalikan dari Seksi 2 ke Seksi 1.',
        ]);

        return redirect()->route('seksi2.dashboard')->with('success', 'Berkas berhasil dikembalikan ke Seksi 1.');
    }

    // METHOD BARU: Mengembalikan berkas ke Arsip
    public function kembalikanKeArsip(Pengajuan $pengajuan)
    {
        $newDeadline = now()->addWeekdays((int) (Setting::where('key', 'deadline_arsip')->first()->value ?? 3));
        $pengajuan->update([
            'status' => 'Di Arsip',
            'tenggat_waktu' => $newDeadline,
            'original_tenggat_waktu' => $newDeadline,
        ]);

        RiwayatAlur::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'status_lama' => 'Di Seksi 2',
            'status_baru' => 'Di Arsip',
            'catatan' => 'Berkas dikembalikan dari Seksi 2 ke Arsip.',
        ]);

        return redirect()->route('seksi2.dashboard')->with('success', 'Berkas berhasil dikembalikan ke Arsip.');
    }

// METHOD BARU: Membuat Berita Acara
    public function buatBeritaAcara(Request $request, Pengajuan $pengajuan)
    {
        $request->validate(['catatan_ba' => 'required|string|max:255']);

        BeritaAcara::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'isi_berita_acara' => $request->catatan_ba
        ]);

        // Cukup argometernya saja yang dimatikan
        $pengajuan->update(['status' => 'Berita Acara', 'tenggat_waktu' => null]);

        RiwayatAlur::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'status_lama' => 'Di Seksi 2',
            'status_baru' => 'Berita Acara',
            'catatan' => $request->catatan_ba
        ]);

        return redirect()->route('seksi2.dashboard')->with('success', 'Berita Acara berhasil dibuat.');
    }

    public function alur()
    {
        $pengajuans = Pengajuan::with('latestRiwayat.user')
            // Hapus 'Selesai (di Loket)' dari pengecualian agar status tersebut ikut tertampil
            ->whereNotIn('status', ['Selesai', 'Dibatalkan', 'Berita Acara'])
            ->latest()
            ->paginate(10);
        return view('seksi2.alur', compact('pengajuans'));
    }
}
