# Panduan Setup Proyek Kontraktor.id

Dokumen ini berisi panduan teknis untuk programmer selanjutnya agar dapat melakukan instalasi, konfigurasi, dan menjalankan proyek **Kontraktor.id** di lingkungan lokal (*development*).

## 🛠️ Persyaratan Sistem (Prerequisites)
Pastikan sistem komputer Anda sudah terinstal perangkat lunak berikut:
1. **PHP** (minimal versi 8.1+)
2. **Composer** (untuk manajemen dependensi PHP / CodeIgniter 4)
3. **Node.js & npm** (untuk kompilasi Tailwind CSS dan *library* JavaScript)
4. **Laragon / XAMPP** (sebagai *local server* & eksekusi PHP)
5. **DBeaver** (sebagai *Database Client GUI* standar tim)
6. **Git** (untuk *version control*)

---

## 🚀 Langkah-langkah Instalasi

### 1. Clone Repositori
Arahkan terminal Anda ke folder *document root* web server lokal (seperti folder `www` di Laragon atau `htdocs` di XAMPP).
```bash
git clone <url-repo-anda> konstruktor.id
cd konstruktor.id
```

### 2. Instalasi Dependensi Backend (PHP)
Jalankan perintah composer untuk mengunduh seluruh *library* dan *framework* CodeIgniter 4 yang dibutuhkan oleh proyek ini.
```bash
composer install
```

### 3. Instalasi Dependensi Frontend (Node.js/NPM)
Proyek ini secara ekstensif menggunakan **Tailwind CSS** (sebagai *framework styling*) dan **Preline UI** (untuk komponen interaktif seperti modal dan dropdown). Instal seluruh dependensi ini menggunakan `npm`.
```bash
npm install
```

### 4. Konfigurasi Environment (`.env`)
Salin file bawaan konfigurasi (`env`) menjadi `.env`.
```bash
cp env .env
```
Buka file `.env`, lalu pastikan variabel `CI_ENVIRONMENT` diubah menjadi `development` agar pesan *error* dapat dimunculkan dengan jelas selama proses *coding*:
```env
CI_ENVIRONMENT = development
```

---

## 🗄️ Setup Database (Menggunakan DBeaver)

Proyek ini terhubung ke dua arsitektur *database* yang berbeda (*multidatabase*). Konfigurasi dan pengelolaannya dilakukan melalui aplikasi **DBeaver**.

1. Buka aplikasi **DBeaver**.
2. Klik ikon colokan listrik (**New Database Connection**) lalu pilih **MySQL**.
3. Buat dua buah koneksi baru dengan kredensial yang tersimpan di dalam file `.env` proyek Anda:

   **A. Database Utama (Kontraktor)**
   Menyimpan data pengguna, profil kontraktor, proyek, RAB, gudang, dll.
   - **Host:** (Cek variabel `database.default.hostname` di `.env`, saat ini menggunakan *cloud* `mysql-306fa75b-kontraktor-123.e.aivencloud.com`)
   - **Port:** `14807`
   - **Database:** `defaultdb`
   - **User/Pass:** Sesuai di `.env`
   
   **B. Database Master Eksternal (Estimator)**
   Menyimpan master data wilayah, AHS, dan item material pusat.
   - **Host:** (Cek variabel `database.estimator.hostname`, contoh: `147.93.19.39`)
   - **Port:** `3306`
   - **Database:** `estimator_alpha`
   - **User/Pass:** Sesuai di `.env`

> **Catatan Mode Lokal:** Jika Anda tidak menggunakan *database cloud* dan ingin menjalankan *database* Utama murni di lokal Anda:
> 1. Buat database baru di DBeaver (misal: `kontraktor_lokal`).
> 2. Ubah host di `.env` bagian `database.default` menjadi `localhost` dengan nama database `kontraktor_lokal`.
> 3. Jalankan migrasi dan seeder di terminal:
>    `php spark migrate`
>    `php spark db:seed MainSeeder` *(Sesuaikan nama class seeder)*

---

## 🏃‍♂️ Menjalankan Aplikasi (Development Mode)

Untuk mengembangkan aplikasi ini, Anda membutuhkan **dua buah terminal yang berjalan secara bersamaan**.

**Terminal 1: Menjalankan Local Web Server (CodeIgniter)**
Buka terminal pertama di dalam folder `konstruktor.id`, lalu jalankan:
```bash
php spark serve
```
*(Aplikasi akan bisa diakses melalui browser di alamat `http://localhost:8080`)*

**Terminal 2: Menjalankan Tailwind CSS Watcher**
Buka tab terminal kedua, lalu jalankan:
```bash
npm run dev
```
*(Perintah ini bertugas memantau/watching setiap kali Anda melakukan 'Save' pada file `.php`. Ia akan otomatis mengkompilasi ulang kode *class* Tailwind ke dalam file `public/assets/css/output.css`)*

---

## 🐛 Panduan Troubleshooting Cepat

*   **Tampilan CSS berantakan atau *class* baru tidak muncul?**
    Pastikan Terminal 2 yang menjalankan `npm run dev` aktif dan tidak mengalami *crash*.
*   **Ikon navbar atau menu tidak muncul?**
    Proyek ini menggunakan FontAwesome. Jika membuat halaman baru yang bersifat *standalone* (tidak *extend* dari `layouts/app.php`), pastikan Anda sudah memasukkan tag berikut di `<head>`:
    `<link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">`
*   **Dropdown Pemilihan Kota / Pencarian Master Barang Kosong (No results found)?**
    Sistem menggunakan mekanisme aman (*try-catch*) untuk menarik data dari server *Estimator* eksternal (IP: `147.93.19.39`). Jika dropdown kosong sama sekali, besar kemungkinan server *Estimator* sedang *down* atau *offline*. Sistem memang dirancang agar tidak *crash* ketika koneksi eksternal ini terputus.
