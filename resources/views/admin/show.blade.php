<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Pengajuan #') . $pengajuan->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900">Informasi Berkas</h3>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nama Pemohon</p>
                            <p class="font-semibold">{{ $pengajuan->nama_pemohon }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nomor Hak</p>
                            <p class="font-semibold">{{ $pengajuan->nomor_hak }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Desa</p>
                            <p class="font-semibold">{{ $pengajuan->desa }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Kecamatan</p>
                            <p class="font-semibold">{{ $pengajuan->kecamatan }}</p>
                        </div>
                        
                        {{-- UPDATE 1: Status Saat Ini --}}
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Status Saat Ini</p>
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
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $statusColor }}">
                                {{ $pengajuan->status }}
                            </span>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500">Dibuat Oleh Loket</p>
                            <p class="font-semibold">{{ $pengajuan->userLoket->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Riwayat Alur Berkas</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dikerjakan Oleh</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                             @forelse ($pengajuan->riwayat as $riwayat)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $riwayat->created_at->format('d-m-Y H:i') }}</td>
                                    
                                    {{-- UPDATE 2: Status Riwayat --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $riwayatColor = match($riwayat->status_baru) {
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
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $riwayatColor }}">
                                            {{ $riwayat->status_baru }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $riwayat->user->name ?? 'N/A' }} ({{ $riwayat->user->role ?? '' }})</td>
                                    <td class="px-6 py-4">{{ $riwayat->catatan }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada riwayat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>