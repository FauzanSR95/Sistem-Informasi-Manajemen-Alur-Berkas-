<?php

namespace App\Exports;

use App\Models\RiwayatAlur;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RiwayatLengkapExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
    * Mengambil data dari database.
    */
    public function query()
    {
        // Ambil semua data riwayat, diurutkan berdasarkan ID pengajuan lalu tanggalnya
        return RiwayatAlur::with(['pengajuan', 'user'])
                            ->orderBy('pengajuan_id', 'asc')
                            ->orderBy('created_at', 'asc');
    }

    /**
     * Mendefinisikan header kolom di Excel.
     */
    public function headings(): array
    {
        return [
            'ID Pengajuan',
            'Nama Pemohon',
            'Nomor Hak',
            'Tanggal Aksi',
            'Status Lama',
            'Status Baru',
            'Catatan/Alasan',
            'Petugas',
            'Role Petugas',
        ];
    }

    /**
     * Memetakan data untuk setiap baris di Excel.
     * @param RiwayatAlur $riwayat
     */
    public function map($riwayat): array
    {
        return [
            $riwayat->pengajuan->id ?? 'N/A',
            $riwayat->pengajuan->nama_pemohon ?? 'N/A',
            $riwayat->pengajuan->nomor_hak ?? 'N/A',
            $riwayat->created_at->format('d-m-Y H:i:s'),
            $riwayat->status_lama,
            $riwayat->status_baru,
            $riwayat->catatan,
            $riwayat->user->name ?? 'Sistem',
            $riwayat->user ? Str::ucfirst($riwayat->user->role->value) : '',
        ];
    }
}