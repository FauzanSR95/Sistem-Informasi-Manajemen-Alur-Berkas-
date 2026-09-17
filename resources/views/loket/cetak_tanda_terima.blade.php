<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tanda Terima - {{ str_replace('/', '-', $pengajuan->nomor_berkas) }}</title>
    <link rel="icon" type="image/png" href="{{ asset('logo_bpn_welcome.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f3f4f6; }
        
        @media print {
            @page { 
                margin: 0; 
            }
            body { 
                background-color: white;
                padding: 0;
                margin: 0;
            }
            /* Memastikan tombol benar-benar hilang saat print */
            .no-print { 
                display: none !important; 
            }
            .ticket-card {
                box-shadow: none !important;
                border: 2px dashed #000 !important;
                margin-top: 50px !important;
            }
        }

        .ticket-card { 
            border: 2px dashed #333; 
            padding: 30px; 
            width: 420px; 
            margin: 40px auto; 
            text-align: center; 
            border-radius: 15px;
            background: white;
            position: relative;
        }
    </style>
</head>
<body onload="window.print()">

    <div class="ticket-card shadow-xl">
        <div class="mb-4">
            <img src="{{ asset('logo_bpn_welcome.png') }}" class="h-16 mx-auto mb-2" alt="Logo BPN">
            <div class="text-lg font-extrabold border-b-2 border-gray-100 pb-2 uppercase tracking-tighter">
                Tanda Terima Berkas
            </div>
        </div>

        <div class="text-left text-sm mb-6 space-y-4">
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Nomor Berkas</p>
                <p class="font-bold text-gray-900 text-base">{{ $pengajuan->nomor_berkas }}</p>
            </div>

            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Nama Pemohon</p>
                <p class="font-bold text-gray-900">{{ $pengajuan->nama_pemohon }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">No. Hak</p>
                    <p class="font-bold text-gray-900">{{ $pengajuan->nomor_hak }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Kecamatan</p>
                    <p class="font-bold text-gray-900">{{ $pengajuan->kecamatan }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Desa</p>
                    <p class="font-bold text-gray-900">{{ $pengajuan->desa }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Waktu Cetak</p>
                    <p class="text-[11px] font-bold text-gray-700">{{ now()->format('d/m/Y H:i') }} WIB</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100 mb-4">
            <div class="flex justify-center mb-2">
                {!! $qrcode !!}
            </div>
            <p class="text-[11px] font-black text-gray-800 uppercase tracking-tighter">Scan Untuk Cek Progres Berkas</p>
            
            <div class="mt-3 pt-2 border-t border-gray-200">
                <p class="text-[8.5px] text-gray-500 font-bold uppercase tracking-widest mb-0.5">Atau masukkan Nomor Berkas di link:</p>
                <p class="text-[11px] font-bold text-blue-600 tracking-wide">{{ url('/lacak') }}</p>
            </div>
        </div>

        <div class="text-[9px] text-gray-400 leading-tight italic">
            Kantor Pertanahan Kabupaten Rokan Hulu<br>
            Dicetak secara otomatis melalui Sistem Informasi Digital Berkas (SIM Berkas).
        </div>
    </div>

    <div class="no-print mt-4 flex justify-center gap-4 pb-10">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-full font-bold text-xs transition shadow-lg">
            Cetak Ulang
        </button>
        <button onclick="window.close()" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-6 py-2 rounded-full font-bold text-xs transition shadow-sm">
            Tutup Halaman
        </button>
    </div>

</body>
</html>