<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Berita Acara') }}
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID Pengajuan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Pemohon</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status BA</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl. Dibuat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat Oleh</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Isi Berita Acara</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($beritaAcaras as $ba)
                                    <tr>
                                        <td class="px-6 py-4">{{ $ba->pengajuan->id ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">{{ $ba->pengajuan->nama_pemohon ?? 'N/A' }}</td>
                                        
                                        {{-- LOGIKA STATUS BERITA ACARA --}}
                                        <td class="px-6 py-4">
                                            @if(isset($ba->pengajuan) && in_array($ba->pengajuan->status, ['Berita Acara', 'BA']))
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800">
                                                    Belum Selesai
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-800">
                                                    Selesai
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4">{{ $ba->created_at->format('d-m-Y H:i') }}</td>
                                        <td class="px-6 py-4">{{ $ba->user->name ?? 'N/A' }} ({{ $ba->user->role->value ?? ''}})</td>
                                        <td class="px-6 py-4">{{ $ba->isi_berita_acara }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        {{-- Jangan lupa ubah colspan menjadi 6 karena ada tambahan kolom baru --}}
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data Berita Acara.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $beritaAcaras->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>