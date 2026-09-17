<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Statistik') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- TOMBOL CETAK LAPORAN --}}
            <!-- <div class="flex justify-end mb-4">
                <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 flex items-center shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Laporan
                </button>
            </div> -->

            {{-- KARTU RINGKASAN (SCORECARDS) --}}
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500 p-4">
                    <dt class="text-sm font-medium text-gray-500">Total Berkas Masuk</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalMasuk }}</dd>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500 p-4">
                    <dt class="text-sm font-medium text-gray-500">Total Selesai</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalSelesai }}</dd>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-yellow-500 p-4">
                    <dt class="text-sm font-medium text-gray-500">Sedang Berjalan</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalBerjalan }}</dd>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-red-500 p-4 bg-red-50">
                    <dt class="text-sm font-bold text-red-600 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Terlambat (Lewat SOP)
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-red-700">{{ $totalTerlambat }}</dd>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-orange-500">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 truncate">Total Berita Acara</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ $totalBeritaAcara }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- CHART KINERJA PEGAWAI (STACKED BAR) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg mb-4">Statistik Jumlah Berkas (Tepat Waktu vs Terlambat)</h3>
                    <p class="text-sm text-gray-500 mb-4">Menampilkan perbandingan absolut jumlah berkas yang diselesaikan tepat waktu dibandingkan yang terlambat untuk setiap bagian operasional.</p>
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>

            {{-- CHART JUMLAH BERKAS SELESAI --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg mb-4">Jumlah Berkas Masuk vs Selesai per Bulan</h3>
                    <canvas id="completedChart"></canvas>
                </div>
            </div>

            {{-- CHART RATA-RATA WAKTU --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg">Durasi Rata-Rata Berkas per Seksi</h3>
                    <p class="text-sm text-gray-500 mb-4">Menunjukkan rata-rata waktu yang dihabiskan berkas di setiap seksi.</p>

                    <div class="flex items-center space-x-4 mb-4 text-sm text-gray-600">
                        <div class="flex items-center">
                            <span class="w-4 h-4 inline-block mr-2 rounded" style="background-color: rgba(34, 197, 94, 0.6);"></span>
                            <span>Arsip</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-4 h-4 inline-block mr-2 rounded" style="background-color: rgba(234, 179, 8, 0.6);"></span>
                            <span>Seksi 1</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-4 h-4 inline-block mr-2 rounded" style="background-color: rgba(249, 115, 22, 0.6);"></span>
                            <span>Seksi 2</span>
                        </div>
                    </div>

                    <canvas id="avgTimeChart"></canvas>
                </div>
            </div>

            {{-- TABEL DETAIL KINERJA INDIVIDU --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg mb-1">Tingkat Ketepatan Waktu Kinerja Pegawai (Skor)</h3>
                    <p class="text-sm text-gray-500 mb-4">Persentase keberhasilan staf dalam menyelesaikan berkas sesuai dengan Standar Operasional Prosedur (SOP).</p>
                    
                    {{-- LEGENDA KINERJA DIPINDAHKAN KESINI --}}
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mb-4 text-sm text-gray-600 bg-gray-50 p-3 rounded-md border border-gray-100">
                        <span class="font-semibold text-gray-700 mr-2">Indikator Skor:</span>
                        <div class="flex items-center">
                            <span class="w-3 h-3 inline-block mr-1 rounded-full bg-green-500"></span>
                            <span>Bagus (>= 75%)</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 inline-block mr-1 rounded-full bg-yellow-400"></span>
                            <span>Cukup (50% - 74%)</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 inline-block mr-1 rounded-full bg-red-500"></span>
                            <span>Kurang (< 50%)</span>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Pegawai (Role)</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Evaluasi (SOP)</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tepat Waktu</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Terlambat</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Skor Kinerja</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase border-l-2">Tertunda (BA)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($employeePerformanceData as $emp)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $emp['name'] }}</td>
                                        
                                        {{-- Alur Matematika yang Runtut --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center font-bold">{{ $emp['total'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-center font-bold">{{ $emp['on_time'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 text-center font-bold">{{ $emp['late'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full shadow-sm" 
                                                  style="background-color: {{ str_replace('0.6', '0.15', $emp['color']) }}; color: {{ str_replace('0.6', '1', $emp['color']) }}; border: 1px solid {{ str_replace('0.6', '0.5', $emp['color']) }};">
                                                {{ $emp['score'] }}%
                                            </span>
                                        </td>
                                        
                                        {{-- Informasi BA Dipisah di Ujung --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-orange-600 text-center font-bold border-l-2 bg-orange-50/30">
                                            {{ $emp['ba'] }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada data riwayat kinerja pegawai.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk library Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Data dari Controller
        const labelsCompleted = @json($labelsCompleted);
        const valuesCompleted = @json($valuesCompleted);
        const valuesIncoming = @json($valuesIncoming);
        
        const avgLabels = @json($avgLabels);
        const avgValues = @json($avgValues);
        const avgColors = @json($avgColors);
        const avgBorderColors = @json($avgBorderColors);
        
        const employeePerformanceData = @json($employeePerformanceData);

        // =================== CHART KINERJA (STACKED BAR) ===================
        const performanceLabels = employeePerformanceData.map(item => item.name);
        const performanceOnTime = employeePerformanceData.map(item => item.on_time);
        const performanceLate = employeePerformanceData.map(item => item.late);

        const ctxPerformance = document.getElementById('performanceChart');
        new Chart(ctxPerformance, {
            type: 'bar',
            data: {
                labels: performanceLabels,
                datasets: [
                    {
                        label: 'Tepat Waktu',
                        data: performanceOnTime,
                        backgroundColor: 'rgba(34, 197, 94, 0.7)', // Hijau
                        borderWidth: 1
                    },
                    {
                        label: 'Terlambat',
                        data: performanceLate,
                        backgroundColor: 'rgba(239, 68, 68, 0.7)', // Merah
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        // =================== CHART MASUK VS SELESAI (GROUPED BAR) ===================
        const ctxCompleted = document.getElementById('completedChart');
        new Chart(ctxCompleted, {
            type: 'bar',
            data: {
                labels: labelsCompleted,
                datasets: [
                    {
                        label: 'Berkas Masuk',
                        data: valuesIncoming,
                        backgroundColor: 'rgba(59, 130, 246, 0.7)', // Biru
                        borderWidth: 1
                    },
                    {
                        label: 'Berkas Selesai',
                        data: valuesCompleted,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)', // Teal
                        borderWidth: 1
                    }
                ]
            },
            options: { scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });

        // =================== CHART RATA-RATA WAKTU (FORMAT HARI & JAM) ===================
        const ctxAvgTime = document.getElementById('avgTimeChart');
        new Chart(ctxAvgTime, {
            type: 'bar',
            data: {
                labels: avgLabels,
                datasets: [{
                    label: 'Durasi Pengerjaan',
                    data: avgValues,
                    backgroundColor: avgColors,
                    borderColor: avgBorderColors,
                    borderWidth: 1
                }]
            },
            options: { 
                scales: { y: { beginAtZero: true } },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let totalHours = context.parsed.y;
                                if (totalHours === 0) return ' 0 Jam';
                                
                                // Matematika sederhana mengubah desimal jam ke Hari dan Jam
                                let days = Math.floor(totalHours / 24);
                                let hours = Math.round(totalHours % 24);
                                
                                let result = '';
                                if (days > 0) result += days + ' Hari ';
                                if (hours > 0) result += hours + ' Jam';
                                if (days === 0 && hours === 0) result = '< 1 Jam';
                                
                                return ' Rata-rata: ' + result.trim();
                            }
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>