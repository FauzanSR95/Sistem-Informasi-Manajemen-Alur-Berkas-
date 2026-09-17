<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Arsip') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ openCatatanModal: false, modalContent: '', openBaModal: false, openSeksi2Modal: false, selectedPengajuanId: null, actionUrl: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-md sm:rounded-xl border border-gray-200">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg">Daftar Tugas Masuk</h3>
                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-3 rounded-md">
                            {{ session('success') }}
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sisa Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelengkapan</th>
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
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($pengajuan->catatan_loket)
                                                <button @click="openCatatanModal = true; modalContent = '{{ addslashes($pengajuan->catatan_loket) }}'" class="text-indigo-600 hover:text-indigo-900 text-sm">Lihat</button>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" data-tenggat="{{ $pengajuan->tenggat_waktu }}" data-status="{{ $pengajuan->status }}"></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-4">
                                                <label class="flex items-center"><input type="checkbox" data-id="{{ $pengajuan->id }}" data-field="bt_checked" @checked($pengajuan->bt_checked) class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"><span class="ms-2 text-sm text-gray-600">BT</span></label>
                                                <label class="flex items-center"><input type="checkbox" data-id="{{ $pengajuan->id }}" data-field="su_checked" @checked($pengajuan->su_checked) class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"><span class="ms-2 text-sm text-gray-600">SU</span></label>
                                                <label class="flex items-center"><input type="checkbox" data-id="{{ $pengajuan->id }}" data-field="ht_checked" @checked($pengajuan->ht_checked) class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"><span class="ms-2 text-sm text-gray-600">HT</span></label>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">

                                            {{-- Tombol Seksi 1 (Warna Kuning) --}}
                                            <form method="POST" action="{{ route('arsip.kirim', $pengajuan) }}" class="inline">
                                                @csrf
                                                <button id="kirim-btn-{{ $pengajuan->id }}" type="submit" disabled class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 disabled:opacity-25 disabled:bg-gray-400 transition ease-in-out duration-150">
                                                    {{ $pengajuan->status === 'Berita Acara' ? 'Selesaikan BA & Kirim ke Seksi 1' : 'Kirim ke Seksi 1' }}
                                                </button>
                                            </form>

                                            {{-- Tombol Seksi 2 (Warna Oranye) --}}
                                            <button @click="openSeksi2Modal = true; selectedPengajuanId = {{ $pengajuan->id }}; actionUrl = '{{ route('arsip.kirim_seksi2', $pengajuan) }}'" id="kirim-seksi2-btn-{{ $pengajuan->id }}" type="button" disabled class="inline-flex items-center px-4 py-2 bg-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-600 focus:bg-orange-600 active:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 disabled:opacity-25 disabled:bg-gray-400 transition ease-in-out duration-150">
                                                {{ $pengajuan->status === 'Berita Acara' ? 'Selesaikan BA & Kirim ke Seksi 2' : 'Kirim ke Seksi 2' }}
                                            </button>

                                            {{-- SEMBUNYIKAN TOMBOL KEMBALI DAN BUAT BA JIKA STATUSNYA BERITA ACARA --}}
                                            @if($pengajuan->status !== 'Berita Acara')

                                                {{-- Tombol Kembalikan ke Loket --}}
                                                <form method="POST" action="{{ route('arsip.kembalikan_loket', $pengajuan) }}" class="inline" onsubmit="return confirm('Anda yakin ingin mengembalikan berkas ini ke Loket?');">
                                                    @csrf
                                                    <x-secondary-button>Kembalikan ke Loket</x-secondary-button>
                                                </form>

                                                {{-- Tombol Buat BA (Hanya Muncul Jika Berkas Belum BA) --}}
                                                <button @click="openBaModal = true; selectedPengajuanId = {{ $pengajuan->id }}; actionUrl = '{{ route('arsip.ba', $pengajuan) }}'" type="button" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    Buat BA
                                                </button>

                                            @endif

                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada tugas</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $pengajuans->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="openCatatanModal" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div @click.away="openCatatanModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div @click.stop class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Catatan dari Loket</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" x-text="modalContent"></p>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="openCatatanModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="openSeksi2Modal" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen">
                <div @click.away="openSeksi2Modal = false" class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
                <div @click.stop class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg w-full">
                    <form :action="actionUrl" method="POST">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Kirim ke Seksi 2 untuk Pengajuan #<span x-text="selectedPengajuanId"></span></h3>
                            <div class="mt-4">
                                <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan Wajib</label>
                                <textarea id="catatan" name="catatan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <x-themed-button type="submit" class="sm:ms-3">Kirim</x-themed-button>
                            <button @click="openSeksi2Modal = false" type="button" class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                        </div>
                    </form>
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
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function checkAllCheckboxes(rowId) {
                const row = document.getElementById(`row-${rowId}`);
                if (!row) return;

                // Ambil checkbox BT dan SU secara spesifik
                const btCheckbox = row.querySelector('input[data-field="bt_checked"]');
                const suCheckbox = row.querySelector('input[data-field="su_checked"]');

                const kirimBtn1 = document.getElementById(`kirim-btn-${rowId}`);
                const kirimBtn2 = document.getElementById(`kirim-seksi2-btn-${rowId}`);

                // Tombol akan aktif jika BT dan SU sudah diceklis
                const isReadyToSend = btCheckbox.checked && suCheckbox.checked;

                kirimBtn1.disabled = !isReadyToSend;
                kirimBtn2.disabled = !isReadyToSend;
            }

            document.querySelectorAll('tbody tr[id]').forEach(row => {
                const rowId = row.id.split('-')[1];
                checkAllCheckboxes(rowId);
            });

            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.addEventListener('change', function(event) {
                    const pengajuanId = event.target.dataset.id;
                    const field = event.target.dataset.field;
                    const value = event.target.checked;
                    fetch(`/arsip/update-check/${pengajuanId}`, {
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
