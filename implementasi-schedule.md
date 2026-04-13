# Product Requirements Document (PRD)
**Project:** konstruktor.id - Fitur Jadwal (Schedule/Gantt Chart)
**Type:** Refactoring & Feature Implementation
**Architecture:** Vanilla JS (ES Modules) + AJAX/Fetch API

## 1. Project Overview
Melakukan perombakan arsitektur (refactoring) pada modul Jadwal Konstruksi dari skrip monolithic menjadi struktur direktori modular. Tujuannya adalah memisahkan antara logika *state management*, perhitungan *timeline*, pembuatan *template* HTML, manipulasi DOM, dan *orchestrator*. Pendekatan ini menggunakan AJAX/Fetch API agar *rendering* data berpindah ke sisi *client* tanpa memuat ulang halaman (*Client-Side Rendering*).

## 2. User Stories
* **View:** Sebagai pengguna, saya dapat melihat *timeline* proyek dalam format *Gantt Chart* mingguan dan indikator *loading* saat data pertama kali dimuat.
* **Filter & Export:** Sebagai pengguna, saya dapat memfilter data tabel berdasarkan kategori dan mengekspor data tersebut.
* **Inline Editing:** Sebagai pengguna, saya dapat mengubah tanggal *Start* atau *Finish* pada suatu pekerjaan langsung dari dalam tabel.
* **Auto-Calculation:** Saat saya mengubah tanggal *item* pekerjaan, sistem otomatis menghitung ulang durasi (hari), persentase *progress bar*, dan memperbarui batas waktu (*earliest/latest date*) dari kategori induknya.
* **Interaction:** Sebagai pengguna, saya dapat melakukan *collapse/expand* pada baris kategori untuk menyembunyikan/menampilkan detail *item* pekerjaan.

## 3. Functional & Technical Requirements
* **Asynchronous Data:** Pengambilan data harus menggunakan Fetch API ke *backend* Laravel.
* **Single Source of Truth:** Data `schedules` harus disimpan dalam *state* terpusat yang aman.
* **Dynamic DOM Manipulation:** DOM harus diinjeksi ke dalam kontainer yang sudah disediakan (`#schedule-thead` dan `#schedule-tbody`).
* **Event Delegation:** Pengikatan event (klik, *change input*) harus persisten dan tidak rusak ketika tabel di-*render* ulang.

## 4. Architecture & File Structure Design
Sistem akan dipecah ke dalam struktur ES Modules berikut:

### A. Direktori `core/` (Data & Logic)
* **`state.js`**: Menyimpan variabel global (`schedules`, `collapsedCategories`, `TIMELINE_CONFIG`) dan mengelola *getter/setter*.
* **`data.js`**: Menangani AJAX `fetch` ke backend dan memproses kalkulasi bisnis (seperti menghitung durasi rentang tanggal dan mencari tanggal terawal/terakhir untuk auto-update kategori induk).

### B. Direktori `components/` (UI & Interaksi)
* **`template.js`**: Pure function yang menerima *state/data* dan mengembalikan *string* HTML (untuk Header, Row Kategori, Row Item, dan komponen visual Gantt Bar).
* **`render.js`**: Bertanggung jawab menyuntikkan *string* HTML dari `template.js` ke dalam elemen DOM (`thead` dan `tbody`).
* **`categories.js`**: Mengelola interaksi visual spesifik seperti *collapse/expand* baris SVG plus/minus pada kategori.

### C. Direktori `hooks/` (Shared Utilities)
* Menyimpan fungsi *hooks* atau *event handlers* global/reusable seperti `search.js` dan `pending.js` yang dapat dipanggil oleh modul jadwal jika diperlukan.

### D. Direktori `schedule/` (Module Entry Point)
* **`index.js`**: Bertindak sebagai *Controller* utama khusus untuk modul Jadwal. Bertugas menginisiasi pengambilan data pertama kali, memanggil *render*, dan memasang *Global Event Listeners* (seperti `handleDateChange` untuk *inline editing*).