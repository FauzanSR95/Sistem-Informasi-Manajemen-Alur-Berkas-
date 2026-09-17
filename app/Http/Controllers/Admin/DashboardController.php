<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\RiwayatAlur;
use Illuminate\Http\Request;
use App\Exports\PengajuanExport;
use App\Exports\RiwayatLengkapExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Dashboard utama admin
    public function index()
    {
        $pengajuans = Pengajuan::with(['userLoket', 'latestRiwayat.user'])->latest()->paginate(10);
        return view('admin.dashboard', compact('pengajuans'));
    }

    // Melihat detail satu pengajuan
    public function show(Pengajuan $pengajuan)
    {
        // Eager load relasi riwayat dan user yang terkait
        $pengajuan->load('riwayat.user');
        return view('admin.show', compact('pengajuan'));
    }

    public function export(Request $request)
    {
        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun');

        $fileName = 'laporan-rekap';
        if ($bulan) {
            $bulanInt = (int)$bulan;
            if ($bulanInt >= 1 && $bulanInt <= 12) { // Validasi sederhana
                $namaBulan = Carbon::create()->month($bulanInt)->translatedFormat('F');
                $fileName .= '-' . strtolower($namaBulan);
            }
        }
        if ($tahun) {
            $fileName .= '-' . $tahun; // Tahun sebagai string 
        }
        $fileName .= '.xlsx';

        // Filter yang dikirim ke Export Class sudah diperbaiki sebelumnya
        return Excel::download(new PengajuanExport($bulan, $tahun), $fileName);
    }

    public function updateDeadline(Request $request, Pengajuan $pengajuan)
    {
        $request->validate([
            'tenggat_waktu' => 'required|date',
        ]);

        $statusLama = $pengajuan->status;

        // Update deadline di database
        $pengajuan->update([
            'tenggat_waktu' => $request->tenggat_waktu,
        ]);

        // Catat perubahan di riwayat alur untuk audit
        RiwayatAlur::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => auth()->id(),
            'status_lama' => $statusLama,
            'status_baru' => $statusLama, // Status tidak berubah, hanya catatan
            'catatan' => 'Admin mengubah batas waktu menjadi: ' . \Carbon\Carbon::parse($request->tenggat_waktu)->format('d-m-Y H:i'),
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Batas waktu untuk pengajuan #' . $pengajuan->id . ' berhasil diubah.');
    }

    public function addTimeToDeadline(Request $request, Pengajuan $pengajuan)
    {
        $request->validate(['duration' => 'required|integer']);

        // Ambil tenggat waktu saat ini, atau jika tidak ada, ambil waktu sekarang
        $currentDeadline = \Carbon\Carbon::parse($pengajuan->tenggat_waktu ?? now());

        // PERBAIKAN: Gunakan addWeekdays agar penambahan waktu manual melompati Sabtu/Minggu
        $newDeadline = $currentDeadline->addWeekdays((int) $request->duration);

        $pengajuan->update(['tenggat_waktu' => $newDeadline]);

        // Catat di riwayat
        $catatan = $request->duration > 0
            ? 'Admin menambah batas waktu sebanyak ' . $request->duration . ' hari (Hari Kerja).'
            : 'Admin mengurangi batas waktu sebanyak ' . abs($request->duration) . ' hari (Hari Kerja).';

        RiwayatAlur::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => auth()->id(),
            'status_lama' => $pengajuan->status,
            'status_baru' => $pengajuan->status,
            'catatan' => $catatan . ' Batas waktu baru: ' . $newDeadline->format('d-m-Y H:i'),
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Batas waktu berhasil diubah.');
    }

    public function exportLengkap()
    {
        return Excel::download(new RiwayatLengkapExport, 'laporan-riwayat-lengkap-bpn.xlsx');
    }
}