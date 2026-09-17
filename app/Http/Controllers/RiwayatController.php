<?php

namespace App\Http\Controllers;

use App\Models\RiwayatAlur;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    /**
     * Tampilkan halaman riwayat.
     */
    public function index()
    {
        // Tampilkan semua riwayat untuk semua user, diurutkan dari terbaru
        $riwayat = RiwayatAlur::with('user', 'pengajuan')
                                ->latest()
                                ->paginate(20);
        
        return view('riwayat.index', compact('riwayat'));
    }
}