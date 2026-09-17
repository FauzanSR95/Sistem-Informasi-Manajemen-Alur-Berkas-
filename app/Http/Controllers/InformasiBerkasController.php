<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use Illuminate\Http\Request;

class InformasiBerkasController extends Controller
{
    public function index(Request $request)
    {
        // Ambil kata kunci pencarian dari input
        $search = $request->input('search');

        // Query dasar untuk mengambil semua pengajuan, diurutkan dari yang terbaru
        $query = Pengajuan::with('latestRiwayat.user')->latest();

        // Jika ada kata kunci pencarian, tambahkan kondisi 'where'
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_berkas', 'like', "%{$search}%")
                  ->orWhere('nama_pemohon', 'like', "%{$search}%")
                  ->orWhere('nomor_hak', 'like', "%{$search}%")
                  ->orWhere('desa', 'like', "%{$search}%")
                  ->orWhere('kecamatan', 'like', "%{$search}%");
            });
        }

        // Eksekusi query dengan paginasi
        $pengajuans = $query->paginate(15)->withQueryString();

        // Kirim data ke view
        return view('informasi-berkas.index', compact('pengajuans', 'search'));
    }
}
