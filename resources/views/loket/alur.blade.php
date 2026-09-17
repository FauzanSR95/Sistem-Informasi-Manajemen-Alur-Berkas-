<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Alur Pengajuan Berkas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-3 rounded-md">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 font-medium text-sm text-red-600 bg-red-100 p-3 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-md sm:rounded-xl border border-gray-200 p-6 mb-6">
                <form action="{{ route('loket.alur') }}" method="GET" class="flex gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Cari Nama, No. Berkas, atau No. Hak..." 
                        class="flex-1 border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                    <button type="submit" class="inline-flex items-center px-6 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('loket.alur') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-md sm:rounded-xl border border-gray-200">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg mb-4">Pengajuan Sedang Berjalan</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Berkas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Pemohon</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Dibuat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Hak</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Desa/Kec</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Saat Ini</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pengajuansBerjalan as $pengajuan)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $pengajuan->nomor_berkas }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $pengajuan->nama_pemohon }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $pengajuan->created_at->format('d-m-Y') }} 
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $pengajuan->nomor_hak }}
                                        </td>
                                        <td class="px-6 py-4">{{ $pengajuan->desa }}, {{ $pengajuan->kecamatan }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($pengajuan->status == 'Di Arsip') bg-green-100 text-green-800
                                                @elseif($pengajuan->status == 'Di Seksi 1') bg-yellow-100 text-yellow-800
                                                @elseif($pengajuan->status == 'Di Seksi 2') bg-orange-100 text-orange-800
                                                @elseif($pengajuan->status == 'Terlambat') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ $pengajuan->status }}
                                            </span>
                                            @if ($pengajuan->latestRiwayat && $pengajuan->latestRiwayat->user)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Oleh: {{ $pengajuan->latestRiwayat->user->name }}
                                                    ({{ Str::ucfirst($pengajuan->latestRiwayat->user->role->value) }})
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex gap-3">
                                                @if ($pengajuan->status == 'Di Arsip')
                                                    <a href="{{ route('loket.edit', $pengajuan) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                @endif
                                                
                                                <a href="{{ route('loket.cetak', $pengajuan) }}" target="_blank" class="text-green-600 hover:text-green-900 font-bold">
                                                    Cetak QR
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">Tidak ada pengajuan yang sedang berjalan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $pengajuansBerjalan->links() }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-md sm:rounded-xl border border-gray-200">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg mb-4">Berkas yang Dikembalikan (Perlu Tindak Lanjut)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Berkas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Pemohon</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Hak</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Desa/Kec</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Saat Ini</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan Pengembalian</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pengajuansDikembalikan as $pengajuan)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $pengajuan->nomor_berkas }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $pengajuan->nama_pemohon }}</td>
                                        <td class="px-6 py-4">{{ $pengajuan->nomor_hak }}</td>
                                        <td class="px-6 py-4">{{ $pengajuan->desa }}, {{ $pengajuan->kecamatan }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                {{ $pengajuan->status }}
                                            </span>
                                            @if ($pengajuan->latestRiwayat && $pengajuan->latestRiwayat->user)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Oleh: {{ $pengajuan->latestRiwayat->user->name }}
                                                    ({{ Str::ucfirst($pengajuan->latestRiwayat->user->role->value) }})
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-red-600">{{ $pengajuan->riwayat()->latest()->first()->catatan ?? 'Tidak ada catatan' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('loket.edit', $pengajuan) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                Revisi & Kirim Ulang
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada berkas yang dikembalikan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $pengajuansDikembalikan->links() }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-md sm:rounded-xl border border-gray-200">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg mb-4">Berkas Selesai (Siap Diarsipkan)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Berkas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Pemohon</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Hak</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Desa/Kec</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Saat Ini</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan Terakhir</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi Final</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pengajuansSelesai as $pengajuan)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $pengajuan->nomor_berkas }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $pengajuan->nama_pemohon }}</td>
                                        <td class="px-6 py-4">{{ $pengajuan->nomor_hak }}</td>
                                        <td class="px-6 py-4">{{ $pengajuan->desa }}, {{ $pengajuan->kecamatan }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $pengajuan->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pengajuan->latestRiwayat->catatan ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <form action="{{ route('loket.finalisasi_selesai', $pengajuan) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menutup proses untuk berkas ini?');">
                                                @csrf
                                                <x-primary-button class="bg-gray-800 hover:bg-gray-900">Tutup & Arsipkan</x-primary-button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada berkas yang selesai.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $pengajuansSelesai->links() }}
                        </div>
                    </div>
                </div>
            </div>
            {{-- =================== BAGIAN BARU UNTUK RIWAYAT =================== --}}
            <div class="bg-white overflow-hidden shadow-md sm:rounded-xl border border-gray-200">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg mb-4">Riwayat Berkas Selesai</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Berkas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Pemohon</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Hak</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Desa/Kec</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Final</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Selesai</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pengajuansDiarsipkan as $pengajuan)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $pengajuan->nomor_berkas }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $pengajuan->nama_pemohon }}</td>
                                        <td class="px-6 py-4">{{ $pengajuan->nomor_hak }}</td>
                                        <td class="px-6 py-4">{{ $pengajuan->desa }}, {{ $pengajuan->kecamatan }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-800">
                                                {{ $pengajuan->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $pengajuan->updated_at->format('d-m-Y H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">Belum ada riwayat berkas yang selesai.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $pengajuansDiarsipkan->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
