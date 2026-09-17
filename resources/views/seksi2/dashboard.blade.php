<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Seksi 2') }}
        </h2>
    </x-slot>

    {{-- Tambahkan x-data untuk kontrol modal BA --}}
    <div class="py-12" x-data="{ openModal: false, selectedPengajuan: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-xl border border-gray-200">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg">Daftar Tugas Masuk</h3>
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
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 mt-6">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Pemohon</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Hak</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Desa/Kec</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Masuk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sisa Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan Terakhir</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pengajuans as $pengajuan)
                                    <tr class="@if($pengajuan->original_tenggat_waktu && now()->isAfter($pengajuan->original_tenggat_waktu)) bg-red-100 @endif">
                                        <td class="px-6 py-4">{{ $pengajuan->id }}</td>
                                        <td class="px-6 py-4">
                                            {{ $pengajuan->nama_pemohon }}
                                            @if($pengajuan->status === 'Berita Acara')
                                                <br><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800 mt-1">Status: Sedang BA</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">{{ $pengajuan->nomor_hak }}</td>
                                        <td class="px-6 py-4">{{ $pengajuan->desa }}, {{ $pengajuan->kecamatan }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{-- Mengambil tanggal dari riwayat terakhir (saat berkas masuk ke seksi ini) --}}
                                            {{ $pengajuan->latestRiwayat?->created_at->format('d-m-Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4" data-tenggat="{{ $pengajuan->tenggat_waktu }}" data-status="{{ $pengajuan->status }}"></td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            @if ($pengajuan->latestRiwayat)
                                                {{ $pengajuan->latestRiwayat->catatan }}
                                                <div class="text-xs text-gray-400 mt-1">Oleh: {{ $pengajuan->latestRiwayat->user->name ?? 'N/A' }}</div>
                                            @endif
                                        </td>
                                        {{-- KOLOM AKSI BARU DENGAN BANYAK TOMBOL --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex flex-col space-y-2">
                                                
                                                {{-- Tombol Utama: Selesai & Kirim ke Loket --}}
                                                {{-- Tombol ini SELALU MUNCUL, teksnya berubah dinamis --}}
                                                <form method="POST" action="{{ route('seksi2.kirimKeLoket', $pengajuan) }}" onsubmit="return confirm('Anda yakin ingin menyelesaikan dan mengirim berkas ini ke Loket?');">
                                                    @csrf
                                                    <x-secondary-button type="submit" class="w-full justify-center">
                                                        {{ $pengajuan->status === 'Berita Acara' ? 'Selesaikan BA & Kirim ke Loket' : 'Selesai & Kirim ke Loket' }}
                                                    </x-secondary-button>
                                                </form>

                                                {{-- SEMBUNYIKAN SEMUA TOMBOL DI BAWAH INI JIKA STATUSNYA BERITA ACARA --}}
                                                @if($pengajuan->status !== 'Berita Acara')
                                                    
                                                    <div class="flex space-x-2">
                                                        {{-- Tombol Kembalikan Ke Seksi 1 (Konsisten Kuning) --}}
                                                        <form method="POST" action="{{ route('seksi2.kembalikanKeSeksi1', $pengajuan) }}" onsubmit="return confirm('Anda yakin ingin mengembalikan berkas ini ke Seksi 1?');" class="flex-1">
                                                            @csrf
                                                            <button type="submit" class="inline-flex items-center w-full justify-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                                Ke Seksi 1
                                                            </button>
                                                        </form>

                                                        {{-- Tombol Kembalikan Ke Arsip (Konsisten Hijau) --}}
                                                        <form method="POST" action="{{ route('seksi2.kembalikanKeArsip', $pengajuan) }}" onsubmit="return confirm('Anda yakin ingin mengembalikan berkas ini ke Arsip?');" class="flex-1">
                                                            @csrf
                                                            <button type="submit" class="inline-flex items-center w-full justify-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 focus:bg-green-600 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                                Ke Arsip
                                                            </button>
                                                        </form>
                                                    </div>

                                                    {{-- Tombol Buat Berita Acara (Konsisten Merah) --}}
                                                    <button @click="openModal = true; selectedPengajuan = {{ $pengajuan->id }}" type="button" class="inline-flex items-center w-full justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        BA
                                                    </button>
                                                    
                                                @endif

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada tugas.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="openModal" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
             <div class="flex items-center justify-center min-h-screen">
                <div class="fixed inset-0 transition-opacity" @click="openModal = false">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <div @click.stop class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                    <form :action="'/seksi2/' + selectedPengajuan + '/buat-ba'" method="POST">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Buat Berita Acara untuk Pengajuan #<span x-text="selectedPengajuan"></span></h3>
                            <div class="mt-4">
                                <x-input-label for="catatan_ba" value="Isi Berita Acara" />
                                <x-textarea id="catatan_ba" name="catatan_ba" class="mt-1 block w-full" required></x-textarea>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <x-danger-button type="submit">Buat BA</x-danger-button>
                            <x-secondary-button type="button" @click="openModal = false" class="me-2">Batal</x-secondary-button>
                        </div>
                    </form>
                </div>
             </div>
        </div>
    </div>
</x-app-layout>
