<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\RiwayatAlur;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    public function index()
    {
        // --- DATA 0: KARTU RINGKASAN (SCORECARDS) ---
        $totalMasuk = Pengajuan::count();
        
        // PERBAIKAN: Masukkan 'Selesai (di Loket)' agar hitungan Selesai akurat
        $totalSelesai = Pengajuan::whereIn('status', ['Selesai', 'Selesai (di Loket)'])->count();
        
        $totalBerjalan = Pengajuan::whereNotIn('status', ['Selesai', 'Selesai (di Loket)', 'Dibatalkan'])->count();
        
        $totalTerlambat = Pengajuan::whereNotIn('status', ['Selesai', 'Selesai (di Loket)', 'Dibatalkan'])
                            ->whereNotNull('tenggat_waktu')
                            ->where('tenggat_waktu', '<', now())
                            ->count();

        // PENAMBAHAN BARU: Menghitung total Berita Acara
        $totalBeritaAcara = Pengajuan::where('status', 'Berita Acara')->count();

        // --- DATA 1: Berkas Masuk vs Selesai per Bulan ---
        $completedData = Pengajuan::where('status', 'Selesai')
            ->select(DB::raw('YEAR(updated_at) as year, MONTH(updated_at) as month, COUNT(*) as count'))
            ->groupBy('year', 'month')->orderBy('year', 'asc')->orderBy('month', 'asc')->get();

        $incomingData = Pengajuan::select(DB::raw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count'))
            ->groupBy('year', 'month')->orderBy('year', 'asc')->orderBy('month', 'asc')->get();

        // Buat label gabungan dari bulan masuk & selesai
        $allMonths = $completedData->pluck('month')->merge($incomingData->pluck('month'))->unique()->sort();
        $labelsCompleted = $allMonths->map(fn($month) => Carbon::createFromDate(now()->year, $month)->format('M Y'))->values();
        
        $valuesCompleted = [];
        $valuesIncoming = [];
        foreach ($allMonths as $month) {
            $valuesCompleted[] = $completedData->where('month', $month)->first()->count ?? 0;
            $valuesIncoming[] = $incomingData->where('month', $month)->first()->count ?? 0;
        }

        // --- DATA 2: Rata-rata Waktu Pengerjaan per Seksi (Logika Dinamis) ---
        $durations = ['Di Arsip' => [], 'Di Seksi 1' => [], 'Di Seksi 2' => []];
        
        // Tarik semua berkas yang sudah Selesai (termasuk yang selesai langsung di Loket)
        $completedPengajuans = Pengajuan::whereIn('status', ['Selesai', 'Selesai (di Loket)'])
            ->with(['riwayat' => function($query) {
                // Pastikan riwayat diurutkan dari yang paling lama ke terbaru
                $query->orderBy('created_at', 'asc');
            }])->get();

        foreach ($completedPengajuans as $pengajuan) {
            $riwayatList = $pengajuan->riwayat;

            for ($i = 0; $i < count($riwayatList) - 1; $i++) {
                $currentStatus = $riwayatList[$i]->status_baru;

                // Jika status saat ini adalah salah satu seksi yang ingin kita pantau
                if (array_key_exists($currentStatus, $durations)) {
                    $startTime = Carbon::parse($riwayatList[$i]->created_at);
                    
                    // Waktu berhentinya adalah riwayat APA PUN yang tercatat setelahnya 
                    // (bisa dikirim ke seksi selanjutnya, dikembalikan, atau selesai)
                    $endTime = Carbon::parse($riwayatList[$i + 1]->created_at);

                    // Menggunakan diffInMinutes lalu dibagi 60 agar mendapatkan angka desimal (Contoh: 30 menit = 0.5 jam)
                    // Ini mencegah hasil 0 bulat jika pengerjaan sangat cepat saat testing
                    $hours = $startTime->diffInMinutes($endTime) / 60;
                    $durations[$currentStatus][] = $hours;
                }
            }
        }

        $avgLabels = ['Arsip', 'Seksi 1', 'Seksi 2'];
        $avgValues = [
            count($durations['Di Arsip']) > 0 ? array_sum($durations['Di Arsip']) / count($durations['Di Arsip']) : 0,
            count($durations['Di Seksi 1']) > 0 ? array_sum($durations['Di Seksi 1']) / count($durations['Di Seksi 1']) : 0,
            count($durations['Di Seksi 2']) > 0 ? array_sum($durations['Di Seksi 2']) / count($durations['Di Seksi 2']) : 0,
        ];

        // Definisikan warna untuk setiap seksi sesuai tema
        $avgColors = [
            'rgba(34, 197, 94, 0.6)',  // Hijau untuk Arsip
            'rgba(234, 179, 8, 0.6)',   // Kuning untuk Seksi 1
            'rgba(249, 115, 22, 0.6)',  // Oranye untuk Seksi 2
        ];

        $avgBorderColors = [
            'rgba(34, 197, 94, 1)',
            'rgba(234, 179, 8, 1)',
            'rgba(249, 115, 22, 1)',
        ];

        // --- DATA 3: Kinerja Pegawai (Tepat Waktu vs Terlambat) ---
        $employeePerformanceData = [];
        
        // Hanya ambil role yang memiliki batas waktu (SOP)
        // Menggunakan orderByRaw FIELD untuk memaksa urutan selalu Arsip -> Seksi 1 -> Seksi 2
        $employees = User::whereIn('role', ['arsip', 'seksi1', 'seksi2'])
                 ->orderByRaw("FIELD(role, 'arsip', 'seksi1', 'seksi2')")
                 ->get();

        foreach ($employees as $employee) {
            $onTimeTasks = 0;
            $lateTasks = 0;

            // 1. HITUNG TUGAS MASA LALU (Tepat Waktu)
            // Semua tugas yang sudah berhasil diteruskan oleh pegawai ini ke tahap selanjutnya.
            $completedTasks = RiwayatAlur::where('user_id', $employee->id)
                ->whereNotIn('status_baru', ['Dikembalikan']) // Hanya hitung yang maju
                ->count();
            
            $onTimeTasks += $completedTasks;

            // 2. HITUNG TUGAS SAAT INI (Tanggung Jawab yang sedang berjalan)
            // Tentukan status berkas apa yang menjadi tanggung jawab role ini
            $responsibilityStatus = '';
            if ($employee->role->value === 'arsip') $responsibilityStatus = 'Di Arsip';
            if ($employee->role->value === 'seksi1') $responsibilityStatus = 'Di Seksi 1';
            if ($employee->role->value === 'seksi2') $responsibilityStatus = 'Di Seksi 2';

            if ($responsibilityStatus) {
                // Tarik semua berkas yang SAAT INI sedang berada di seksi mereka
                $currentTasks = Pengajuan::where('status', $responsibilityStatus)->get();

                foreach ($currentTasks as $task) {
                    if ($task->original_tenggat_waktu && now()->isAfter($task->original_tenggat_waktu)) {
                        // Jika saat ini berkas tersebut sudah lewat tenggat, tambahkan ke Terlambat
                        $lateTasks++;
                    } else {
                        // Jika masih aman, hitung sebagai Tepat Waktu (Masih dalam proses)
                        $onTimeTasks++;
                    }
                }
            }

            $totalTasks = $onTimeTasks + $lateTasks;
            
            // PENAMBAHAN BARU: Hitung berkas yang SEDANG berstatus Berita Acara oleh pegawai ini
            $baTasks = Pengajuan::where('status', 'Berita Acara')
                ->whereHas('latestRiwayat', function ($q) use ($employee) {
                    $q->where('user_id', $employee->id)
                      ->where('status_baru', 'Berita Acara');
                })->count();

            // Hitung skor persentase
            $score = ($totalTasks > 0) ? ($onTimeTasks / $totalTasks) * 100 : 0;

            // Tentukan warna berdasarkan skor
            if ($totalTasks == 0) {
                $color = 'rgba(156, 163, 175, 0.6)'; // Warna Abu-abu jika belum ada tugas
            } elseif ($score >= 75) {
                $color = 'rgba(34, 197, 94, 0.6)'; // Hijau (Bagus)
            } elseif ($score >= 50) {
                $color = 'rgba(234, 179, 8, 0.6)'; // Kuning (Sedang)
            } else {
                $color = 'rgba(239, 68, 68, 0.6)'; // Merah (Buruk)
            }

            // Simpan data untuk dikirim ke view
            $employeePerformanceData[] = [
                'name' => $employee->name . ' (' . ucfirst($employee->role->value) . ')',
                'total' => $totalTasks,  // KOLOM BARU
                'ba' => $baTasks,        // KOLOM BARU
                'on_time' => $onTimeTasks,
                'late' => $lateTasks,
                'score' => round($score, 2),  
                'color' => $color             
            ];
        }

        // Kirim semua data ke view
        return view('admin.statistics.index', compact(
            'totalMasuk', 'totalSelesai', 'totalBerjalan', 'totalTerlambat', 'totalBeritaAcara',
            'labelsCompleted', 'valuesCompleted', 'valuesIncoming',
            'avgLabels', 'avgValues', 'avgColors', 'avgBorderColors',
            'employeePerformanceData'
        ));
    }
}