<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Informasi Manajemen Berkas - ATR/BPN Rokan Hulu</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('logo_bpn_welcome.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-bpn-main text-stone-800">

    <div id="preloader" class="fixed top-0 left-0 w-full h-full z-[9999] bg-bpn-main flex justify-center items-center transition-opacity duration-700">
        <div class="flex items-center gap-10">
            <img src="{{ asset('logo_bpn_welcome.png') }}" alt="Logo BPN" class="w-28 h-28 object-contain animate-pulse">
            {{-- Tambahkan logo UNRI di sini, pastikan file-nya ada di folder public --}}
            <img src="{{ asset('LOGO-UNRI.png') }}" alt="Logo Universitas Riau" class="w-28 h-28 object-contain animate-pulse">
        </div>
    </div>

    <nav id="navbar" class="fixed top-0 left-0 w-full z-50 py-4 transition-all duration-500">
        <div class="container mx-auto px-6 lg:px-8 flex justify-between items-center">
            <a class="flex items-center space-x-3" href="#">
                <img src="{{ asset('logo_bpn_welcome.png') }}" alt="Logo BPN" class="h-10 transition-all duration-300">
                <span class="font-montserrat font-bold text-bpn-brown text-lg">SIM A Berkas</span>
            </a>
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-bpn-primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn-bpn-secondary">Log In</a>
                @endauth
            </div>
        </div>
    </nav>

    <header id="hero" class="min-h-screen flex items-center justify-center text-center relative overflow-hidden">
        <div class="container mx-auto px-6 lg:px-8 z-10">
            <img src="{{ asset('logo_bpn_welcome.png') }}" alt="Logo BPN" class="h-28 mx-auto mb-6">
            <h1 class="text-3xl md:text-5xl font-montserrat font-extrabold text-bpn-brown md:whitespace-nowrap">
                Sistem Informasi Manajemen Alur Berkas
            </h1>
            <p class="text-lg text-stone-600 mt-4 max-w-3xl mx-auto">Memantau dan mengelola alur kerja permohonan berkas secara efisien, transparan, dan terintegrasi di lingkungan internal ATR/BPN Rokan Hulu.</p>
        </div>
    </header>

    <main>
        <section id="keunggulan" class="py-20 bg-bpn-cards reveal-section">
            <div class="container mx-auto px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-montserrat font-bold text-bpn-brown">Alur Kerja Digital Terintegrasi</h2>
                    <div class="w-20 h-1 bg-bpn-gold mx-auto mt-4"></div>
                </div>
                <div class="flex flex-wrap justify-center gap-8">
    
                {{-- Card 1: Loket & Arsip --}}
                <div class="w-full md:w-2/5 bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 text-center border border-gray-100">
                    <h3 class="text-2xl font-bold font-montserrat text-bpn-brown mb-4">Registrasi & Kendali Alur</h3>
                    <p class="text-stone-600 leading-relaxed">Proses dimulai dengan input data oleh Loket, disusul verifikasi kelengkapan oleh Arsip untuk menentukan distribusi alur serta menandai dimulainya pemantauan durasi pengerjaan secara sistematis.</p>
                </div>
                
                {{-- Card 2: Seksi 1 --}}
                <div class="w-full md:w-2/5 bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 text-center border border-gray-100">
                    <h3 class="text-2xl font-bold font-montserrat text-bpn-brown mb-4">Validasi Spasial & Tekstual</h3>
                    <p class="text-stone-600 leading-relaxed">Seksi 1 memverifikasi data teknis secara spesifik, memastikan integritas aspek spasial dan tekstual permohonan terpenuhi sebelum berkas diteruskan ke tahap penetapan.</p>
                </div>

                {{-- Card 3: Seksi 2 & Penyelesaian --}}
                <div class="w-full md:w-2/5 bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 text-center border border-gray-100">
                    <h3 class="text-2xl font-bold font-montserrat text-bpn-brown mb-4">Finalisasi & Penetapan</h3>
                    <p class="text-stone-600 leading-relaxed">Tahap pemeriksaan akhir oleh Seksi 2 untuk menjamin validitas seluruh dokumen. Berkas yang sah akan difinalisasi dalam sistem dan dikembalikan ke Loket untuk penyerahan.</p>
                </div>

                {{-- Card 4: Admin & Monitor --}}
                <div class="w-full md:w-2/5 bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 text-center border border-gray-100">
                    <h3 class="text-2xl font-bold font-montserrat text-bpn-brown mb-4">Evaluasi & Manajemen</h3>
                    <p class="text-stone-600 leading-relaxed">Pimpinan dan Admin mengelola parameter global sistem, termasuk pengaturan batas waktu, serta memantau statistik kinerja pegawai secara real-time demi efisiensi layanan.</p>
                </div>
                
            </div>
            </div>
        </section>

        <section id="tim-pengembang" class="py-20 bg-bpn-main reveal-section">
            <div class="container mx-auto px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-montserrat font-bold text-bpn-brown">Pengembang Sistem</h2>
                    <p class="text-stone-600 mt-2">Mahasiswa Universitas Riau</p>
                    <img src="{{ asset('LOGO-UNRI.png') }}" alt="Logo Universitas Riau" class="h-20 mx-auto mt-4">
                </div>
                
                <div class="max-w-xl mx-auto bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 text-center border border-stone-100">
                    
                    <div class="w-24 h-24 bg-stone-100 rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-white shadow-md overflow-hidden">
                        <img src="{{ asset('fauzan.jpeg') }}" alt="Fauzan Sahrul Ramadhan" class="w-full h-full object-cover">
                    </div>

                    <h3 class="text-2xl font-bold font-montserrat text-bpn-brown mb-1">Fauzan Sahrul Ramadhan</h3>
                    <p class="font-semibold text-stone-500 mb-6 tracking-wide">NIM. 2303027239</p>
                    
                    <div class="text-stone-600 space-y-1">
                        <p>D3 Manajemen Informatika</p>
                        <p>Jurusan Ilmu Komputer</p>
                        <p>Fakultas Matematika dan Ilmu Pengetahuan Alam</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <footer class="py-8 border-t border-stone-200">
        <div class="container mx-auto text-center text-stone-600">
            <p>&copy; 2025 - {{ date('Y') }} Sistem Informasi Manajemen Alur Berkas BPN Rokan Hulu.</p>
        </div>
    </footer>

    <script>
        // Script untuk Pre-loader
        window.addEventListener('load', () => {
            const preloader = document.getElementById('preloader');
            if (preloader) {
                setTimeout(() => {
                    preloader.style.opacity = '0';
                    preloader.style.visibility = 'hidden';
                }, 500);
            }
        });

        // Script untuk mengubah warna navbar saat scroll
        const nav = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                nav.classList.add('bg-bpn-cards', 'shadow-md', 'py-2');
                nav.classList.remove('py-4');
            } else {
                nav.classList.remove('bg-bpn-cards', 'shadow-md', 'py-2');
                nav.classList.add('py-4');
            }
        });

        // Script untuk efek fade-in saat scroll
        const sections = document.querySelectorAll('.reveal-section');
        const observer = new IntersectionObserver(function(entries, observer) {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.1 });
        sections.forEach(section => {
            observer.observe(section);
        });
    </script>
</body>
</html>