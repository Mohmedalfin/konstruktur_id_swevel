# Dokumentasi Proses Bisnis: Modul Purchasing

Dokumen ini menjelaskan alur proses bisnis (*Business Process*) secara *end-to-end* untuk **Modul Purchasing** (Pembelian) di sistem Kontraktor.id. Modul ini bertanggung jawab atas seluruh aktivitas pengadaan barang dari pihak eksternal (*Supplier/Vendor*) berdasarkan permintaan yang diajukan oleh Gudang Pusat.

---

## 🛒 1. Gambaran Umum (High-Level Flow)

Modul Purchasing merupakan ujung tombak dalam proses pengeluaran dana untuk pengadaan material. Alur utamanya terbagi menjadi beberapa fase:

1. **Pengelolaan Master Data** (Supplier & Harga Material)
2. **Penerimaan Purchase Request (PR)** (Dari Gudang)
3. **Pemrosesan Purchase Order (PO)** (Pemilihan Supplier & Negosiasi Harga)
4. **Tracking & Monitoring PO** (Pemantauan Status Pengiriman)
5. **Penyelesaian Transaksi** (Sinkronisasi dengan Penerimaan Gudang)

---

## 📋 2. Rincian Alur Per Sub-Modul

### A. Pengelolaan Master Data (Supplier & Material)
- **Aktor:** Staf Purchasing
- **Fungsi:** 
  Sebagai fondasi untuk melakukan transaksi pembelian yang efisien, tim Purchasing harus mengelola data referensi utama:
  1. **Master Supplier:** Mendata seluruh vendor/supplier, alamat, kontak, dan spesialisasi material yang mereka jual.
  2. **Master Material & Harga:** (*Terintegrasi dengan Modul Gudang*) Purchasing mencatat histori harga beli dari setiap material agar mempermudah negosiasi dan estimasi pengeluaran di masa mendatang.
- **Output:** Database Supplier dan Referensi Harga yang valid dan mutakhir.

### B. Penerimaan Purchase Request (PR)
- **Aktor:** Staf Purchasing / Manager Purchasing
- **Fungsi:** 
  Modul ini menerima dokumen **Pengadaan (Purchase Request / PR)** yang di-*generate* secara otomatis oleh Modul Gudang (ketika stok gudang kurang atau mencapai *Reorder Point*).
  - Purchasing meninjau daftar barang yang diminta beserta kuantitasnya.
  - Memastikan *urgensi* dan mencocokkan permintaan dengan anggaran (jika diperlukan).
- **Output:** Antrean PR berstatus *Pending* yang siap untuk diproses menjadi order pembelian.

### C. Pembuatan Purchase Order (Generate PO)
- **Aktor:** Staf Purchasing
- **Fungsi:** 
  Proses inti di mana Purchasing mengeksekusi pengadaan barang dengan mengkonversi dokumen PR internal menjadi dokumen PO eksternal yang resmi.
  1. **Pemilihan Supplier:** Purchasing memilih *Supplier* mana yang akan mensuplai barang pada PR tersebut.
  2. **Split Order (Pemecahan PO):** Jika satu PR memuat banyak barang, Purchasing dapat memecah (*split*) PR tersebut menjadi beberapa PO berbeda yang ditujukan ke *Supplier* yang berbeda.
  3. **Penetapan Harga:** Memasukkan harga kesepakatan (*Harga Deal*) untuk setiap item.
- **Output:** Dokumen resmi **Purchase Order (PO)** yang dikirim ke Supplier. Status PR asal akan berubah menjadi *Processed* (sebagian atau seluruhnya).

### D. PO Tracking & Monitoring
- **Aktor:** Staf Purchasing
- **Fungsi:** 
  Setelah PO diterbitkan, Purchasing bertugas melacak status pemenuhan order dari Supplier bersangkutan.
  Siklus status PO umumnya meliputi:
  - `Draft`: PO sedang disusun.
  - `Sent`: PO sudah dikirim ke Supplier.
  - `On Process`: Supplier sedang memproses/menyiapkan barang.
  - `In Transit`: Barang sedang dalam perjalanan ke Gudang Pusat.
  - `Completed`: Barang sudah tiba dan diverifikasi oleh Gudang.
  - `Cancelled`: Pembatalan order karena kendala tertentu.
- **Output:** Visibilitas *real-time* mengenai di mana posisi barang yang sedang dipesan.

### E. Sinkronisasi Penerimaan Gudang (Goods Receipt)
- **Aktor:** Staf Purchasing & Petugas Gudang
- **Fungsi:** 
  Ketika fisik barang tiba, proses penerimaan dilakukan oleh **Petugas Gudang** (Modul Gudang). 
  - Saat Gudang mengonfirmasi penerimaan jumlah barang sesuai dengan PO, status PO di Modul Purchasing akan otomatis tertutup (`Completed`).
  - Purchasing dapat merekonsiliasi dokumen tagihan (*Invoice*) dari Supplier dengan *Goods Receipt* dari Gudang.
- **Output:** Transaksi tertutup (Selesai), stok gudang bertambah.

---

## 🔗 3. Keterkaitan dengan Modul Lain (Integration Points)

- **Dari Modul Gudang:** 
  Modul Gudang adalah sumber (*trigger*) utama pengadaan. Purchasing **tidak** membeli barang tanpa adanya dokumen PR resmi dari Gudang.
- **Ke Modul Gudang:** 
  Setelah Purchasing menerbitkan PO dan barang dikirim oleh Supplier, Gudang adalah pihak yang akan memverifikasi kedatangan fisik barang tersebut. Gudang akan menutup siklus PO yang dibuat oleh Purchasing.
- **Ke Master Estimator / Proyek:** 
  (Opsional) Harga beli material aktual yang didapat oleh Purchasing dapat digunakan sebagai *feedback* evaluasi (*Historical Price*) bagi Estimator dalam menyusun RAP proyek di masa depan agar nilai anggarannya lebih realistis.
