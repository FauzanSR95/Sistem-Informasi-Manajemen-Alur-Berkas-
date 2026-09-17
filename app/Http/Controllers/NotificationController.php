<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\RiwayatAlur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Notifikasi pop-up sederhana untuk tugas baru.
     */
    public function check(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['new_task' => false]);
        }

        $newTasksCount = 0;

        $statusMap = [
            'arsip' => 'Di Arsip',
            'seksi1' => 'Di Seksi 1',
            'seksi2' => 'Di Seksi 2',
            'loket' => ['Dikembalikan', 'Selesai (di Loket)'], // Loket dapat notif untuk 2 status
        ];

        if (isset($statusMap[$user->role->value])) {
            $status = $statusMap[$user->role->value];
            if (is_array($status)) {
                $newTasksCount = Pengajuan::whereIn('status', $status)->count();
            } else {
                $newTasksCount = Pengajuan::where('status', $status)->count();
            }
        }

        $lastNotifiedCount = $request->session()->get('notified_tasks_count_v2', -1);

        if ($newTasksCount > 0 && $newTasksCount != $lastNotifiedCount) {
            $request->session()->put('notified_tasks_count_v2', $newTasksCount);
            return response()->json(['new_task' => true]);
        }

        $request->session()->put('notified_tasks_count_v2', $newTasksCount);
        return response()->json(['new_task' => false]);
    }

    /**
     * Notifikasi untuk tugas yang terlambat.
     */
    public function checkOverdue(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['overdue_task' => false]);
        }

        $statusToCheck = '';
        switch ($user->role->value) {
            case 'arsip':
                $statusToCheck = 'Di Arsip';
                break;
            case 'seksi1':
                $statusToCheck = 'Di Seksi 1';
                break;
            case 'seksi2':
                $statusToCheck = 'Di Seksi 2';
                break;
            default:
                // Untuk role lain (admin, loket, monitor), tidak ada notifikasi tugas terlambat personal
                return response()->json(['overdue_task' => false]);
        }

        // Gunakan exists() dengan logika 'kunci waktu' yang sudah benar
        $hasOverdueTask = Pengajuan::where('status', $statusToCheck)
                                  ->whereNotNull('original_tenggat_waktu')
                                  ->where('original_tenggat_waktu', '<', now())
                                  ->exists();

        // Logika untuk memastikan notifikasi hanya muncul sekali per sesi
        $sessionKey = 'notified_overdue_'. $statusToCheck;
        if ($hasOverdueTask && !$request->session()->has($sessionKey)) {
            $request->session()->put($sessionKey, true);
            return response()->json(['overdue_task' => true]);
        } elseif (!$hasOverdueTask) {
            $request->session()->forget($sessionKey);
        }

        return response()->json(['overdue_task' => false]);
    }

    /**
     * Rangkuman Notifikasi Khusus Pimpinan (Admin & Monitor)
     */
    public function adminSummary()
    {
        // Hitung total Berita Acara dengan akurat
        $baCount = Pengajuan::where('status', 'Berita Acara')->count();

        // Hitung Terlambat: Gunakan 'tenggat_waktu' dan kecualikan status akhir & BA
        $terlambatCount = Pengajuan::whereNotIn('status', ['Selesai', 'Selesai (di Loket)', 'Dibatalkan', 'Dikembalikan', 'Berita Acara'])
            ->whereNotNull('tenggat_waktu')
            ->where('tenggat_waktu', '<', now())
            ->count();

        return response()->json([
            'terlambat' => $terlambatCount,
            'ba' => $baCount
        ]);
    }

    /**
     * Mengambil notifikasi lonceng yang belum dibaca.
     */
    public function getUnread(Request $request)
    {
        $user = Auth::user();
        $query = RiwayatAlur::whereNull('read_at')->with('pengajuan');

        if (in_array($user->role->value, ['admin', 'monitor'])) {
            // Admin & Monitor melihat semua notifikasi baru
            $unreadNotifications = $query->where('status_baru', '!=', 'Input')->latest()->take(5)->get();
        } else {
            // Ambil ID pengajuan yang pernah ditangani oleh user ini
            $pengajuanIds = RiwayatAlur::where('user_id', $user->id)
                                        ->pluck('pengajuan_id')
                                        ->unique();

            // Juga tambahkan pengajuan yang mereka buat (khusus untuk Loket)
            if ($user->role->value === 'loket') {
                $pengajuanIds = $pengajuanIds->merge(
                    Pengajuan::where('user_loket_id', $user->id)->pluck('id')
                )->unique();
            }

            // Tampilkan notifikasi jika statusnya relevan ATAU jika terjadi pada berkas yang pernah mereka tangani
            $unreadNotifications = $query->where(function ($q) use ($user, $pengajuanIds) {
                $q->whereIn('pengajuan_id', $pengajuanIds)
                  ->orWhere(function ($q2) use ($user) {
                        switch ($user->role->value) {
                            case 'arsip': $q2->where('status_baru', 'Di Arsip'); break;
                            case 'seksi1': $q2->where('status_baru', 'Di Seksi 1'); break;
                            case 'seksi2': $q2->where('status_baru', 'Di Seksi 2'); break;
                            case 'loket': $q2->where('status_baru', 'Dikembalikan')->orWhere('status_baru', 'Selesai (di Loket)'); break;
                        }
                  });
            })->latest()->take(5)->get();
        }

        return response()->json([
            'count' => $unreadNotifications->count(),
            'notifications' => $unreadNotifications
        ]);
    }

    /**
     * Menandai notifikasi lonceng sebagai sudah dibaca.
     */
    public function markAsRead(Request $request)
    {
        $user = Auth::user();

        if (in_array($user->role->value, ['admin', 'monitor'])) {
            RiwayatAlur::whereNull('read_at')->update(['read_at' => now()]);
        } else {
            // Logika yang sama seperti getUnread untuk memastikan notifikasi yang benar ditandai
            $pengajuanIds = RiwayatAlur::where('user_id', $user->id)
                                        ->pluck('pengajuan_id')
                                        ->unique();

            if ($user->role->value === 'loket') {
                $pengajuanIds = $pengajuanIds->merge(
                    Pengajuan::where('user_loket_id', $user->id)->pluck('id')
                )->unique();
            }

            RiwayatAlur::whereNull('read_at')
                ->where(function ($q) use ($user, $pengajuanIds) {
                    $q->whereIn('pengajuan_id', $pengajuanIds)
                    ->orWhere(function ($q2) use ($user) {
                        switch ($user->role->value) {
                            case 'arsip': $q2->where('status_baru', 'Di Arsip'); break;
                            case 'seksi1': $q2->where('status_baru', 'Di Seksi 1'); break;
                            case 'seksi2': $q2->where('status_baru', 'Di Seksi 2'); break;
                            case 'loket': $q2->where('status_baru', 'Dikembalikan')->orWhere('status_baru', 'Selesai (di Loket)'); break;
                        }
                    });
                })->update(['read_at' => now()]);
        }

        return response()->json(['status' => 'success']);
    }
}
