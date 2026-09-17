<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pantau Alur Semua Pengajuan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Pemohon</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Hak</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Desa/Kec</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Saat Ini</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pengajuans as $pengajuan)
                                    <tr id="row-{{ $pengajuan->id }}" class="@if($pengajuan->status === 'Terlambat') bg-red-100 hover:bg-red-200 @else hover:bg-gray-50 @endif">
                                        <td class="px-6 py-4">{{ $pengajuan->id }}</td>
                                        <td class="px-6 py-4">{{ $pengajuan->nama_pemohon }}</td>
                                        <td class="px-6 py-4">{{ $pengajuan->nomor_hak }}</td>
                                        <td class="px-6 py-4">{{ $pengajuan->desa }}, {{ $pengajuan->kecamatan }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                        {{-- Status Badge --}}
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($pengajuan->status == 'Selesai') bg-green-100 text-green-800
                                            @elseif(in_array($pengajuan->status, ['Berita Acara', 'Dibatalkan', 'Terlambat'])) bg-red-100 text-red-800
                                            @elseif($pengajuan->status == 'Di Arsip') bg-green-100 text-green-800
                                            @elseif($pengajuan->status == 'Di Seksi 1') bg-yellow-100 text-yellow-800
                                            @elseif($pengajuan->status == 'Di Seksi 2') bg-orange-100 text-orange-800
                                            @else bg-gray-100 text-gray-800
                                            @endif
                                        ">
                                            {{ $pengajuan->status }}
                                        </span>

                                        {{-- Info Petugas --}}
                                        @if ($pengajuan->latestRiwayat && $pengajuan->latestRiwayat->user)
                                            <div class="text-xs text-gray-500 mt-1">
                                                Oleh: {{ $pengajuan->latestRiwayat->user->name }}
                                                ({{ Str::ucfirst($pengajuan->latestRiwayat->user->role->value) }})
                                            </div>
                                        @endif
                                    </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">Tidak ada pengajuan yang sedang berjalan.</td>
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
