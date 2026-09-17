<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajuan;
use App\Models\Setting;
use App\Models\RiwayatAlur;
use App\Models\BeritaAcara;
use Illuminate\Support\Facades\Auth;

class ArsipController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Mengambil berkas yang 'Di Arsip' ATAU 'Berita Acara' yang dibuat oleh user Arsip ini
        $pengajuans = Pengajuan::with('latestRiwayat.user')
            ->where(function($query) use ($userId) {
                $query->where('status', 'Di Arsip')
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

        return view('arsip.dashboard', compact('pengajuans'));
    }

    public function kirimKeSeksi1(Pengajuan $pengajuan)
    {
        // Update pengajuan untuk dikirim ke Seksi 1
        // Ubah baris ini:
        $newDeadline = now()->addWeekdays((int) (Setting::where('key', 'deadline_seksi2')->first()->value ?? 3));
        $pengajuan->update([
            'status' => 'Di Seksi 1',
            'user_arsip_id' => Auth::id(),
            'tenggat_waktu' => $newDeadline,
            'original_tenggat_waktu' => $newDeadline,
        ]);

        RiwayatAlur::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'status_lama' => 'Di Arsip',
            'status_baru' => 'Di Seksi 1',
            'catatan' => 'Berkas diteruskan oleh Arsip ke Seksi 1.',
        ]);

        return redirect()->route('arsip.dashboard')->with('success', 'Berkas berhasil dikirim ke Seksi 1.');
    }

    public function buatBeritaAcara(Request $request, Pengajuan $pengajuan)
    {
        $request->validate(['catatan_ba' => 'required|string|max:255']);

        BeritaAcara::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'isi_berita_acara' => $request->catatan_ba
        ]);

        $pengajuan->update(['status' => 'Berita Acara', 'tenggat_waktu' => null]);

        RiwayatAlur::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'status_lama' => 'Di Arsip',
            'status_baru' => 'Berita Acara',
            'catatan' => $request->catatan_ba
        ]);

        return redirect()->route('arsip.dashboard')->with('success', 'Berita Acara berhasil dibuat.');
    }

    public function updateCheck(Request $request, Pengajuan $pengajuan)
    {
        $request->validate([
            'field' => 'required|string|in:bt_checked,su_checked,ht_checked',
            'value' => 'required|boolean',
        ]);

        $pengajuan->update([
            $request->field => $request->value,
        ]);

        return response()->json(['status' => 'success']);
    }

    public function alur()
    {
        $pengajuans = Pengajuan::with('latestRiwayat.user')
            ->whereNotIn('status', ['Selesai', 'Dibatalkan', 'Berita Acara'])
            ->latest()
            ->paginate(10);

        return view('arsip.alur', compact('pengajuans'));
    }

    public function kembalikanKeLoket(Pengajuan $pengajuan)
    {
        $alasan = "Ada data pemohon atau nomor hak yang perlu diperbaiki oleh Loket.";

        $pengajuan->update([
            'status' => 'Dikembalikan',
            'tenggat_waktu' => null,
            'bt_checked' => false,
            'su_checked' => false,
            'ht_checked' => false,
        ]);

        RiwayatAlur::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'status_lama' => 'Di Arsip',
            'status_baru' => 'Dikembalikan',
            'catatan' => $alasan,
        ]);

        return redirect()->route('arsip.dashboard')->with('success', 'Berkas berhasil dikembalikan ke Loket.');
    }

    public function kirimKeSeksi2(Request $request, Pengajuan $pengajuan)
    {
        $request->validate(['catatan' => 'required|string|max:255']);

        // Update pengajuan untuk dikirim langsung ke Seksi 2
        $newDeadline = now()->addDays((int) (Setting::where('key', 'deadline_seksi2')->first()->value ?? 3));
        $pengajuan->update([
            'status' => 'Di Seksi 2',
            'user_arsip_id' => Auth::id(),
            'tenggat_waktu' => $newDeadline,
            'original_tenggat_waktu' => $newDeadline,
        ]);

        RiwayatAlur::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'status_lama' => 'Di Arsip',
            'status_baru' => 'Di Seksi 2',
            'catatan' => 'Diteruskan langsung dari Arsip. Catatan: ' . $request->catatan,
        ]);

        return redirect()->route('arsip.dashboard')->with('success', 'Berkas berhasil dikirim langsung ke Seksi 2.');
    }
}
