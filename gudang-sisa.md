# Rencana Implementasi: Rekonsiliasi Sisa Material Gudang Lapangan (Opsi A)

Dokumen ini menjelaskan rancangan teknis untuk menangani material sisa ketika status proyek diubah menjadi **Selesai (Done)**.

---

## 1. Alur Pengguna (User Flow)

1. Pengguna membuka halaman **Daftar Proyek** (`proyek/index.php`) dan mengklik tombol **"Selesai"** pada kartu proyek.
2. Sistem melakukan pengecekan via API:
   * **Skenario 1 (Tidak ada sisa):** Jika proyek tidak memiliki sisa material (`stok_aktual = 0` untuk semua barang), proyek langsung diubah statusnya menjadi `done`.
   * **Skenario 2 (Ada sisa):** Jika terdapat sisa material (`stok_aktual > 0`), modal **"Rekonsiliasi Sisa Material"** akan muncul.
3. Di dalam modal, pengguna mengalokasikan seluruh sisa stok per barang ke dalam 3 pilihan tindakan:
   * **Retur ke Gudang Central:** Mengembalikan kelipatan kemasan utuh (misal: 50 kg / 1 Sak).
   * **Mutasi ke Proyek Lain:** Memindahkan barang ke proyek aktif lain yang dipilih dari dropdown.
   * **Waste (Penyusutan):** Membuang sisa pecahan (misal: 10 kg sisa semen) atau barang rusak agar stok di proyek tersebut menjadi `0`.
4. Pengguna mengklik tombol **"Proses & Selesaikan Proyek"**. Data dikirim sebagai payload JSON ke backend. Jika transaksi sukses, status proyek diperbarui menjadi `done` dan halaman di-refresh.

---

## 2. Perubahan Database & Log Kartu Stok

Kita akan menggunakan tabel `stok_proyek` dan `kartu_stok_proyek` yang sudah mendukung desimal (`DECIMAL(15,4)`). Ketika alokasi diproses:
1. **Retur ke Central:**
   * Memotong `stok_proyek`.
   * Menambah `stok_gudang` (dibagi `konversi_faktor` barang).
   * Mencatat di `kartu_stok_proyek` dengan tipe `keluar` dan sumber `retur_ke_central`.
2. **Mutasi ke Proyek Lain:**
   * Memotong `stok_proyek` asal.
   * Menambah `stok_proyek` tujuan (jika belum ada record, dibuat otomatis).
   * Mencatat log `mutasi_keluar` di proyek asal dan log `mutasi_masuk` di proyek tujuan.
3. **Waste / Penyusutan:**
   * Memotong `stok_proyek` asal hingga menjadi `0`.
   * Mencatat di `kartu_stok_proyek` dengan tipe `keluar` dan sumber `waste_penyusutan`.

---

## 3. Rencana Perubahan Kode

### A. Backend: API & Routing
#### [MODIFY] [Routes.php](file:///d:/laragon/www/konstruktor.id/app/Config/Routes.php)
* Tambahkan endpoint API baru:
  ```php
  $routes->get('api/gudang-lapangan/sisa-stok/(:num)', 'menu\GudangLapanganController::getSisaStok/$1');
  $routes->get('api/proyek/aktif', 'ProyekController::getActiveProjects');
  $routes->post('api/proyek/selesai-reconcile/(:num)', 'ProyekController::selesaiReconcile/$1');
  ```

### B. Backend: Services
#### [MODIFY] [ProjectInventoryService.php](file:///d:/laragon/www/konstruktor.id/app/Services/ProjectInventoryService.php)
* Tambahkan method untuk mencatat pembuangan/waste material sisa:
  ```php
  public function catatWaste(int $idProject, int $idBarang, float $jumlah, string $keterangan = 'Waste / Penyusutan sisa proyek'): void
  {
      if ($jumlah <= 0) return;
      $sisaStok = $this->stokProyekModel->kurangiStok($idProject, $idBarang, $jumlah);
      
      $this->kartuStokModel->catat(
          idProject:  $idProject,
          idBarang:   $idBarang,
          tipe:       'keluar',
          jumlah:     $jumlah,
          sisaStok:   $sisaStok,
          sumber:     'waste_penyusutan',
          idSumber:   null,
          keterangan: $keterangan
      );
  }
  ```

### C. Backend: Controllers
#### [MODIFY] [ProyekController.php](file:///d:/laragon/www/konstruktor.id/app/Controllers/ProyekController.php)
* Tambahkan method `getActiveProjects()` untuk mendapatkan daftar proyek yang belum `done` (untuk dropdown mutasi).
* Tambahkan method `selesaiReconcile($id)` yang memproses alokasi sisa material di dalam DB Transaction:
  ```php
  $db = db_connect();
  $db->transStart();
  
  // Looping alokasi item sisa dari request JSON:
  foreach ($reconciliations as $item) {
      $idBarang = (int)$item['id_barang'];
      
      if ($item['jumlah_retur'] > 0) {
          $projectInventoryService->returKeCentral($idProject, $idBarang, $idPerusahaan, $item['jumlah_retur'], 'Retur otomatis penutupan proyek');
      }
      if ($item['jumlah_mutasi'] > 0 && !empty($item['id_proyek_tujuan'])) {
          $projectInventoryService->mutasiAntar($idProject, (int)$item['id_proyek_tujuan'], $idBarang, $item['jumlah_mutasi'], 'Mutasi penutupan proyek');
      }
      if ($item['jumlah_waste'] > 0) {
          $projectInventoryService->catatWaste($idProject, $idBarang, $item['jumlah_waste'], 'Penyusutan sisa penutupan proyek');
      }
  }
  
  // Set status proyek menjadi 'done'
  $db->table('projects')->where('id_project', $idProject)->update(['status_proyek' => 'done']);
  $db->transComplete();
  ```

### D. Frontend: Views & Javascript
#### [MODIFY] [index.php](file:///d:/laragon/www/konstruktor.id/app/Views/proyek/index.php)
* Tambahkan modal HTML `#modal-reconcile-proyek` dengan desain Navy/Slate yang konsisten:
  * Header warna Navy `#1e293b`.
  * Tabel daftar material sisa (Kode, Nama Material, Sisa Stok dalam satuan dasar & kemasan).
  * Form input pembagian alokasi: **Retur ke Central** (input jumlah), **Mutasi ke Proyek** (dropdown proyek aktif + input jumlah), **Waste** (input jumlah).
  * Baris/indikator sisa alokasi berwarna merah jika total alokasi belum sama dengan sisa stok aktual.
  * Tombol **"Selesaikan Proyek"** dinonaktifkan jika ada item yang alokasinya belum pas.

* Perbarui event listener tombol **"Selesai"** (`.btn-selesai-proyek`):
  1. Melakukan fetch ke `api/gudang-lapangan/sisa-stok/${id}`.
  2. Jika data kosong (tidak ada sisa stok), langsung panggil API penutupan proyek biasa.
  3. Jika ada sisa stok, isi tabel modal, panggil data proyek aktif untuk dropdown mutasi, lalu tampilkan modal rekonsiliasi.

---

## 4. Rencana Pengujian (Verification Plan)

1. **Uji Tanpa Sisa Stok:**
   * Tandai proyek yang tidak memiliki stok material lapangan sebagai Selesai.
   * Pastikan proyek langsung berubah menjadi `done` dan ter-refresh tanpa muncul modal rekonsiliasi.

2. **Uji Dengan Sisa Stok (Pecahan & Utuh):**
   * Buat skenario proyek dengan sisa semen `75 kg` (Faktor: 50 kg/Sak).
   * Klik selesai, pastikan modal rekonsiliasi muncul dan menampilkan `Semen: 75 kg (1.5 Sak)`.
   * Lakukan alokasi:
     * Retur: `50 kg` (1 Sak)
     * Waste: `25 kg` (0.5 Sak)
   * Kirim form, pastikan:
     * Status proyek berubah menjadi `done`.
     * Stok Gudang Central bertambah `1 Sak`.
     * Stok Proyek tersebut menjadi `0`.
     * Kartu stok proyek mencatat log `retur_ke_central` (`50 kg`) dan log `waste_penyusutan` (`25 kg`).
