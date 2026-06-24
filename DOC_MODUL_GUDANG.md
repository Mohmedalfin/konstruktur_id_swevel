# Dokumentasi Proses Bisnis: Modul Gudang (Pusat)

Dokumen ini menjelaskan alur proses bisnis (*Business Process*) secara *end-to-end* untuk **Modul Gudang** (Gudang Pusat) di sistem Kontraktor.id. Modul ini bertindak sebagai pusat logistik (*logistic hub*) yang menghubungkan kebutuhan lapangan (Modul Proyek) dengan proses pengadaan barang (Modul Purchasing).

---

## 🏭 1. Gambaran Umum (High-Level Flow)

Modul Gudang beroperasi berdasarkan prinsip pasokan dan permintaan (*Supply & Demand*). Alur utamanya terbagi menjadi beberapa fase:

1. **Penerimaan Permintaan** (Merespons *Request* dari Proyek)
2. **Pengecekan & Alokasi Stok** (Fulfillment)
3. **Pengajuan Pengadaan** (Jika stok kurang / membuat *Purchase Request*)
4. **Penerimaan Barang Masuk** (Dari *Supplier* atau Retur Proyek)
5. **Manajemen Stok Master** (Kontrol Inventaris / *Stock Opname*)

---

## 📋 2. Rincian Alur Per Sub-Modul

### A. Tinjauan Permintaan (Request Management)
- **Aktor:** Kepala Gudang / Admin Logistik
- **Fungsi:** 
  Gudang menerima dokumen **Permintaan Barang** yang dikirimkan oleh Pelaksana Lapangan dari berbagai proyek.
  1. **Verifikasi:** Gudang meninjau permintaan tersebut apakah rasional dan sesuai dengan kebutuhan.
  2. **Tindakan Status:** Gudang dapat menolak permintaan (status `Ditolak`) dengan menyertakan alasan, atau menyetujuinya (status `Disetujui`).
- **Output:** Dokumen permintaan yang valid dan siap diproses untuk pemenuhan barang.

### B. Pemenuhan Barang & Pengecekan Stok (Fulfillment)
- **Aktor:** Petugas Gudang
- **Fungsi:** 
  Untuk permintaan yang berstatus `Disetujui`, Gudang harus mengecek ketersediaan fisik stok di Gudang Pusat. Di sini terdapat dua skenario:
  - **Skenario 1 (Stok Mencukupi):** Barang langsung disiapkan, di-*packing*, dan dikirim ke lokasi proyek. Status permintaan diubah menjadi `Terkirim` / `Selesai`. Stok di Gudang Pusat akan berkurang secara otomatis (tercatat di **Riwayat** sebagai Barang Keluar).
  - **Skenario 2 (Stok Kurang / Kosong):** Barang tidak bisa dikirim. Gudang harus melakukan pengadaan barang baru terlebih dahulu melalui fitur **Pengadaan**.
- **Output:** Pengurangan stok inventaris atau *trigger* untuk melakukan proses pengadaan.

### C. Pengajuan Pengadaan (Purchase Request)
- **Aktor:** Kepala Gudang
- **Fungsi:** 
  Gudang bertindak sebagai pihak yang melakukan "Pemesanan Internal" kepada divisi Pembelian (Purchasing). Gudang mengompilasi:
  - Barang-barang yang diminta oleh proyek namun stoknya kosong.
  - Barang-barang yang jumlah stoknya sudah menyentuh batas minimum (*Reorder Point*).
  Gudang membuat **Dokumen Pengadaan (Purchase Request)** dan mengirimkannya ke Modul Purchasing.
- **Output:** Dokumen PR (*Purchase Request*) yang masuk ke antrean kerja Modul Purchasing.

### D. Penerimaan Barang (Inbound Logistics)
- **Aktor:** Petugas Gudang
- **Fungsi:** 
  Ketika barang yang dibeli oleh divisi Purchasing (berdasarkan *Purchase Order*) telah dikirim oleh Supplier dan tiba di Gudang Pusat.
  - Petugas gudang memverifikasi jumlah dan kualitas fisik barang (Goods Receipt).
  - Jika sesuai, sistem mencatat penerimaan ini yang otomatis **menambah Saldo Stok** di Gudang Pusat.
- **Output:** Penambahan stok inventaris yang tercatat di **Riwayat** sebagai Barang Masuk.

### E. Penerimaan Retur (Reverse Logistics)
- **Aktor:** Petugas Gudang
- **Fungsi:** 
  Menerima material sisa yang dikembalikan dari lokasi proyek (biasanya ketika proyek sudah memasuki fase Penutupan/Rekonsiliasi). 
  - Barang retur yang masih layak pakai akan diinput kembali ke dalam sistem dan menambah stok Gudang Pusat.
- **Output:** Penambahan stok hasil retur proyek.

### F. Manajemen Stok Master (Inventory Control)
- **Aktor:** Kepala Gudang
- **Fungsi:** 
  Halaman untuk mengelola seluruh *database* material yang ada di gudang secara komprehensif.
  - Memantau kartu stok (*Stock Card*) dari setiap item.
  - Mengatur parameter peringatan stok kritis (*Minimum Stock Level*).
  - Melakukan *Stock Opname* (Penyesuaian stok manual jika terjadi kehilangan, penyusutan, atau kerusakan fisik barang di gudang).
- **Output:** Laporan validitas dan akurasi nilai inventaris perusahaan.

---

## 🔗 3. Keterkaitan dengan Modul Lain (Integration Points)

- **Dari Modul Proyek:** 
  Gudang menerima **Permintaan Barang** (sebagai *Demand*). Selain itu, Gudang menerima barang **Retur** dari sisa Gudang Lapangan proyek.
- **Ke Modul Purchasing:** 
  Gudang menyuplai data **Pengadaan / Purchase Request (PR)**. Pembelian barang murni menjadi tanggung jawab divisi Purchasing, Gudang hanya bertugas meminta dan menerima (*Requesting & Receiving*).
- **Ke Master Barang (Data Terpusat):** 
  Modul Gudang berbagi tabel `master_barang` (katalog material) yang sama dengan Modul Purchasing agar tidak terjadi duplikasi kode barang.
