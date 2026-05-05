# Rencana Implementasi: Import BOQ Ultra-Fleksibel

Untuk menangani berbagai format rancangan Excel (BOQ) dari user yang sangat dinamis, kita perlu merancang ulang logika pembacaan Excel agar tidak kaku. Berikut adalah strategi untuk menyelesaikan setiap masalah yang Anda sebutkan:

## 1. Pekerjaan & Sub-Pekerjaan Menyamping (Multi-Column Uraian)
**Masalah**: Seperti di gambar, `Pekerjaan A` ada di kolom K1, `Sub Pekerjaan A` ada di K2, dst. Sistem saat ini hanya bisa baca 1 kolom Uraian.
**Solusi**:
- Ubah dropdown *Mapping* (Langkah 1) agar user bisa menandai **lebih dari satu kolom** sebagai `Uraian Pekerjaan`.
- Saat sistem membaca baris, sistem akan mengecek kolom-kolom `Uraian` tersebut dari kiri ke kanan.
- **Deteksi Indentasi Otomatis**: Jika teks ditemukan di kolom Uraian ke-1, maka levelnya = 0 (Kategori/Pekerjaan Utama). Jika teks ditemukan di kolom Uraian ke-2, level = 1 (Sub Pekerjaan), dan seterusnya. Ini membuat user tidak perlu capek menggeser-geser indentasi di Studio Organisir secara manual jika datanya sudah menyamping.

## 2. Merge Kolom & Merge Row (Merged Cells)
**Masalah**: Excel sering menggunakan *Merge Cells* (misalnya nama kategori di-merge dari kolom A sampai H). Saat dibaca oleh program, biasanya hanya cell pertama (kiri atas) yang ada isinya, sisanya `null`.
**Solusi**:
- Pustaka `ExcelJS` yang kita gunakan memiliki properti `worksheet.model.merges`.
- Kita akan membuat fungsi *helper* `getCellValue(row, col)` yang secara cerdas mendeteksi apakah cell tersebut merupakan bagian dari *Merge Cell*. Jika iya, sistem akan otomatis mengambil nilai dari "Master Cell" (sel utama dari gabungan tersebut).
- Ini memastikan data tidak terlewat meskipun posisinya berada di tengah-tengah area yang di-merge.

## 3. Header Beda Nama & Kolom Tidak Perlu
**Masalah**: Kolom `Rate` bisa bernama `Harga Satuan`, `Rate Per Unit`, atau ada banyak kolom sisipan seperti `Keterangan`, `Kode SNI`, dll.
**Solusi**:
- *Sistem Mapping Dropdown* (Langkah 1) yang sudah ada sebenarnya sudah menyelesaikan ini. Kita hanya perlu menguatkannya.
- User dibebaskan memilih kolom mana yang jadi `Volume` dan `Satuan`. Kolom yang tidak ditandai (dibiarkan kosong) akan **diabaikan otomatis**.

## 4. Penomoran yang Kacau / Berbeda-beda
**Masalah**: Kadang pakai A, B, C. Kadang 1, 1.1, 1.1.1. Kadang romawi (I, II, III).
**Solusi**:
- Kita **abaikan total** kolom penomoran dari Excel bawaan. 
- Sistem kita sudah menggunakan logika penomoran berjenjang (*hierarchical numbering*) secara otomatis (1, 1.1, 1.1.1) berdasarkan level indentasi. Jadi nomor bawaan Excel tidak akan merusak struktur di sistem Konstruktor.id.

## 5. Harga (Rate) dan Total yang di-Merge atau Terpisah
**Masalah**: Terkadang "Rate Per Unit" dipecah jadi "In Figure" (Angka) dan "In Words" (Huruf).
**Solusi**:
- Sama seperti kolom tidak perlu, user cukup me-*mapping* kolom "In Figure" ke kolom `Harga Satuan` di sistem. Kolom "In words" dibiarkan *unmapped* agar diabaikan.

---

## Rencana Perubahan Kode (Proposed Changes)

### 1. `public/js/rab/components/import.js`
- **[MODIFY] Fungsi `_renderTableHeaders`**: Mengubah UI dari `select` dropdown tunggal menjadi bisa memilih multi-kolom untuk `Uraian Pekerjaan` (misal dengan checkbox atau `select` multiple, atau merubah state `currentMapping.uraian` menjadi array `[]`).
- **[MODIFY] Fungsi `_prepareOrganizedData`**: 
  - Iterasi melalui array kolom `uraian`.
  - Hitung `level` secara dinamis berdasarkan indeks kolom `uraian` mana yang pertama kali memiliki nilai teks tidak kosong.
- **[MODIFY] Logika Pembacaan Nilai**: Mengganti pemanggilan `vals[index]` biasa dengan fungsi khusus yang memeriksa *merged cells*.

### 2. Membangun Helper `getMergedValue`
- Menggunakan `globalWorksheet._merges` dari ExcelJS untuk mencari koordinat master cell jika cell saat ini (row, col) kosong namun berada dalam batas range merge.

## User Review Required
> [!IMPORTANT]
> **Keputusan UI untuk Multi-Uraian**:
> Saat ini, user memilih mapping via dropdown di setiap kolom header (misal Header K1 -> dipilih jadi "Uraian"). Jika user mengatur K1 jadi "Uraian" dan K2 jadi "Uraian" juga, apakah kita izinkan sistem menganggap K1 sebagai Level 0 dan K2 sebagai Level 1 secara berurutan? (Ini adalah pendekatan paling simpel untuk user).
> 
> Silakan berikan tanggapan apakah pendekatan ini sesuai dengan bayangan tingkat "fleksibilitas" yang diharapkan bos Anda!
