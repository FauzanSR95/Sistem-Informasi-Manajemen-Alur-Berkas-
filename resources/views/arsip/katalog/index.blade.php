<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Katalog Arsip Digital') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- KOTAK INFORMASI SINGKATAN BARU --}}
            <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-4 text-sm shadow-sm">
                <p class="font-semibold mb-2">Informasi Singkatan Dokumen:</p>
                <ul class="flex flex-wrap gap-x-6 gap-y-1">
                    <li><strong>HM</strong> = Hak Milik</li>
                    <li><strong>SU</strong> = Surat Ukur</li>
                    <li><strong>BT</strong> = Buku Tanah</li>
                    <li><strong>HT</strong> = Hak Tanggungan</li>
                    <li><strong>HGB</strong> = Hak Guna Bangunan</li>
                    <li><strong>HGU</strong> = Hak Guna Usaha</li>
                </ul>
            </div>

            <div class="mb-4 flex justify-between">
                <a href="{{ route('arsip.katalog.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Tambah Data Arsip
                </a>
                <form action="{{ route('arsip.katalog.index') }}" method="GET">
                    <x-text-input type="text" name="search" placeholder="Cari HM, Jenis, Desa, Rak..." value="{{ request('search') }}" class="w-64" />
                    <x-primary-button class="ms-2">Cari</x-primary-button>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                     @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-3 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor HM</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Desa/Kecamatan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rak</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Baris</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($arsipRaks as $arsip)
                                    <tr>
                                        {{-- FONT SEMIBOLD DIHAPUS DI SINI --}}
                                        <td class="px-6 py-4 text-gray-800">{{ $arsip->nomor_hm }}</td>
                                        <td class="px-6 py-4">{{ $arsip->jenis_sertifikat }}</td>
                                        <td class="px-6 py-4">{{ $arsip->desa }}, {{ $arsip->kecamatan }}</td>
                                        <td class="px-6 py-4">{{ $arsip->lokasi_rak }}</td>
                                        <td class="px-6 py-4">{{ $arsip->lokasi_baris }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('arsip.katalog.edit', $arsip) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <form action="{{ route('arsip.katalog.destroy', $arsip) }}" method="POST" class="inline ml-4" onsubmit="return confirm('Anda yakin ingin menghapus data ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data katalog arsip.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                     <div class="mt-4">
                        {{ $arsipRaks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>