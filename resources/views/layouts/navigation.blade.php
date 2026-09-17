{{-- Container untuk seluruh sidebar --}}
<div class="flex flex-col h-full text-gray-700">
    <div class="flex-shrink-0 flex items-center justify-center px-4 py-6">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
            <img src="{{ asset('logo_bpn_welcome.png') }}" alt="Logo BPN" class="h-10 w-auto">
            <span class="font-bold text-gray-800 text-lg">SIM A Berkas</span>
        </a>
    </div>

    <nav class="flex-1 overflow-y-auto px-4 space-y-1">
        <x-side-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            {{ __('Dashboard') }}
        </x-side-nav-link>

        @if(in_array(auth()->user()->role->value, ['loket', 'arsip', 'seksi1', 'seksi2']))
            @if(auth()->user()->role->value === 'arsip')
                <x-side-nav-link :href="route('arsip.katalog.index')" :active="request()->routeIs('arsip.katalog.index')">
                    {{ __('Katalog Arsip') }}
                </x-side-nav-link>
            @endif
            <x-side-nav-link :href="route(auth()->user()->role->value . '.alur')" :active="request()->routeIs(auth()->user()->role->value . '.alur')">
                {{ __('Alur Pengajuan') }}
            </x-side-nav-link>
        @endif

        <x-side-nav-link :href="route('berita-acara.index')" :active="request()->routeIs('berita-acara.index')">
            {{ __('Berita Acara') }}
        </x-side-nav-link>

        <x-side-nav-link :href="route('riwayat.index')" :active="request()->routeIs('riwayat.index')">
            {{ __('Riwayat') }}
        </x-side-nav-link>

        @if(in_array(auth()->user()->role->value, ['admin', 'monitor']))
             <hr class="my-4 border-gray-200">
             <p class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Manajemen</p>

             <x-side-nav-link :href="route('statistics.index')" :active="request()->routeIs('statistics.index')">
                {{ __('Statistik') }}
            </x-side-nav-link>

            <x-side-nav-link :href="route('informasi-berkas.index')" :active="request()->routeIs('informasi-berkas.index')">
                {{ __('Informasi Berkas') }}
            </x-side-nav-link>

            @if(auth()->user()->role->value === 'admin')
                <x-side-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    {{ __('Manajemen User') }}
                </x-side-nav-link>
                <x-side-nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.index')">
                    {{ __('Pengaturan') }}
                </x-side-nav-link>
            @endif
        @endif
    </nav>
</div>
