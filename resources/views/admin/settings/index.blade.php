<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Batas Waktu') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-3 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.settings.store') }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="deadline_arsip" value="Batas Waktu Role Arsip (Hari)" />
                                <x-text-input id="deadline_arsip" type="number" name="deadline_arsip" class="mt-1 block w-full" 
                                              value="{{ $settings['deadline_arsip'] ?? 3 }}" required />
                            </div>
                            <div>
                                <x-input-label for="deadline_seksi1" value="Batas Waktu Role Seksi 1 (Hari)" />
                                <x-text-input id="deadline_seksi1" type="number" name="deadline_seksi1" class="mt-1 block w-full" 
                                              value="{{ $settings['deadline_seksi1'] ?? 3 }}" required />
                            </div>
                            <div>
                                <x-input-label for="deadline_seksi2" value="Batas Waktu Role Seksi 2 (Hari)" />
                                <x-text-input id="deadline_seksi2" type="number" name="deadline_seksi2" class="mt-1 block w-full" 
                                              value="{{ $settings['deadline_seksi2'] ?? 3 }}" required />
                            </div>
                        </div>
                        <div class="mt-6">
                            <x-primary-button>Simpan Pengaturan</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>