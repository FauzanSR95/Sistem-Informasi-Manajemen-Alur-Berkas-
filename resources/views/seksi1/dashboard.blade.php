<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Seksi 1') }}
        </h2>
    </x-slot>

    {{-- Tambahkan state 'openAddTimeModal' untuk modal baru --}}
    <div class="py-12" x-data="{ openBaModal: false, openAddTimeModal: false, selectedPengajuanId: null, actionUrl: '' }">
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemeriksaan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pengajuans as $pengajuan)
                                    <tr id="row-{{ $pengajuan->id }}"
                                        class="@if($pengajuan->original_tenggat_waktu && now()->isAfter($pengajuan->original_tenggat_waktu)) bg-red-100 hover:bg-red-200 @else hover:bg-gray-50 @endif">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $pengajuan->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $pengajuan->nama_pemohon }}
                                            @if($pengajuan->status === 'Berita Acara')
                                                <br><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800 mt-1">Status: Sedang BA</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $pengajuan->nomor_hak }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $pengajuan->desa }}, {{ $pengajuan->kecamatan }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{-- Mengambil tanggal dari riwayat terakhir (saat berkas masuk ke seksi ini) --}}
                                            {{ $pengajuan->latestRiwayat?->created_at->format('d-m-Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" data-tenggat="{{ $pengajuan->tenggat_waktu }}" data-status="{{ $pengajuan->status }}"></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-4">
                                                <label class="flex items-center"><input type="checkbox" data-id="{{ $pengajuan->id }}" data-field="spasial_checked" @checked($pengajuan->spasial_checked) class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"><span class="ms-2 text-sm text-gray-600">Spasial</span></label>
                                                <label class="flex items-center"><input type="checkbox" data-id="{{ $pengajuan->id }}" data-field="tekstual_checked" @checked($pengajuan->tekstual_checked) class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"><span class="ms-2 text-sm text-gray-600">Tekstual</span></label>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                
                                                {{-- Tombol Kirim ke Seksi 2 (Warna Oranye) --}}
                                                {{-- Tombol ini SELALU MUNCUL, hanya teksnya yang berubah jika sedang BA --}}
                                                <form method="POST" action="{{ route('seksi1.kirim', $pengajuan) }}" class="inline">
                                                    @csrf
                                                    <button id="kirim-btn-{{ $pengajuan->id }}" type="submit" disabled class="inline-flex items-center px-4 py-2 bg-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-600 focus:bg-orange-600 active:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 disabled:opacity-25 disabled:bg-gray-400 transition ease-in-out duration-150">
                                                        {{ $pengajuan->status === 'Berita Acara' ? 'Selesaikan BA & Kirim ke Seksi 2' : 'Kirim ke Seksi 2' }}
                                                    </button>
                                                </form>

                                                {{-- SEMBUNYIKAN SEMUA TOMBOL DI BAWAH INI JIKA STATUSNYA BERITA ACARA --}}
                                                @if($pengajuan->status !== 'Berita Acara')
                                                
                                                {{-- Tombol Selesai & Ke Loket (Biru) --}}
                                                <form method="POST" action="{{ route('seksi1.kembalikan', $pengajuan) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin pemeriksaan selesai dan berkas siap diteruskan ke Loket?');">
                                                    @csrf
                                                    <button id="selesai-btn-{{ $pengajuan->id }}" type="submit" disabled class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 disabled:bg-gray-400 transition ease-in-out duration-150">
                                                        Selesai & Ke Loket
                                                    </button>
                                                </form>
                                                
                                                    {{-- Tombol Ke Arsip (Warna Hijau) --}}
                                                    <form method="POST" action="{{ route('seksi1.kembalikan_arsip', $pengajuan) }}" class="inline" onsubmit="return confirm('Kembalikan ke Arsip?');">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 focus:bg-green-600 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                            Ke Arsip
                                                        </button>
                                                    </form>


                                                    {{-- Tombol BA (Merah) --}}
                                                    <button @click="openBaModal = true; selectedPengajuanId = {{ $pengajuan->id }}; actionUrl = '{{ route('seksi1.ba', $pengajuan) }}'" type="button" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        BA
                                                    </button>

                                                    {{-- Tombol Tambah Waktu (Biru) --}}
                                                    <button @click="openAddTimeModal = true; selectedPengajuanId = {{ $pengajuan->id }}; actionUrl = '{{ route('seksi1.pengajuan.add_time', $pengajuan) }}'" type="button" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        +
                                                    </button>
                                                    
                                                @endif

                                            </div>
                                        </td>
                                    </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada tugas</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">{{ $pengajuans->links() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="openBaModal" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen">
                <div @click.away="openBaModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <div @click.stop class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg w-full">
                    <form :action="actionUrl" method="POST">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Buat Berita Acara untuk Pengajuan #<span x-text="selectedPengajuanId"></span></h3>
                            <div class="mt-4">
                                <x-input-label for="catatan_ba" value="Catatan / Alasan" />
                                <x-textarea id="catatan_ba" name="catatan_ba" class="mt-1 block w-full" rows="3" required></x-textarea>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <x-danger-button type="submit" class="sm:ms-3">
                                Simpan BA
                            </x-danger-button>
                            <button @click="openBaModal = false" type="button" class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div x-show="openAddTimeModal" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen">
                <div @click.away="openAddTimeModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
                <div @click.stop class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg w-full">
                    <form :action="actionUrl" method="POST">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Tambah Waktu untuk Pengajuan #<span x-text="selectedPengajuanId"></span></h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="duration" class="block text-sm font-medium text-gray-700">Tambah Berapa Hari?</label>
                                    <input id="duration" name="duration" type="number" value="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                </div>
                                <div>
                                    <label for="catatan" class="block text-sm font-medium text-gray-700">Alasan Penambahan Waktu</label>
                                    <textarea id="catatan" name="catatan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <x-themed-button type="submit" class="sm:ms-3">Simpan</x-themed-button>
                            <button @click="openAddTimeModal = false" type="button" class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

    {{-- Script JavaScript untuk Checkbox --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function checkAllCheckboxes(rowId) {
                const row = document.getElementById(`row-${rowId}`);
                if (!row) return;

                const checkboxes = row.querySelectorAll('input[type="checkbox"]');
                const kirimBtn = document.getElementById(`kirim-btn-${rowId}`);
                const selesaiBtn = document.getElementById(`selesai-btn-${rowId}`); // Menangkap tombol Selesai

                let allChecked = true;
                checkboxes.forEach(cb => {
                    if (!cb.checked) {
                        allChecked = false;
                    }
                });

                // Mengaktifkan/menonaktifkan kedua tombol secara bersamaan
                if (kirimBtn) kirimBtn.disabled = !allChecked;
                if (selesaiBtn) selesaiBtn.disabled = !allChecked; 
            }

            document.querySelectorAll('tbody tr[id]').forEach(row => {
                const rowId = row.id.split('-')[1];
                checkAllCheckboxes(rowId);
            });

            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function(event) {
                    const pengajuanId = event.target.dataset.id;
                    const field = event.target.dataset.field;
                    const value = event.target.checked;

                    fetch(`/seksi1/update-check/${pengajuanId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ field: field, value: value })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            checkAllCheckboxes(pengajuanId);
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            });
        });
    </script>
</x-app-layout>
