<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\RiwayatAlur;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LoketController extends Controller
{
    /**
     * Menampilkan halaman utama loket (form input).
     */
    public function index()
    {
        return view('loket.dashboard');
    }

    /**
     * Menyimpan pengajuan baru dari form.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pemohon' => 'required|string|max:255',
            'nomor_hak' => 'required|string|max:255',
            'desa' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
        ]);

        // Menggunakan addWeekdays untuk melompati Sabtu & Minggu
        $tenggat = now()->addWeekdays((int) (Setting::where('key', 'deadline_arsip')->first()->value ?? 3));

        // Langkah 1: Buat pengajuan terlebih dahulu untuk mendapatkan ID
        $pengajuan = Pengajuan::create([
            'user_loket_id' => Auth::id(),
            'nama_pemohon' => $request->nama_pemohon,
            'nomor_hak' => $request->nomor_hak,
            'desa' => $request->desa,
            'kecamatan' => $request->kecamatan,
            'catatan_loket' => $request->catatan_loket,
            'status' => 'Di Arsip',
            'tenggat_waktu' => $tenggat,
            'original_tenggat_waktu' => $tenggat,
        ]);

        // Langkah 2: Buat Nomor Berkas unik berdasarkan ID dan Tahun
        $tahun = now()->year;
        // Format: 4/BPN-RH/2025 (ID/Kode Kantor/Tahun)
        $nomorBerkas = $pengajuan->id . '/BPN-RH/' . $tahun;

        // Langkah 3: Update pengajuan dengan Nomor Berkas yang baru dibuat
        $pengajuan->nomor_berkas = $nomorBerkas;
        $pengajuan->save();

        RiwayatAlur::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'status_lama' => 'Input',
            'status_baru' => 'Di Arsip',
            'catatan' => 'Berkas baru dibuat dan dikirim ke bagian Arsip.',
        ]);

        return redirect()->route('loket.dashboard')->with('success', 'Pengajuan berhasil dibuat dan dikirim ke Arsip.');
    }

    public function cetak(Pengajuan $pengajuan)
    {
        // URL yang akan dibuka saat pemohon scan QR
        $urlLacak = route('public.search.auto', [
            'nomor_berkas' => $pengajuan->nomor_berkas,
            'nomor_hak' => $pengajuan->nomor_hak
        ]);

        // Membuat gambar QR Code dalam format SVG
        $qrcode = QrCode::size(120)->generate($urlLacak);

        return view('loket.cetak_tanda_terima', compact('pengajuan', 'qrcode'));
    }

    public function alur(Request $request) // Tambahkan parameter Request $request di sini
    {
        $search = $request->input('search');

        // Logic pencarian: mencari di Nama, No. Berkas, atau No. Hak
        $queryBase = function($query) use ($search) {
            return $query->with('latestRiwayat.user')
                ->when($search, function($q) use ($search) {
                    $q->where(function($sub) use ($search) {
                        $sub->where('nama_pemohon', 'like', "%{$search}%")
                            ->orWhere('nomor_berkas', 'like', "%{$search}%")
                            ->orWhere('nomor_hak', 'like', "%{$search}%");
                    });
                });
        };

        // Query 1: Berkas yang sedang berjalan
        $pengajuansBerjalan = $queryBase(Pengajuan::whereHas('userLoket', function ($query) {
                $query->where('role', \App\Enums\Role::LOKET);
            })
            ->whereNotIn('status', ['Selesai', 'Dibatalkan', 'Berita Acara', 'Dikembalikan', 'Selesai (di Loket)']))
            ->latest()->paginate(10, ['*'], 'berjalan');

        // Query 2: Berkas yang dikembalikan
        $pengajuansDikembalikan = $queryBase(Pengajuan::where('status', 'Dikembalikan'))
            ->latest()->paginate(10, ['*'], 'dikembalikan');

        // Query 3: Berkas yang sudah selesai (di Loket)
        $pengajuansSelesai = $queryBase(Pengajuan::where('status', 'Selesai (di Loket)'))
            ->latest()->paginate(10, ['*'], 'selesai');

        // Query 4: Riwayat Berkas Selesai (Arsip)
        $pengajuansDiarsipkan = $queryBase(Pengajuan::where('status', 'Selesai'))
            ->latest()->paginate(10, ['*'], 'diarsipkan');

        // Kirim semua data ke view
        return view('loket.alur', compact('pengajuansBerjalan', 'pengajuansDikembalikan', 'pengajuansSelesai', 'pengajuansDiarsipkan'));
    }

    /**
     * Melakukan finalisasi berkas yang sudah selesai.
     */
    public function finalisasiSelesai(Pengajuan $pengajuan)
    {
        // == PERBAIKAN: Pengecekan user_loket_id dihapus ==
        if ($pengajuan->status === 'Selesai (di Loket)') {
            // Ubah status menjadi 'Selesai' (status final)
            $pengajuan->update(['status' => 'Selesai']);

            RiwayatAlur::create([
                'pengajuan_id' => $pengajuan->id,
                'user_id' => Auth::id(),
                'status_lama' => 'Selesai (di Loket)',
                'status_baru' => 'Selesai',
                'catatan' => 'Berkas telah ditutup dan diarsipkan oleh Loket.',
            ]);
            return redirect()->route('loket.alur')->with('success', 'Proses berkas telah berhasil ditutup.');
        }
        return redirect()->route('loket.alur')->with('error', 'Gagal memfinalisasi berkas.');
    }

    /**
     * Membatalkan pengajuan yang baru dikirim ke arsip.
     */
    public function batalkan(Pengajuan $pengajuan)
    {
        // == PERBAIKAN: Pengecekan user_loket_id dihapus ==
        if ($pengajuan->status === 'Di Arsip') {
            $pengajuan->update(['status' => 'Dibatalkan', 'tenggat_waktu' => null]);

            RiwayatAlur::create([
                'pengajuan_id' => $pengajuan->id,
                'user_id' => Auth::id(),
                'status_lama' => 'Di Arsip',
                'status_baru' => 'Dibatalkan',
                'catatan' => 'Pengajuan dibatalkan oleh Loket.',
            ]);
            return redirect()->route('loket.alur')->with('success', 'Pengajuan berhasil dibatalkan.');
        }
        return redirect()->route('loket.alur')->with('error', 'Gagal membatalkan, berkas sudah diproses.');
    }

    /**
     * Menyelesaikan proses untuk berkas yang dikembalikan.
     */
    public function selesaikanPengembalian(Pengajuan $pengajuan)
    {
         // == PERBAIKAN: Pengecekan user_loket_id dihapus ==
        if ($pengajuan->status === 'Dikembalikan') {
            $pengajuan->update(['status' => 'Selesai']);

            RiwayatAlur::create([
                'pengajuan_id' => $pengajuan->id,
                'user_id' => Auth::id(),
                'status_lama' => 'Dikembalikan',
                'status_baru' => 'Selesai',
                'catatan' => 'Proses diselesaikan oleh Loket setelah pengembalian.',
            ]);
            return redirect()->route('loket.alur')->with('success', 'Berkas berhasil diselesaikan.');
        }
        return redirect()->route('loket.alur')->with('error', 'Status berkas tidak valid.');
    }

    /**
     * Menampilkan halaman edit untuk berkas yang sedang Di Arsip atau Dikembalikan.
     */
    public function edit(Pengajuan $pengajuan)
    {
        // Pastikan hanya berkas yang masih di Arsip atau Dikembalikan yang bisa diedit
        if (!in_array($pengajuan->status, ['Di Arsip', 'Dikembalikan'])) {
            return redirect()->route('loket.alur')->with('error', 'Berkas ini tidak dapat diedit saat ini.');
        }

        return view('loket.edit', compact('pengajuan'));
    }

    /**
     * Menyimpan perubahan data dan mengirimkannya kembali ke Arsip.
     */
    public function update(Request $request, Pengajuan $pengajuan)
    {
        $request->validate([
            'nama_pemohon' => 'required|string|max:255',
            'nomor_hak' => 'required|string|max:255',
            'desa' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
        ]);

        $statusLama = $pengajuan->status;

        // Menggunakan addWeekdays untuk melompati Sabtu & Minggu
        $tenggatBaru = now()->addWeekdays((int) (Setting::where('key', 'deadline_arsip')->first()->value ?? 3));

        // Update data pengajuan, ubah status ke Arsip, DAN berikan waktu tenggat baru
        $pengajuan->update([
            'nama_pemohon' => $request->nama_pemohon,
            'nomor_hak' => $request->nomor_hak,
            'desa' => $request->desa,
            'kecamatan' => $request->kecamatan,
            'catatan_loket' => $request->catatan_loket,
            'status' => 'Di Arsip', 
            'tenggat_waktu' => $tenggatBaru,          // Argometer waktu dihidupkan lagi
            'original_tenggat_waktu' => $tenggatBaru, // Catat waktu mulainya
        ]);

        // Catat riwayat perubahan
        RiwayatAlur::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'status_lama' => $statusLama,
            'status_baru' => 'Di Arsip',
            'catatan' => 'Data berkas telah diperbaiki oleh Loket dan dikirim ulang ke Arsip.',
        ]);

        return redirect()->route('loket.alur')->with('success', 'Data pengajuan berhasil diperbaiki dan dikirim ke Arsip.');
    }
}