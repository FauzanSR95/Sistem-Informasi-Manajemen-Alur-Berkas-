<?php

namespace App\Http\Controllers\Monitor;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama Monitor dengan semua pengajuan.
     */
    public function index()
    {
        // PERBAIKAN: Tambahkan with(['userLoket', 'latestRiwayat.user']) agar sama dengan Admin
        $pengajuans = Pengajuan::with(['userLoket', 'latestRiwayat.user'])->latest()->paginate(10);

        // Menggunakan view yang sama dengan admin
        return view('admin.dashboard', compact('pengajuans'));
    }

    /**
     * Menampilkan detail dan riwayat satu pengajuan.
     */
    public function show(Pengajuan $pengajuan)
    {
        // Eager load relasi riwayat dan user yang terkait di dalam riwayat
        $pengajuan->load('riwayat.user');

        // Menggunakan view yang sama dengan admin
        return view('admin.show', compact('pengajuan'));
    }
}
