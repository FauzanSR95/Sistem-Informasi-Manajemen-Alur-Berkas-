<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use Illuminate\Http\Request;

class PublicTrackingController extends Controller
{
    // Menampilkan halaman form pelacakan
    public function index()
    {
        return view('public.tracking');
    }

    // Proses pencarian berkas
    public function search(Request $request)
    {
        $request->validate([
            'nomor_berkas' => 'required',
            'nomor_hak' => 'required',
        ]);

        $berkas = Pengajuan::where('nomor_berkas', $request->nomor_berkas)
                            ->where('nomor_hak', $request->nomor_hak)
                            ->first();

        if (!$berkas) {
            return back()->with('error', 'Data tidak ditemukan. Pastikan Nomor Berkas dan Nomor Hak sudah benar.');
        }

        return view('public.result', compact('berkas'));
    }

    public function autoSearch(Request $request)
    {
        // Mengambil data berkas berdasarkan parameter dari URL (QR Code)
        $berkas = \App\Models\Pengajuan::where('nomor_berkas', $request->nomor_berkas)
                            ->where('nomor_hak', $request->nomor_hak)
                            ->first();

        if (!$berkas) {
            return redirect()->route('public.tracking')->with('error', 'Data berkas tidak ditemukan atau QR Code tidak valid.');
        }

        return view('public.result', compact('berkas'));
    }
}