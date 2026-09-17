<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Progres - Kantah Rokan Hulu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen p-4 md:p-8">

    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <img src="{{ asset('logo_bpn_welcome.png') }}" class="h-16 w-auto" alt="Logo BPN">
            <a href="{{ route('public.tracking') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 flex items-center bg-blue-50 px-4 py-2 rounded-xl transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden mb-6">
            <div class="bg-slate-800 p-6 text-white text-center">
                <p class="text-slate-400 text-xs uppercase tracking-widest font-bold mb-1">Nomor Berkas</p>
                <h2 class="text-2xl font-bold">{{ $berkas->nomor_berkas }}</h2>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 border-b border-slate-100">
                <div class="p-6 border-r border-slate-100">
                    <p class="text-[10px] text-slate-400 uppercase font-extrabold mb-1 tracking-wider">Pemohon</p>
                    <p class="text-slate-800 font-bold text-sm">{{ $berkas->nama_pemohon }}</p>
                </div>
                <div class="p-6 border-r border-slate-100">
                    <p class="text-[10px] text-slate-400 uppercase font-extrabold mb-1 tracking-wider">Nomor Hak</p>
                    <p class="text-slate-800 font-bold text-sm">{{ $berkas->nomor_hak }}</p>
                </div>
                <div class="p-6 border-r border-slate-100">
                    <p class="text-[10px] text-slate-400 uppercase font-extrabold mb-1 tracking-wider">Desa</p>
                    <p class="text-slate-800 font-bold text-sm">{{ $berkas->desa }}</p>
                </div>
                <div class="p-6">
                    <p class="text-[10px] text-slate-400 uppercase font-extrabold mb-1 tracking-wider">Kecamatan</p>
                    <p class="text-slate-800 font-bold text-sm">{{ $berkas->kecamatan }}</p>
                </div>
            </div>

            <div class="p-8">
                @php
                    $status = $berkas->status;
                    $step = 1; 

                    if(in_array($status, ['Di Arsip', 'Di Seksi 1', 'Di Seksi 2'])) { $step = 2; } 
                    elseif($status == 'Selesai (di Loket)') { $step = 3; } 
                    elseif($status == 'Selesai') { $step = 4; } 
                    elseif(in_array($status, ['Berita Acara', 'Dikembalikan'])) { $step = 2.5; }
                @endphp

                <div class="space-y-8 relative">
                    <div class="absolute left-[15px] top-2 h-[calc(100%-20px)] w-0.5 bg-slate-100"></div>

                    <div class="relative flex items-start group">
                        <div class="z-10 flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white shadow-lg shadow-blue-200">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                        </div>
                        <div class="ml-6">
                            <h3 class="font-bold text-slate-800">Berkas Diterima</h3>
                            <p class="text-sm text-slate-500">Petugas telah menerima dokumen di Loket.</p>
                        </div>
                    </div>

                    <div class="relative flex items-start group">
                        <div class="absolute left-[15px] -top-8 h-8 w-0.5 {{ $step >= 2 ? 'bg-blue-600' : 'bg-slate-100' }}"></div>
                        <div class="z-10 flex items-center justify-center w-8 h-8 rounded-full {{ $step >= 2 ? ($step == 2.5 ? 'bg-orange-500 animate-bounce' : 'bg-blue-600') : 'bg-slate-200' }} text-white transition-all duration-500">
                            @if($step >= 3)
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                            @elseif($step == 2.5)
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/></svg>
                            @else
                                <div class="w-2 h-2 bg-white rounded-full {{ $step == 2 ? 'animate-ping' : '' }}"></div>
                            @endif
                        </div>
                        <div class="ml-6">
                            <h3 class="font-bold {{ $step >= 2 ? 'text-slate-800' : 'text-slate-400' }}">
                                {{ $step == 2.5 ? 'Berkas Tertunda (Berita Acara)' : 'Proses Verifikasi Internal' }}
                            </h3>
                            <p class="text-sm text-slate-500">Validasi kelengkapan berkas dan pengecekan teknis oleh tim BPN.</p>
                        </div>
                    </div>

                    <div class="relative flex items-start group">
                        <div class="absolute left-[15px] -top-8 h-8 w-0.5 {{ $step >= 3 ? 'bg-blue-600' : 'bg-slate-100' }}"></div>
                        <div class="z-10 flex items-center justify-center w-8 h-8 rounded-full {{ $step >= 3 ? 'bg-blue-600' : 'bg-slate-200' }} text-white transition-all duration-500">
                            @if($step >= 4)
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                            @else
                                <div class="w-2 h-2 bg-white rounded-full {{ $step == 3 ? 'animate-ping' : '' }}"></div>
                            @endif
                        </div>
                        <div class="ml-6">
                            <h3 class="font-bold {{ $step >= 3 ? 'text-slate-800' : 'text-slate-400' }}">Siap Diambil di Loket</h3>
                            <p class="text-sm text-slate-500">Sertifikat/Dokumen telah diproses dan siap diserahkan.</p>
                        </div>
                    </div>

                    <div class="relative flex items-start group">
                        <div class="absolute left-[15px] -top-8 h-8 w-0.5 {{ $step == 4 ? 'bg-green-600' : 'bg-slate-100' }}"></div>
                        <div class="z-10 flex items-center justify-center w-8 h-8 rounded-full {{ $step == 4 ? 'bg-green-600' : 'bg-slate-200' }} text-white shadow-lg transition-all duration-500">
                            @if($step == 4)
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            @else
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                            @endif
                        </div>
                        <div class="ml-6">
                            <h3 class="font-bold {{ $step == 4 ? 'text-green-600' : 'text-slate-400' }}">Proses Selesai</h3>
                            <p class="text-sm text-slate-500">Dokumen telah diserahkan sepenuhnya kepada pemilik berkas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-center text-slate-400 text-xs">
            Layanan ini disediakan oleh Kantor Pertanahan Rokan Hulu.<br>
            Harap hubungi loket informasi jika terdapat kendala lebih lanjut.
        </p>
    </div>

</body>
</html>