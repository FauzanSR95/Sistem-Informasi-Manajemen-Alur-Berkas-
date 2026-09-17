<?php

namespace App\Http\Controllers\Arsip;

use App\Http\Controllers\Controller;
use App\Models\ArsipRak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = ArsipRak::query();

        // Logika Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            
            // Menggunakan closure (function($q)) agar logika OR terkelompok dengan aman
            $query->where(function($q) use ($search) {
                $q->where('nomor_hm', 'like', "%{$search}%")
                  ->orWhere('jenis_sertifikat', 'like', "%{$search}%")
                  ->orWhere('desa', 'like', "%{$search}%")
                  ->orWhere('kecamatan', 'like', "%{$search}%")
                  ->orWhere('lokasi_rak', 'like', "%{$search}%")
                  ->orWhere('lokasi_baris', 'like', "%{$search}%");
            });
        }

        $arsipRaks = $query->latest()->paginate(15);

        return view('arsip.katalog.index', compact('arsipRaks'));
    }

    public function create()
    {
        return view('arsip.katalog.create');
    }

    // FUNGSI BANTUAN UNTUK MENGECEK TUMPANG TINDIH RENTANG NOMOR HM
    private function checkOverlap($inputHm, $desa, $jenis, $ignoreId = null)
    {
        // 1. Bersihkan spasi dan pecah berdasarkan tanda strip '-'
        $newParts = explode('-', str_replace(' ', '', $inputHm));
        $newStart = (int) $newParts[0];
        $newEnd = isset($newParts[1]) ? (int) $newParts[1] : $newStart;

        // Balikkan jika user salah ketik terbalik (misal: 50-1)
        if ($newStart > $newEnd) {
            $temp = $newStart; $newStart = $newEnd; $newEnd = $temp;
        }

        // 2. Ambil data arsip khusus di desa DAN JENIS yang sama
        $query = ArsipRak::where('desa', $desa)->where('jenis_sertifikat', $jenis);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId); // Abaikan data yang sedang diedit
        }
        $existingRecords = $query->get();

        // 3. Cek tumpang tindih dengan data yang sudah ada
        foreach ($existingRecords as $record) {
            $existParts = explode('-', str_replace(' ', '', $record->nomor_hm));
            $existStart = (int) $existParts[0];
            $existEnd = isset($existParts[1]) ? (int) $existParts[1] : $existStart;

            if ($existStart > $existEnd) {
                $temp = $existStart; $existStart = $existEnd; $existEnd = $temp;
            }

            // Rumus Matematika Overlap
            if ($newStart <= $existEnd && $newEnd >= $existStart) {
                return $record->nomor_hm; // Kembalikan nomor yang bertabrakan untuk pesan error
            }
        }
        
        return false; // Aman, tidak ada tumpang tindih
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_hm' => 'required|string|max:255',
            'jenis_sertifikat' => 'required|string',
            'desa' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'lokasi_rak' => 'required|integer|min:1',
            'lokasi_baris' => 'required|integer|min:1',
        ]);

        // CEK OVERLAP SEBELUM MENYIMPAN (Sekarang memasukkan jenis_sertifikat)
        $tabrakan = $this->checkOverlap($request->nomor_hm, $request->desa, $request->jenis_sertifikat);
        if ($tabrakan) {
            return back()->withInput()->withErrors([
                'nomor_hm' => "Gagal! Rentang Nomor HM tumpang tindih dengan data {$request->jenis_sertifikat} yang sudah ada di desa ini (Bentrok dengan nomor: {$tabrakan})."
            ]);
        }

        ArsipRak::create([
            'user_arsip_id' => Auth::id(),
            'nomor_hm' => $request->nomor_hm,
            'jenis_sertifikat' => $request->jenis_sertifikat,
            'desa' => $request->desa,
            'kecamatan' => $request->kecamatan,
            'lokasi_rak' => $request->lokasi_rak,
            'lokasi_baris' => $request->lokasi_baris,
        ]);

        return redirect()->route('arsip.katalog.index')->with('success', 'Data arsip berhasil ditambahkan.');
    }

    public function edit(ArsipRak $katalog)
    {
        return view('arsip.katalog.edit', compact('katalog'));
    }

    public function update(Request $request, ArsipRak $katalog)
    {
        $request->validate([
            'nomor_hm' => 'required|string|max:255',
            'jenis_sertifikat' => 'required|string',
            'desa' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'lokasi_rak' => 'required|integer|min:1',
            'lokasi_baris' => 'required|integer|min:1',
        ]);

        // CEK OVERLAP SEBELUM UPDATE (Sekarang memasukkan jenis_sertifikat)
        $tabrakan = $this->checkOverlap($request->nomor_hm, $request->desa, $request->jenis_sertifikat, $katalog->id);
        if ($tabrakan) {
            return back()->withInput()->withErrors([
                'nomor_hm' => "Gagal! Rentang Nomor HM tumpang tindih dengan data {$request->jenis_sertifikat} yang sudah ada di desa ini (Bentrok dengan nomor: {$tabrakan})."
            ]);
        }

        $katalog->update($request->all());

        return redirect()->route('arsip.katalog.index')->with('success', 'Data arsip berhasil diupdate.');
    }

    public function destroy(ArsipRak $katalog)
    {
        $katalog->delete();
        return redirect()->route('arsip.katalog.index')->with('success', 'Data arsip berhasil dihapus.');
    }
}