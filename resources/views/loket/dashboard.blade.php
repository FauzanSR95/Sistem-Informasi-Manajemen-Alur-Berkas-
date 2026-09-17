<x-app-layout> 
    <x-slot name="header"> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight"> 
            {{ __('Dashboard Loket') }} 
        </h2> 
    </x-slot> 
 
    <div class="py-12"> 
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"> 
            <div class="bg-white overflow-hidden shadow-md sm:rounded-xl border border-gray-200"> 
                <div class="p-6 text-gray-900"> 
                    <h3 class="font-bold text-lg">Formulir Pengajuan Berkas Baru</h3> 
 
                    @if (session('success')) 
                        <div class="mb-4 font-medium text-sm text-green-600"> 
                            {{ session('success') }} 
                        </div> 
                    @endif 
 
                    {{-- Tambahkan id="pengajuan-form" dan novalidate --}} 
                    <form id="pengajuan-form" method="POST" action="{{ route('loket.store') }}" class="mt-6 space-y-6" novalidate> 
                        @csrf 
 
                        <div> 
                            <x-input-label for="nama_pemohon" :value="__('Nama Pemohon')" /> 
                            <x-text-input id="nama_pemohon" name="nama_pemohon" type="text" class="mt-1 block w-full" autofocus /> 
                            <p id="nama_pemohon_error" class="text-sm text-red-600 mt-2"></p> 
                        </div> 
 
                        <div> 
                            <x-input-label for="nomor_hak" :value="__('Nomor Hak')" /> 
                            <x-text-input id="nomor_hak" name="nomor_hak" type="number" class="mt-1 block w-full" /> 
                            <p id="nomor_hak_error" class="text-sm text-red-600 mt-2"></p> 
                        </div> 
 
                        <div> 
                            <x-input-label for="kecamatan" :value="__('Kecamatan')" /> 
                            <select id="kecamatan" name="kecamatan" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" required>
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                            <p id="kecamatan_error" class="text-sm text-red-600 mt-2"></p> 
                        </div> 

                        <div> 
                            <x-input-label for="desa" :value="__('Desa / Kelurahan')" /> 
                            <select id="desa" name="desa" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" disabled required>
                                <option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>
                            </select>
                            <p id="desa_error" class="text-sm text-red-600 mt-2"></p> 
                        </div>
 
                        <div> 
                            <x-input-label for="catatan_loket" :value="__('Catatan (Jika Ada)')" /> 
                            <textarea id="catatan_loket" name="catatan_loket" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"></textarea> 
                        </div> 
 
                        <div class="flex items-center gap-4"> 
                            <x-primary-button>{{ __('Kirim ke Arsip') }}</x-primary-button> 
                        </div> 
                    </form> 
                </div> 
            </div> 
        </div> 
    </div> 
 
    <script> 
        document.getElementById('pengajuan-form').addEventListener('submit', function(event) { 
            // Hentikan pengiriman form untuk diperiksa dulu 
            event.preventDefault();  
             
            let isValid = true; 
            const requiredFields = ['nama_pemohon', 'nomor_hak', 'desa', 'kecamatan']; 
             
            requiredFields.forEach(function(fieldId) { 
                const input = document.getElementById(fieldId); 
                const errorElement = document.getElementById(fieldId + '_error'); 
                 
                // Bersihkan error dan style merah yang lama 
                errorElement.textContent = ''; 
                input.classList.remove('border-red-500'); 
 
                // Cek jika input kosong 
                if (input.value.trim() === '') { 
                    isValid = false; 
                    errorElement.textContent = 'Kolom ini wajib diisi.'; // Pesan Bahasa Indonesia 
                    input.classList.add('border-red-500'); // Beri border merah 
                } 
            }); 
 
            // Jika semua kolom sudah terisi, kirim form 
            if (isValid) { 
                this.submit(); 
            } 
        }); 

        
        // DATA WILAYAH KABUPATEN ROKAN HULU (Sudah dibersihkan dari kata Desa/Kelurahan)
        const dataWilayah = {
            "Bangun Purba": ["Bangun Purba", "Bangun Purba Barat", "Bangun Purba Timur Jaya", "Pasir Agung", "Pasir Intan", "Rambah Jaya", "Tangun"],
            "Bonai Darussalam": ["Bonai", "Kasang Munai", "Kasang Padang", "Pauh", "Rawa Makmur", "Sontang", "Teluk Sono"],
            "Kabun": ["Aliantan", "Batu Langkah Besar", "Bencah Kesuma", "Giti", "Kabun", "Koto Ranah"],
            "Kepenuhan Hulu": ["Kepayan", "Kepenuhan Hulu", "Kepenuhan Jaya", "Muara Jaya", "Pekan Tebih"],
            "Kepenuhan": ["Kepenuhan Tengah", "Kepenuhan Barat", "Kepenuhan Barat Mulya", "Kepenuhan Barat Sei Rokan Jaya", "Kepenuhan Baru", "Kepenuhan Hilir", "Kepenuhan Raya", "Kepenuhan Timur", "Rantau Binuang Sakti", "Ulak Patian"],
            "Kunto Darussalam": ["Kota Lama", "Bagan Tujuh", "Bukit Intan Makmur", "Kota Baru", "Kota Intan", "Kota Raya", "Muara Dilam", "Pasir Indah", "Pasir Luhur", "Sungai Kuti"],
            "Pagaran Tapah Darussalam": ["Kembang Damai", "Pagaran Tapah", "Sangkir Jaya"],
            "Pendalian IV Koto": ["Air Panas", "Bengkolan Salak", "Pendalian", "Sei Kandis", "Suligi"],
            "Rambah": ["Pasir Pengaraian", "Babussalam", "Koto Tinggi", "Menaming", "Pasir Baru", "Pasir Maju", "Pematang Berangan", "Rambah Tengah Barat", "Rambah Tengah Hilir", "Rambah Tengah Hulu", "Rambah Tengah Utara", "Sialang Jaya", "Suka Maju", "Tanjung Belit"],
            "Rambah Hilir": ["Lubuk Krapat", "Muara Musu", "Pasir Jaya", "Pasir Utama", "Rambah", "Rambah Hilir", "Rambah Hilir Tengah", "Rambah Hilir Timur", "Rambah Muda", "Sejati", "Serombou Indah", "Sungai Dua Indah", "Sungai Sitolang"],
            "Rambah Samo": ["Karya Mulya", "Langkitin", "Lubuk Bilang", "Lubuk Napal", "Marga Mulya", "Masda Makmur", "Rambah Baru", "Rambah Samo", "Rambah Samo Barat", "Rambah Utama", "Sungai Kuning", "Sungai Salak", "Surau Gading", "Teluk Aur"],
            "Rokan IV Koto": ["Rokan", "Alahan", "Cipang Kanan", "Cipang Kiri Hilir", "Cipang Kiri Hulu", "Lubuk Bendahara", "Lubuk Bendahara Timur", "Lubuk Betung", "Pemandang", "Rokan Koto Ruang", "Rokan Timur", "Sikebau Jaya", "Tanjung Medan", "Tibawan"],
            "Tambusai": ["Tambusai Tengah", "Batang Kumu", "Batas", "Lubuk Soting", "Rantau Panjang", "Sialang Rindang", "Suka Maju", "Sungai Kumango", "Tali Kumain", "Tambusai Barat", "Tambusai Timur", "Tingkok"],
            "Tambusai Utara": ["Bangun Jaya", "Mahato", "Mahato Sakti", "Mekar Jaya", "Pagar Mayang", "Payung Sekaki", "Rantau Sakti", "Simpang Harapan", "Suka Damai", "Tambusai Utara", "Tanjung Medan"],
            "Tandun": ["Bono Tapung", "Dayo", "Koto Tandun", "Kumain", "Puo Raya", "Sungai Kuning", "Tandun", "Tandun Barat", "Tapung Jaya"],
            "Ujung Batu": ["Ujung Batu", "Ngaso", "Pematang Tebih", "Suka Damai", "Ujung Batu Timur"]
        };

        const kecamatanSelect = document.getElementById('kecamatan');
        const desaSelect = document.getElementById('desa');

        // Isi otomatis dropdown Kecamatan saat halaman dimuat
        for (let kec in dataWilayah) {
            let option = document.createElement("option");
            option.value = kec;
            option.text = kec;
            kecamatanSelect.appendChild(option);
        }

        // Logika ketika Kecamatan dipilih
        kecamatanSelect.addEventListener('change', function() {
            let selectedKecamatan = this.value;
            
            // Kosongkan pilihan desa yang lama
            desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
            
            if (selectedKecamatan) {
                desaSelect.disabled = false; // Buka gembok dropdown desa
                let listDesa = dataWilayah[selectedKecamatan];
                
                // Urutkan desa berdasarkan abjad (A-Z) agar rapi
                listDesa.sort().forEach(function(desa) {
                    let option = document.createElement("option");
                    option.value = desa;
                    option.text = desa;
                    desaSelect.appendChild(option);
                });
            } else {
                desaSelect.innerHTML = '<option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>';
                desaSelect.disabled = true; // Gembok kembali jika kecamatan tidak dipilih
            }
        });
    
    </script> 
</x-app-layout>