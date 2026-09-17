<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Data Arsip Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('arsip.katalog.store') }}" class="mt-6 space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="nomor_hm" value="Nomor HM (Hak Milik)" />
                                <x-text-input id="nomor_hm" name="nomor_hm" type="text" class="mt-1 block w-full {{ $errors->has('nomor_hm') ? 'border-red-500' : '' }}" placeholder="Contoh: 1-50 atau 51" value="{{ old('nomor_hm') }}" required />
                                <p class="text-xs text-gray-500 mt-1">Gunakan tanda strip (-) untuk rentang nomor.</p>
                                @error('nomor_hm')
                                    <p class="text-sm text-red-600 mt-2 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <x-input-label for="jenis_sertifikat" value="Jenis Sertifikat" />
                                <select name="jenis_sertifikat" id="jenis_sertifikat" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                                    <option value="SU - BT">SU - BT</option>
                                    <option value="HT">HT</option>
                                    <option value="HGB">HGB</option>
                                    <option value="HGU">HGU</option>
                                </select>
                                <p class="text-[11px] text-gray-500 mt-1 leading-tight">
                                    SU (Surat Ukur), BT (Buku Tanah), HT (Hak Tanggungan), HGB (Hak Guna Bangunan), HGU (Hak Guna Usaha)
                                </p>
                            </div>
                            <div>
                                <x-input-label for="kecamatan" value="Kecamatan" />
                                <select id="kecamatan" name="kecamatan" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full {{ $errors->has('kecamatan') ? 'border-red-500' : '' }}" required>
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                                @error('kecamatan')
                                    <p class="text-sm text-red-600 mt-2 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <x-input-label for="desa" value="Desa / Kelurahan" />
                                <select id="desa" name="desa" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full {{ $errors->has('desa') ? 'border-red-500' : '' }}" disabled required>
                                    <option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>
                                </select>
                                @error('desa')
                                    <p class="text-sm text-red-600 mt-2 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <x-input-label for="lokasi_rak" value="Lokasi Rak" />
                                <x-text-input id="lokasi_rak" name="lokasi_rak" type="number" min="1" class="mt-1 block w-full" required />
                            </div>
                            <div>
                                <x-input-label for="lokasi_baris" value="Lokasi Baris" />
                                <x-text-input id="lokasi_baris" name="lokasi_baris" type="number" min="1" class="mt-1 block w-full" required />
                            </div>
                        </div>
                        <div class="flex items-center gap-4 mt-6">
                            <x-primary-button>Simpan Data</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
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

        for (let kec in dataWilayah) {
            let option = document.createElement("option");
            option.value = kec;
            option.text = kec;
            kecamatanSelect.appendChild(option);
        }

        kecamatanSelect.addEventListener('change', function() {
            let selectedKecamatan = this.value;
            desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
            
            if (selectedKecamatan) {
                desaSelect.disabled = false; 
                let listDesa = dataWilayah[selectedKecamatan];
                listDesa.sort().forEach(function(desa) {
                    let option = document.createElement("option");
                    option.value = desa;
                    option.text = desa;
                    desaSelect.appendChild(option);
                });
            } else {
                desaSelect.innerHTML = '<option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>';
                desaSelect.disabled = true; 
            }
        });
    </script>
</x-app-layout>