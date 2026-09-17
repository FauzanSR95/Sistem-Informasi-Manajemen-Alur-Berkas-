<?php

namespace App\Http\Controllers;

use App\Models\BeritaAcara;
use Illuminate\Http\Request;

class BeritaAcaraController extends Controller
{
    public function index()
    {
        // Ambil semua data Berita Acara, diurutkan dari yang terbaru
        // Kita eager load relasi 'pengajuan' dan 'user' agar lebih efisien
        $beritaAcaras = BeritaAcara::with(['pengajuan', 'user'])->latest()->paginate(10);

        return view('berita-acara.index', compact('beritaAcaras'));
    }
}