<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajuan;
use App\Models\RiwayatAlur;
use App\Models\BeritaAcara;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class Seksi1Controller extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Mengambil berkas yang 'Di Seksi 1' ATAU 'Berita Acara' yang dibuat oleh user Seksi 1 ini
        $pengajuans = Pengajuan::with('latestRiwayat.user')
            ->where(function($query) use ($userId) {
                $query->where('status', 'Di Seksi 1')
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

        return view('seksi1.dashboard', compact('pengajuans'));
    }

    public function kirimKeSeksi2(Pengajuan $pengajuan)
    {
        // Update pengajuan untuk dikirim ke Seksi 2
        $newDeadline = now()->addWeekdays((int) (Setting::where('key', 'deadline_seksi2')->first()->value ?? 3));
        $pengajuan->update([
            'status' => 'Di Seksi 2',
            'user_seksi1_id' => Auth::id(),
            'tenggat_waktu' => $newDeadline,
            'original_tenggat_waktu' => $newDeadline, // <-- Pastikan ini DI-RESET
        ]);

        RiwayatAlur::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'status_lama' => 'Di Seksi 1',
            'status_baru' => 'Di Seksi 2',
            'catatan' => 'Berkas diteruskan oleh Seksi 1 ke Seksi 2.',
        ]);

        return redirect()->route('seksi1.dashboard')->with('success', 'Berkas berhasil dikirim ke Seksi 2.');
    }

    public function kembalikanKeLoket(Pengajuan $pengajuan)
    {
        // Mengubah fungsi dari "Dikembalikan/Revisi" menjadi "Selesai & Teruskan"
        $alasan = "Proses di Seksi 1 telah selesai. Berkas diteruskan ke Loket untuk penyerahan.";

        $pengajuan->update([
            'status' => 'Selesai (di Loket)', // Berubah dari 'Dikembalikan'
            'tenggat_waktu' => null,          // Argometer waktu dihentikan karena sudah selesai
            // checkbox spasial dan tekstual TIDAK DI-RESET ke false karena ini bukan penolakan
        ]);

        RiwayatAlur::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'status_lama' => 'Di Seksi 1',
            'status_baru' => 'Selesai (di Loket)', // Berubah dari 'Dikembalikan'
            'catatan' => $alasan,
        ]);

        return redirect()->route('seksi1.dashboard')->with('success', 'Berkas telah diselesaikan dan diteruskan ke Loket.');
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
            'status_lama' => 'Di Seksi 1',
            'status_baru' => 'Berita Acara',
            'catatan' => $request->catatan_ba
        ]);

        return redirect()->route('seksi1.dashboard')->with('success', 'Berita Acara berhasil dibuat.');
    }

    public function updateCheck(Request $request, Pengajuan $pengajuan)
    {
        $request->validate([
            'field' => 'required|string|in:tekstual_checked,spasial_checked',
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

        return view('seksi1.alur', compact('pengajuans'));
    }

    public function kembalikanKeArsip(Pengajuan $pengajuan)
    {
        $alasan = "Ada kelengkapan dokumen yang perlu diperiksa ulang oleh Arsip.";

        $pengajuan->update([
            'status' => 'Di Arsip',
            'tenggat_waktu' => now()->addWeekdays((int) (Setting::where('key', 'deadline_arsip')->first()->value ?? 3)),
            'tekstual_checked' => false,
            'spasial_checked' => false,
        ]);

        RiwayatAlur::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'status_lama' => 'Di Seksi 1',
            'status_baru' => 'Di Arsip',
            'catatan' => $alasan,
        ]);

        return redirect()->route('seksi1.dashboard')->with('success', 'Berkas berhasil dikembalikan ke Arsip.');
    }

    public function addTimeToDeadline(Request $request, Pengajuan $pengajuan)
    {
        // Pastikan hanya bisa mengubah waktu untuk tugas di seksi 1
        if ($pengajuan->status !== 'Di Seksi 1') {
            return redirect()->route('seksi1.dashboard')->with('error', 'Tidak dapat mengubah waktu untuk berkas ini.');
        }

        $request->validate([
            'duration' => 'required|integer',
            'catatan' => 'required|string|max:255',
        ]);

        $currentDeadline = \Carbon\Carbon::parse($pengajuan->tenggat_waktu ?? now());
        $newDeadline = $currentDeadline->addWeekdays((int) $request->duration);

        $pengajuan->update(['tenggat_waktu' => $newDeadline]);

        // Catat di riwayat dengan alasan yang diinput
        $catatan = 'Seksi 1 mengubah batas waktu. Alasan: ' . $request->catatan;

        RiwayatAlur::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => auth()->id(),
            'status_lama' => $pengajuan->status,
            'status_baru' => $pengajuan->status, // Status tidak berubah
            'catatan' => $catatan,
        ]);

        return redirect()->route('seksi1.dashboard')->with('success', 'Batas waktu berhasil diubah.');
    }
}
