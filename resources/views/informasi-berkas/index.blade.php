<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Informasi & Pencarian Berkas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md sm:rounded-xl border border-gray-200 p-6 mb-6">
                <form action="{{ route('informasi-berkas.index') }}" method="GET">
                    <div class="flex items-center space-x-4">
                        <div class="flex-grow">
                            <x-input-label for="search" value="Cari Berkas (Nomor Berkas, Nama, Nomor Hak, dll)" />
                            <x-text-input id="search" name="search" type="text" class="mt-1 block w-full"
                                          value="{{ $search ?? '' }}"
                                          placeholder="Masukkan kata kunci..."/>
                        </div>
                        <div class="flex-shrink-0">
                            <x-primary-button class="mt-5">Cari</x-primary-button>
                            <a href="{{ route('informasi-berkas.index') }}" class="mt-5 inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-md sm:rounded-xl border border-gray-200">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Berkas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Pemohon</th>
                                    {{-- TAMBAHAN 2 KOLOM BARU DI BAWAH INI --}}
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Hak</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Desa/Kec</th>
                                    {{-- =================================== --}}
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Terakhir</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl. Dibuat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pengajuans as $pengajuan)
                                    <tr class="@if($pengajuan->original_tenggat_waktu && now()->isAfter($pengajuan->original_tenggat_waktu) && !in_array($pengajuan->status, ['Selesai', 'Selesai (di Loket)', 'Dibatalkan', 'Dikembalikan', 'Berita Acara'])) bg-red-100 @else hover:bg-gray-50 @endif">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $pengajuan->nomor_berkas ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $pengajuan->nama_pemohon }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $pengajuan->nomor_hak }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $pengajuan->desa }}, {{ $pengajuan->kecamatan }}</td>
                                        
                                        {{-- UPDATE: Bagian Status Terakhir Diberi Warna --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColor = match($pengajuan->status) {
                                                    'Di Arsip' => 'bg-green-100 text-green-800',
                                                    'Di Seksi 1' => 'bg-yellow-100 text-yellow-800',
                                                    'Di Seksi 2' => 'bg-orange-100 text-orange-800',
                                                    'Selesai' => 'bg-emerald-100 text-emerald-800',
                                                    'Dikembalikan' => 'bg-red-100 text-red-800',
                                                    'BA' => 'bg-red-100 text-red-800',
                                                    'Berita Acara' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                };
                                            @endphp
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                                {{ $pengajuan->status }}
                                            </span>
                                            
                                            @if ($pengajuan->latestRiwayat && $pengajuan->latestRiwayat->user)
                                                <div class="mt-1 text-xs text-gray-500">Oleh: {{ $pengajuan->latestRiwayat->user->name }}</div>
                                            @endif
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $pengajuan->created_at->format('d-m-Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('pengajuan.show', $pengajuan) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data yang ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                     <div class="mt-4">
                        {{ $pengajuans->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>