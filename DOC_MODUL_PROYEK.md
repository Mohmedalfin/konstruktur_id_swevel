# Dokumentasi Proses Bisnis: Modul Proyek

Dokumen ini menjelaskan alur proses bisnis (Business Process) secara end-to-end untuk **Modul Proyek** di sistem Kontraktor.id. Modul ini merupakan inti (*core*) dari sistem operasi kontraktor di mana seluruh aktivitas operasional lapangan dicatat, direncanakan, dan dieksekusi.

---

## 🏗️ 1. Gambaran Umum (High-Level Flow)

Modul Proyek dirancang untuk memantau siklus hidup (*lifecycle*) suatu proyek konstruksi dari awal hingga akhir. Alur utamanya terbagi menjadi beberapa fase:

1. **Inisiasi Proyek** (Pembuatan Proyek)
2. **Perencanaan Anggaran** (RAP & AHS)
3. **Perencanaan Waktu** (Penjadwalan / Schedule)
4. **Pelaksanaan Lapangan** (Permintaan Barang & Gudang Lapangan)
5. **Pengawasan & Pencatatan** (Realisasi Progress)
6. **Penutupan Proyek** (Reconciliation)

---

## 📋 2. Rincian Alur Per Sub-Modul

### A. Manajemen Daftar Proyek (Inisiasi)
- **Aktor:** Admin/Kontraktor Utama
- **Fungsi:** 
  Pembuatan *record* proyek baru. Pengguna mengisi informasi dasar seperti Nama Proyek, Lokasi (mengambil data dari *Master Wilayah Estimator*), Nilai Kontrak (Harga Deal), Pemilik (Owner), dan Estimasi Waktu pengerjaan.
- **Output:** Sebuah entitas Proyek berstatus `draft` dengan kode proyek unik yang otomatis ter-generate.

### B. Penyusunan RAP (Rencana Anggaran Pelaksanaan)
- **Aktor:** Estimator / Project Manager
- **Fungsi:** 
  Setelah proyek dibuat, pengguna masuk ke **Menu RAP**. Di sini mereka mem-*breakdown* proyek menjadi beberapa **Kategori Pekerjaan** dan **Detail Pekerjaan**.
  - **AHS (Analisa Harga Satuan):** Pengguna mengaitkan/menarik data AHS dari *Database Master Estimator* pusat untuk mendapatkan daftar kebutuhan Bahan, Alat, dan Upah per pekerjaan.
  - **Kuantitas/Volume:** Pengguna menentukan volume pekerjaan. Sistem secara otomatis menghitung *Subtotal* berdasarkan koefisien AHS dikali Harga Dasar.
- **Output:** Dokumen RAP yang mengunci *budget* maksimal untuk setiap material yang akan digunakan di proyek.

### C. Penjadwalan (Schedule & Kurva-S)
- **Aktor:** Project Manager / Scheduler
- **Fungsi:** 
  Menentukan tanggal mulai (*start*) dan selesai (*end*) untuk setiap item pekerjaan yang sudah didefinisikan di RAP. Sistem akan menggunakan data ini untuk merumuskan target persentase pengerjaan harian/mingguan (Kurva-S Target).
- **Output:** Timeline proyek yang menjadi dasar untuk menghitung *Deviasi* (Keterlambatan atau Percepatan progress).

### D. Eksekusi: Permintaan Barang ke Gudang (Material Request)
- **Aktor:** Pelaksana Lapangan / Site Manager
- **Fungsi:** 
  Tahap ini adalah fase kritis di mana rencana (RAP) mulai dieksekusi menjadi permintaan nyata ke Gudang Pusat. Lapangan **tidak melakukan pembelian barang secara mandiri**, melainkan mengajukan dokumen Permintaan Barang yang akan diteruskan ke tim Logistik/Gudang.
  
  **Alur dan Aturan Permintaan (Request Flow):**
  1. **Pembuatan Permintaan (Multi-Project Support):**
     Pelaksana lapangan dapat membuat satu dokumen pengajuan yang mencakup kebutuhan material/alat untuk **beberapa proyek sekaligus**. Mereka memilih nama proyek, dan sistem akan secara otomatis memuat data Rencana Anggaran Pelaksanaan (RAP) khusus untuk proyek tersebut.
  2. **Pencarian Barang (Autocomplete dari RAP):**
     Saat pelaksana mengetik nama bahan/alat, sistem akan memberikan saran (*autocomplete*) yang ditarik langsung dari item-item yang sudah disetujui di dalam dokumen RAP proyek tersebut. Ini memastikan barang yang diminta tidak keluar dari rencana awal. (Sistem juga tetap mengakomodasi permintaan barang *custom* jika diperlukan).
  3. **Manajemen Kuantitas & Deviasi:**
     Pelaksana menginput jumlah yang diminta (beserta satuannya) dan menyertakan catatan instruksi pengiriman. Sistem akan memantau **Deviasi Material** secara *real-time* untuk membandingkan kuantitas yang sedang diminta terhadap sisa jatah volume yang masih tersedia di RAP.
  4. **Monitoring & Status Approval:**
     Setiap dokumen permintaan memiliki siklus status:
     - `Draft`: Dokumen sedang disusun.
     - `Pending`: Diajukan ke gudang dan menunggu persetujuan.
     - `Disetujui`: Permintaan valid dan sedang diproses/dipacking oleh gudang.
     - `Terkirim` / `Selesai`: Barang sudah dikirimkan ke lokasi proyek (Site Inventory).
     - `Ditolak`: Permintaan ditolak oleh gudang (misalnya karena *over-budget* atau barang tidak relevan).

- **Output:** Sebuah dokumen *Permintaan Barang* (beserta rincian detail per item) yang masuk ke dalam antrean (Dashboard Monitoring) pihak Gudang Pusat.

### E. Eksekusi: Gudang Lapangan (Site Inventory)
- **Aktor:** Logistik Lapangan
- **Fungsi:** 
  Mencatat dan memantau stok fisik yang sudah dikirim oleh Gudang Pusat/Supplier dan **telah tiba di lokasi proyek**.
  - Melacak "Kartu Stok" untuk barang keluar-masuk khusus di proyek tersebut.
  - Memungkinkan fitur **Retur** barang kembali ke Gudang Pusat jika terdapat kelebihan pemakaian.
- **Output:** Laporan ketersediaan material *real-time* di *site*.

### F. Pengawasan: Realisasi Progress
- **Aktor:** Pelaksana Lapangan
- **Fungsi:** 
  Mencatat progress aktual pekerjaan. Pengguna mengisi persentase penyelesaian fisik harian/mingguan dan mencatat kehadiran SDM (tukang/pekerja).
  - Hasil input ini akan langsung membandingkan *Progress Aktual* vs *Progress Rencana* (Schedule).
- **Output:** Dashboard visual yang menampilkan persentase penyelesaian proyek dan metrik deviasi waktu.

### G. Penutupan Proyek & Rekonsiliasi (Project Closing)
- **Aktor:** Project Manager
- **Fungsi:** 
  Ketika proyek ditandai sebagai `selesai` (Done), sistem akan secara otomatis mendeteksi sisa stok di Gudang Lapangan. 
  - Sistem meminta *Rekonsiliasi*: apakah sisa material akan di-**Retur** ke Gudang Pusat, di-**Mutasi** ke proyek lain, atau dicatat sebagai **Waste** (Penyusutan/Dibuang).
- **Output:** Stok lapangan menjadi 0, proyek terkunci (tidak bisa meminta barang lagi), dan status berubah menjadi Selesai.

---

## 🔗 3. Keterkaitan dengan Modul Lain (Integration Points)

- **Ke Modul Gudang:** 
  Modul Proyek mensuplai *Demand* (Permintaan Barang) yang harus dipenuhi oleh Modul Gudang. Modul Gudang yang akan menentukan apakah barang akan diambil dari stok gudang atau dibelikan baru.
- **Ke Modul Purchasing:** 
  Jika gudang tidak memiliki stok atas *Permintaan* dari Proyek, Gudang akan meneruskan permintaan tersebut menjadi *Purchase Request (PR)* ke Modul Purchasing.
- **Ke Master Estimator (Eksternal):** 
  Modul Proyek secara konstan melakukan *query API* ke database Estimator pusat untuk mendapatkan Harga Dasar terbaru, koefisien AHS, dan daftar wilayah.
