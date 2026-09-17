# Sistem Informasi Manajemen Alur Berkas - BPN Rokan Hulu

Sistem Informasi Manajemen Alur Berkas ini dirancang khusus untuk mendigitalisasi proses pelacakan alur dokumen permohonan masyarakat di Kantor Pertanahan Kabupaten Rokan Hulu[cite: 4]. Sistem ini menggantikan metode pencatatan manual dengan pendekatan terkomputerisasi yang transparan, efisien, dan berbasis SLA (*Service Level Agreement*)[cite: 4].

## 🚀 Fitur Utama

*   **Pelacakan Progres Real-Time & Manajemen SLA:** Sistem dilengkapi fitur hitung mundur (*countdown*) dengan indikator warna peringatan untuk memantau batas waktu penyelesaian berkas di setiap seksi operasional[cite: 4].
*   **Portal Pelacakan Publik (QR Code):** Masyarakat/pemohon dapat melakukan pelacakan status berkas secara mandiri tanpa perlu login (autentikasi), cukup dengan memindai QR Code yang tercetak pada dokumen tanda terima fisik[cite: 4].
*   **Manajemen Kendala (Berita Acara):** Mekanisme cerdas untuk menahan sementara berkas yang bermasalah (kendala fisik/teknis) agar tidak hilang dari pantauan, memaksa petugas untuk menyelesaikan kendala tersebut sebelum alur dilanjutkan[cite: 4].
*   **Katalog Arsip Digital:** Modul untuk mencatat lokasi penyimpanan fisik arsip (rak dan baris) yang dilengkapi dengan validasi pencegahan tumpang tindih data (*overlap validation*)[cite: 4].
*   **Dashboard Executive Information System (EIS):** Fitur pelaporan khusus untuk pimpinan yang menyajikan visualisasi statistik kinerja, *throughput*, ketepatan waktu pegawai, serta ekspor laporan ke format Excel[cite: 4].

## 🛠️ Teknologi yang Digunakan

*   **Backend:** PHP 8.2, Framework Laravel 12[cite: 4]
*   **Frontend:** Tailwind CSS, Alpine.js[cite: 4]
*   **Database:** MySQL[cite: 4]
*   **Library:** SimpleSoftwareIO / Simple QrCode (untuk *generate* QR Code otomatis)[cite: 4]

## 👥 Hak Akses (Role-Based Access Control)

Sistem ini menerapkan pembatasan hak akses yang ketat dengan 7 jenis peran pengguna[cite: 4]:
1.  **Loket:** Menginput pengajuan berkas permohonan baru dan mencetak dokumen tanda terima ber-QR Code[cite: 4].
2.  **Arsip:** Memverifikasi kelengkapan awal dokumen fisik dan mengelola pendataan katalog tata letak rak arsip[cite: 4].
3.  **Seksi 1 (Survei dan Pemetaan):** Memverifikasi data teknis (Spasial dan Tekstual) dari berkas permohonan[cite: 4].
4.  **Seksi 2 (Penetapan Hak dan Pendaftaran):** Melakukan pemeriksaan final sebelum berkas dikembalikan ke Loket dengan status Selesai[cite: 4].
5.  **Admin:** Mengelola data akun pengguna dan melakukan konfigurasi batas waktu standar pengerjaan (SOP/SLA)[cite: 4].
6.  **Monitor (Kepala Kantor):** Mengakses *dashboard* statistik kinerja untuk keperluan pengawasan dan evaluasi[cite: 4].
7.  **Pemohon (Eksternal):** Mengakses portal publik secara independen untuk melihat progres dokumen[cite: 4].

---
*Proyek ini diinisiasi pada masa pelaksanaan Kerja Praktik (Juli - Agustus 2025) dan disempurnakan sebagai syarat kelulusan Tugas Akhir di Universitas Riau (2026).*
