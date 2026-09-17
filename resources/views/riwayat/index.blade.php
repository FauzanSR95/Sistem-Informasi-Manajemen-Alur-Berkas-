<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Notifikasi dan Aktivitas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @forelse ($riwayat as $item)
                        <div class="border-l-4 @if(is_null($item->read_at) && $item->status_baru != 'Input') border-indigo-500 bg-indigo-50 @else border-gray-200 @endif p-4 mb-4">
                            <p class="font-bold">
                                @if ($item->pengajuan)
                                    Pengajuan #{{ $item->pengajuan->id }} ({{ $item->pengajuan->nama_pemohon }})
                                @endif
                            </p>
                            <p>{{ $item->catatan }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                Oleh: {{ $item->user->name ?? 'Sistem' }} - {{ $item->created_at->diffForHumans() }}
                            </p>
                        </div>
                    @empty
                        <p>Tidak ada riwayat aktivitas.</p>
                    @endforelse

                    <div class="mt-4">
                        {{ $riwayat->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>