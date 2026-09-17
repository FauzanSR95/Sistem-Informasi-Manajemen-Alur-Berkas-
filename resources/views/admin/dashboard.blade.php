<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Pengawasan') }}
        </h2>
    </x-slot>

    {{-- Inisialisasi Alpine.js untuk mengontrol modal pop-up --}}
    <div class="py-12" x-data="{ openModal: false, openFilterModal: false, selectedPengajuanId: null, actionUrl: '', filterBulan: '', filterTahun: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-3 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if(auth()->user()->role->value === 'admin' || auth()->user()->role->value === 'monitor')
            <div class="mb-4 grid grid-cols-2 md:grid-cols-5 gap-4">
                @if(auth()->user()->role->value === 'admin')
                    <a href="{{ route('admin.users.index') }}" class="text-center px-4 py-3 bg-indigo-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700 transition">
                        Manajemen User
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="text-center px-4 py-3 bg-gray-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-gray-700 transition">
                        Pengaturan
                    </a>
                @endif

                <a href="{{ route('statistics.index') }}" class="text-center px-4 py-3 bg-purple-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-purple-700 transition">
                    Lihat Statistik
                </a>
                {{-- PERUBAHAN 2: Ubah Tombol Export Rekap --}}
                <button type="button" @click="openFilterModal = true" class="text-center px-4 py-3 bg-green-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-green-700 transition">
                    Export Rekap
                </button>
                {{-- ====================================== --}}
                {{-- Tombol Baru --}}
                <a href="{{ route('statistics.export_lengkap') }}" class="text-center px-4 py-3 bg-teal-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-teal-700 transition">
                    Export Riwayat Lengkap
                </a>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-md sm:rounded-xl border border-gray-200">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg mb-4">Semua Data Pengajuan</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Pemohon</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Hak</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl. Dibuat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sisa Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pengajuans as $pengajuan)
                                    {{-- SEBELUM REVISI --}}
                                    {{-- <tr class="@if($pengajuan->status === 'Terlambat') bg-red-100 hover:bg-red-200 @else hover:bg-gray-50 @endif"> --}}
                                    {{-- <p style="background: yellow; color: black; padding: 5px; margin: 5px; border: 1px solid black;">
                                        <b>Debug untuk ID #{{ $pengajuan->id }}:</b><br>
                                        Nilai original_tenggat_waktu: {{ $pengajuan->original_tenggat_waktu ? $pengajuan->original_tenggat_waktu->toDateTimeString() : 'KOSONG (NULL)' }}<br>
                                        Waktu Sekarang: {{ now()->toDateTimeString() }}<br>
                                        Apakah Seharusnya Merah?: {{ $pengajuan->original_tenggat_waktu && now()->isAfter($pengajuan->original_tenggat_waktu) ? 'YA' : 'TIDAK' }}
                                    </p> --}}
                                    <tr id="row-{{ $pengajuan->id }}"
                                        class="@if($pengajuan->original_tenggat_waktu && now()->isAfter($pengajuan->original_tenggat_waktu) && !in_array($pengajuan->status, ['Selesai', 'Selesai (di Loket)', 'Dibatalkan', 'Dikembalikan'])) bg-red-100 hover:bg-red-200 @else hover:bg-gray-50 @endif">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $pengajuan->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $pengajuan->nama_pemohon }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $pengajuan->nomor_hak }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{-- Status Badge Utama --}}
                                            <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full border
                                                @if($pengajuan->status == 'Selesai') bg-green-100 text-green-800 border-green-200
                                                @elseif(in_array($pengajuan->status, ['Berita Acara', 'Dibatalkan', 'Terlambat'])) bg-red-100 text-red-800 border-red-200
                                                @elseif($pengajuan->status == 'Di Arsip') bg-emerald-100 text-emerald-800 border-emerald-200
                                                @elseif($pengajuan->status == 'Di Seksi 1') bg-yellow-100 text-yellow-800 border-yellow-200
                                                @elseif($pengajuan->status == 'Di Seksi 2') bg-orange-100 text-orange-800 border-orange-200
                                                @else bg-gray-100 text-gray-800 border-gray-200
                                                @endif
                                            ">
                                                {{ $pengajuan->status }}
                                            </span>
                                            
                                            {{-- Teks Info Pengirim --}}
                                            @if ($pengajuan->latestRiwayat && $pengajuan->latestRiwayat->user)
                                                <div class="text-[11px] text-gray-400 mt-1 flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    Dikirim: {{ $pengajuan->latestRiwayat->user->name }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $pengajuan->created_at->format('d-m-Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" data-tenggat="{{ $pengajuan->tenggat_waktu }}" data-status="{{ $pengajuan->status }}"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <a href="{{ route(auth()->user()->role->value === 'admin' ? 'admin.dashboard.show' : 'monitor.dashboard.show', ['pengajuan' => $pengajuan]) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                            @if(auth()->user()->role->value === 'admin' && !in_array($pengajuan->status, ['Selesai', 'Dibatalkan', 'Dikembalikan', 'Selesai (di Loket)', 'Berita Acara']))
                                                <button @click="openModal = true; selectedPengajuanId = {{ $pengajuan->id }}; actionUrl = '{{ route('admin.pengajuan.add_time', $pengajuan) }}'" class="text-green-600 hover:text-green-900">Edit Waktu</button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data pengajuan.</td></tr>
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

        {{-- Modal untuk Edit Waktu ditambahkan di sini --}}
        <div x-show="openModal" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="openModal" @click.away="openModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- PERBAIKAN 1: Tambahkan @click.stop agar modal tidak tertutup saat diklik di dalamnya --}}
                <div x-show="openModal" @click.stop class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form :action="actionUrl" method="POST">
                        @csrf
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Ubah Batas Waktu untuk Pengajuan #<span x-text="selectedPengajuanId"></span></h3>
                            <div class="mt-4">
                                <x-input-label for="duration" value="Tambah / Kurangi Berapa Hari?" />
                                {{-- PERBAIKAN 2: Tambahkan x-ref agar tombol bisa mengenali input ini --}}
                                <x-text-input id="duration" type="number" name="duration" x-ref="durationInput" class="mt-1 block w-full" value="1" required />
                                <div class="mt-2 space-x-2">
                                    <x-secondary-button type="button" @click="$refs.durationInput.value = 1">+1 Hari</x-secondary-button>
                                    <x-secondary-button type="button" @click="$refs.durationInput.value = 3">+3 Hari</x-secondary-button>
                                    <x-secondary-button type="button" @click="$refs.durationInput.value = -1">-1 Hari</x-secondary-button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Gunakan angka negatif untuk mengurangi hari.</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <x-primary-button type="submit" class="sm:ms-3">Simpan</x-primary-button>
                            <button @click="openModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- == MODAL BARU UNTUK FILTER == --}}
        <div x-show="openFilterModal" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Background overlay --}}
                <div x-show="openFilterModal" @click.away="openFilterModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                {{-- Centering trick --}}
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Modal Panel --}}
                <div x-show="openFilterModal" @click.stop class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    {{-- Form mengarah ke route export dengan method GET --}}
                    {{-- Kita akan gunakan JavaScript untuk menambahkan parameter bulan/tahun ke URL --}}
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Filter Export Rekap Bulanan</h3>
                        <div class="space-y-4">
                            {{-- Input Bulan --}}
                            <div>
                                <label for="filter_bulan" class="block text-sm font-medium text-gray-700">Bulan</label>
                                <select id="filter_bulan" x-model="filterBulan" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Semua Bulan</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ sprintf('%02d', $m) }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                                    @endfor
                                </select>
                            </div>
                            {{-- Input Tahun --}}
                            <div>
                                <label for="filter_tahun" class="block text-sm font-medium text-gray-700">Tahun</label>
                                <input id="filter_tahun" type="number" x-model="filterTahun" placeholder="Contoh: {{ date('Y') }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        {{-- Tombol Export di dalam Modal --}}
                        <button type="button"
                                {{-- Panggil fungsi JS saat diklik --}}
                                @click="window.location.href = '{{ route('statistics.export') }}' + (filterBulan || filterTahun ? '?' + (filterBulan ? 'bulan=' + filterBulan : '') + (filterBulan && filterTahun ? '&' : '') + (filterTahun ? 'tahun=' + filterTahun : '') : ''); openFilterModal = false;"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm">
                            Export Sekarang
                        </button>
                        <button @click="openFilterModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>