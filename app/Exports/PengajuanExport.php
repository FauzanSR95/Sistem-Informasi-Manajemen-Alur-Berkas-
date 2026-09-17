<?php

namespace App\Exports;

use App\Models\Pengajuan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PengajuanExport implements FromCollection, WithHeadings, WithMapping
{
    // 1. Tambahkan properti untuk menyimpan filter
    protected $bulan;
    protected $tahun;

    // 2. Tambahkan constructor untuk menerima filter
    public function __construct($bulan = null, $tahun = null)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection()
    {
        $query = Pengajuan::with('userLoket');

        // Terapkan filter bulan jika ada, konversi ke integer
        if ($this->bulan) {
            $query->whereMonth('created_at', (int)$this->bulan); // <-- Tambahkan (int)
        }

        // Terapkan filter tahun jika ada, konversi ke integer
        if ($this->tahun) {
            $query->whereYear('created_at', (int)$this->tahun); // <-- Tambahkan (int)
        }

        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // (headings tidak berubah)
        return [
            'ID Pengajuan',
            'Nama Pemohon',
            'Nomor Hak',
            'Desa',
            'Kecamatan',
            'Status Terakhir',
            'Dibuat Oleh (Loket)',
            'Tanggal Dibuat',
        ];
    }

    /**
     * @param Pengajuan $pengajuan
     * @return array
     */
    public function map($pengajuan): array
    {
         // (map tidak berubah)
        return [
            $pengajuan->id,
            $pengajuan->nama_pemohon,
            $pengajuan->nomor_hak,
            $pengajuan->desa,
            $pengajuan->kecamatan,
            $pengajuan->status,
            $pengajuan->userLoket->name ?? 'N/A',
            $pengajuan->created_at->format('d-m-Y H:i:s'),
        ];
    }
}