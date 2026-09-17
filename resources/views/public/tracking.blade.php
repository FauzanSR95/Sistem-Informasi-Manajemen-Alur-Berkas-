<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Berkas - Kantah Rokan Hulu</title>
    <link rel="icon" type="image/png" href="{{ asset('logo_bpn_welcome.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <img src="{{ asset('logo_bpn_welcome.png') }}" class="h-24 mx-auto mb-4" alt="Logo BPN">
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Lacak Progres Berkas</h1>
            <p class="text-slate-500 mt-1">Kantor Pertanahan Kabupaten Rokan Hulu</p>
        </div>

        <div class="bg-white shadow-xl rounded-2xl p-8 border border-slate-100">
            
            @if (session('error'))
                <div class="mb-6 flex items-center p-4 text-sm text-red-800 border-t-4 border-red-500 bg-red-50 rounded-lg">
                    <svg class="flex-shrink-0 inline w-4 h-4 mr-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            <form action="{{ route('public.search') }}" method="POST" class="space-y-5">
                @csrf
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nomor Berkas</label>
                    <input type="text" name="nomor_berkas" 
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all outline-none placeholder:text-slate-400"
                        placeholder="Contoh: 1/BPN-RH/2026" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nomor Hak</label>
                    <input type="text" name="nomor_hak" 
                        oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all outline-none placeholder:text-slate-400"
                        placeholder="Masukkan nomor hak anda" required>
                </div>

                <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-[0.98]">
                    Cari Progres Berkas
                </button>
            </form>
        </div>

        <p class="text-center text-slate-400 text-xs mt-8">
            &copy; 2026 Kantor Pertanahan Kabupaten Rokan Hulu.<br>
            Layanan Informasi Publik Terpadu.
        </p>
    </div>

</body>
</html>