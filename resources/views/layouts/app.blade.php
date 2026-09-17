<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="icon" href="{{ asset('logo_bpn_welcome.png') }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
    @media print {
        /* Sembunyikan sidebar, header, dan tombol-tombol saat dicetak */
        aside, header, button, .no-print { 
            display: none !important; 
        }
        /* Buat area konten menjadi putih bersih memenuhi kertas */
        main { 
            background-color: white !important; 
            padding: 0 !important; 
            margin: 0 !important; 
        }
        /* Hilangkan bayangan/shadow agar terlihat seperti kertas rata */
        .shadow-sm, .shadow {
            box-shadow: none !important;
            border: 1px solid #e5e7eb !important;
        }
    }
</style>
    </head>
    <body class="font-sans antialiased">
        {{-- Alpine.js untuk kontrol state sidebar --}}
        <div x-data="{ sidebarOpen: window.innerWidth > 1024 }" class="flex h-screen bg-gray-100">

            <aside
                class="flex-shrink-0 w-64 bg-white border-r transition-all duration-300"
                :class="{ '-ml-64': !sidebarOpen }"
            >
                @include('layouts.navigation')
            </aside>

            <div class="flex-1 flex flex-col overflow-hidden">
                <header class="bg-white shadow z-10">
                    <div class="w-full mx-auto py-3 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                        <div class="flex items-center">
                            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none lg:hidden mr-4">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </button>
                            @if (isset($header))
                                <div class="min-w-0">
                                    {{ $header }}
                                </div>
                            @endif
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open; markNotificationsAsRead();" class="relative inline-flex items-center p-2 text-gray-500 hover:text-gray-700 focus:outline-none">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    <span id="notification-badge" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full hidden">0</span>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg overflow-hidden z-20" style="display: none;">
                                    <div class="py-2">
                                        <div id="notification-list">
                                            <p class="text-gray-700 px-4 py-2 text-sm">Memuat...</p>
                                        </div>
                                        <a href="{{ route('riwayat.index') }}" class="block text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 text-sm font-bold">Lihat Selengkapnya</a>
                                    </div>
                                </div>
                            </div>
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                        <div>{{ Auth::user()->name }}</div>
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </header>

                <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 sm:p-8
                    @switch(auth()->user()->role->value ?? '')
                        @case('admin') bg-blue-100 @break
                        @case('monitor') bg-gray-200 @break
                        @case('loket') bg-purple-100 @break
                        @case('arsip') bg-green-100 @break
                        @case('seksi1') bg-yellow-100 @break
                        @case('seksi2') bg-orange-100 @break
                        @default bg-gray-100
                    @endswitch
                ">
                    {{ $slot }}
                </main>
            </div>
        </div>

        {{-- ELEMEN UNTUK NOTIFIKASI STANDARD --}}
        <div id="notification-popup" class="hidden fixed bottom-5 right-5 bg-green-500 text-white py-3 px-5 rounded-lg shadow-lg text-lg animate-bounce z-50">
            🔔 Ada tugas baru masuk!
        </div>
        <audio id="notification-sound" src="{{ asset('sounds/notification.mp3') }}" preload="auto"></audio>

        <div id="overdue-notification-popup" class="hidden fixed z-50 inset-0 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen text-center">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-700 opacity-75"></div>
                </div>
                <div class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg w-full p-6">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
                            <svg class="h-10 w-10 text-red-600" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h3 class="mt-4 text-2xl leading-6 font-bold text-gray-900">PERINGATAN!</h3>
                        <div class="mt-2">
                            <p class="text-lg text-gray-600">Ada tugas yang telah melewati batas waktu pengerjaan.</p>
                            <p class="text-sm text-gray-500 mt-1">Silakan segera periksa dashboard Anda.</p>
                        </div>
                    </div>
                    <div class="mt-6">
                        <button id="close-overdue-popup" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none">
                            Saya Mengerti
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <audio id="overdue-notification-sound" src="{{ asset('sounds/warning_pt2.mp3') }}" preload="auto"></audio>

        {{-- ========================================================================= --}}
        {{-- UPDATE BARU: TOAST NOTIFIKASI KHUSUS ADMIN & MONITOR --}}
        {{-- ========================================================================= --}}
        @if(auth()->check() && in_array(auth()->user()->role->value ?? '', ['admin', 'monitor']))
            <div id="admin-toast-popup" class="hidden fixed bottom-5 right-5 z-[100] max-w-sm w-full bg-white border-l-4 border-red-500 shadow-2xl rounded-lg pointer-events-auto transform transition-all duration-500 translate-y-10 opacity-0">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 pt-0.5">
                            <svg class="h-6 w-6 text-red-500 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="ml-3 w-0 flex-1">
                            <p class="text-sm font-bold text-gray-900">Peringatan Eksekutif!</p>
                            <p id="admin-toast-text" class="mt-1 text-sm text-gray-600">
                                </p>
                            <div class="mt-3 flex space-x-4">
                                <a href="{{ route('riwayat.index') }}" class="text-xs font-bold text-red-600 hover:text-red-800 bg-red-50 px-2 py-1 rounded transition">Lihat Riwayat Penuh &rarr;</a>
                            </div>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button onclick="closeAdminToast()" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500">
                                <span class="sr-only">Tutup</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    {{-- SEMUA SCRIPT APLIKASI --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const countdownElements = document.querySelectorAll('[data-tenggat]');
            countdownElements.forEach(function (element) {
                const status = element.dataset.status;
                const tenggat = element.dataset.tenggat;
                const finalStates = ['Selesai', 'Dibatalkan', 'Berita Acara', 'BA', 'Terlambat'];

                if (finalStates.includes(status) || !tenggat) {
                    if(status === 'Terlambat'){
                        element.innerHTML = '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Terlambat</span>';
                    } else {
                        element.textContent = '-';
                    }
                    return;
                }

                const tenggatWaktu = new Date(tenggat).getTime();

                const interval = setInterval(function () {
                    const sekarang = new Date().getTime();
                    const selisih = tenggatWaktu - sekarang;

                    if (selisih < 0) {
                        element.innerHTML = '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Terlambat</span>';
                        clearInterval(interval);
                        return;
                    }

                    const d = Math.floor(selisih / (1000 * 60 * 60 * 24));
                    const h = Math.floor((selisih % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const m = Math.floor((selisih % (1000 * 60 * 60)) / (1000 * 60));
                    const s = Math.floor((selisih % (1000 * 60)) / 1000);
                    element.textContent = `${d}h ${h}j ${m}m ${s}d`;
                }, 1000);
            });
        });

        @auth
            const badge = document.getElementById('notification-badge');
            const list = document.getElementById('notification-list');
            let initialLoad = true;

            function fetchUnreadNotifications() {
                fetch('{{ route("notifications.unread") }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.count > 0) {
                            badge.textContent = data.count;
                            badge.classList.remove('hidden');
                        } else {
                            badge.classList.add('hidden');
                        }
                        list.innerHTML = '';
                        if (data.notifications.length > 0) {
                            data.notifications.forEach(notif => {
                                const item = document.createElement('div');
                                item.className = 'px-4 py-2 text-sm text-gray-700 border-b';
                                item.innerHTML = `<p class="font-bold">Pengajuan #${notif.pengajuan.id}</p><p>${notif.catatan}</p>`;
                                list.appendChild(item);
                            });
                        } else {
                            const noNotif = document.createElement('p');
                            noNotif.className = 'px-4 py-2 text-sm text-gray-500';
                            noNotif.textContent = 'Tidak ada notifikasi baru.';
                            list.appendChild(noNotif);
                        }
                        initialLoad = false;
                    });
            }

            function markNotificationsAsRead() {
                if (badge.textContent > 0 && !initialLoad) {
                    fetch('{{ route("notifications.mark_as_read") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).then(() => {
                        badge.classList.add('hidden');
                    });
                }
            }

            //notifikasi role operasional
            function checkSimpleNotifications() {
                fetch('{{ route("notifications.check") }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.new_task) {
                            const popup = document.getElementById('notification-popup');
                            popup.classList.remove('hidden');
                            document.getElementById('notification-sound').play();
                            setTimeout(() => popup.classList.add('hidden'), 5000);
                        }
                    });
            }

            function checkOverdueNotifications() {
                fetch('{{ route("notifications.check_overdue") }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.overdue_task) {
                            const popup = document.getElementById('overdue-notification-popup');
                            popup.classList.remove('hidden');
                            document.getElementById('overdue-notification-sound').play();
                        }
                    });
            }

            document.getElementById('close-overdue-popup').addEventListener('click', function() {
                document.getElementById('overdue-notification-popup').classList.add('hidden');
            });

            @if(in_array(auth()->user()->role->value ?? '', ['admin', 'monitor']))
                let adminToastTimeout;

                function closeAdminToast() {
                    const popup = document.getElementById('admin-toast-popup');
                    popup.classList.remove('translate-y-0', 'opacity-100');
                    popup.classList.add('translate-y-10', 'opacity-0');
                    setTimeout(() => popup.classList.add('hidden'), 300);
                }

                //durasi melayang admin
                function showAdminToast(terlambat, ba) {
                    const popup = document.getElementById('admin-toast-popup');
                    const textEl = document.getElementById('admin-toast-text');
                    
                    let message = `Terdapat `;
                    if(terlambat > 0) message += `<b class="text-red-600">${terlambat} Berkas Terlambat</b> `;
                    if(terlambat > 0 && ba > 0) message += `dan `;
                    if(ba > 0) message += `<b class="text-red-600">${ba} Berita Acara</b> `;
                    message += `yang belum terselesaikan.`;

                    textEl.innerHTML = message;
                    
                    popup.classList.remove('hidden');
                    setTimeout(() => {
                        popup.classList.remove('translate-y-10', 'opacity-0');
                        popup.classList.add('translate-y-0', 'opacity-100');
                    }, 50);

                    clearTimeout(adminToastTimeout);
                    adminToastTimeout = setTimeout(() => {
                        closeAdminToast();
                    }, 7000);
                }

                // Fungsi untuk meminta data asli dari database dengan Memory (sessionStorage)
                function checkAdminMonitorNotifications() {
                    fetch('{{ route("notifications.admin_summary") }}')
                        .then(response => response.json())
                        .then(data => {
                            if (data && (data.terlambat > 0 || data.ba > 0)) {
                                
                                // Membuat "kode unik" berdasarkan jumlah berkas
                                const dataState = data.terlambat + '-' + data.ba;
                                
                                // Cek apakah notifikasi dengan jumlah ini SUDAH pernah muncul di sesi ini
                                if(sessionStorage.getItem('admin_toast_state') !== dataState) {
                                    
                                    // ===== UPDATE: MAINKAN SUARA =====
                                    const sound = document.getElementById('notification-sound');
                                    if(sound) {
                                        sound.currentTime = 0; // Reset suara ke awal
                                        sound.play().catch(e => console.log('Suara dicegah oleh browser'));
                                    }
                                    // =================================

                                    showAdminToast(data.terlambat, data.ba);
                                    
                                    // Simpan ke ingatan browser agar tidak muncul berulang saat pindah halaman
                                    sessionStorage.setItem('admin_toast_state', dataState);
                                }
                                
                            } else {
                                // Jika sudah tidak ada masalah, bersihkan ingatan
                                sessionStorage.removeItem('admin_toast_state');
                            }
                        })
                        .catch(error => console.error('Gagal mengambil data notifikasi:', error));
                }

                // Panggil satu kali saat halaman pertama kali dimuat
                checkAdminMonitorNotifications();

                @endif
                
            fetchUnreadNotifications();
            setInterval(() => {
                fetchUnreadNotifications();
                checkSimpleNotifications();
                checkOverdueNotifications();

                // Pengecekan khusus admin di dalam interval
                @if(in_array(auth()->user()->role->value ?? '', ['admin', 'monitor']))
                    checkAdminMonitorNotifications();
                @endif
            }, 30000);
        @endauth
    </script>
</body>
</html>