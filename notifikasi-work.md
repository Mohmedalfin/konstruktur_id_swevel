# Panduan Integrasi Sistem Notifikasi (Untuk Modul Purchasing)

> [!NOTE]
> Dokumentasi ini dibuat khusus agar bisa dibaca dan dieksekusi langsung oleh AI Antigravity yang menggarap modul **Purchasing**. Sistem notifikasi inti (Database, Service, API, dan Shared Assets) sudah selesai diimplementasikan dan siap digunakan bersama.

Sistem notifikasi di aplikasi ini menggunakan arsitektur tersentralisasi berbasis database yang terhubung ke REST API dan polling di frontend (tiap 30 detik).

---

## 1. Spesifikasi Backend & Database

### Model & Tabel Database
Tabel `notifications` menyimpan seluruh riwayat notifikasi dengan skema sebagai berikut:
- `id` (INT, Primary Key, Auto Increment)
- `user_id` (INT, Nullable) - Diisi jika notifikasi ditujukan ke pengguna spesifik.
- `role_target` (VARCHAR, Nullable, lowercase) - Diisi jika notifikasi bersifat siaran/broadcast ke divisi tertentu (misal: `'gudang'`, `'purchasing'`, `'kontraktor'`).
- `title` (VARCHAR) - Judul notifikasi.
- `message` (TEXT) - Isi pesan notifikasi.
- `link` (VARCHAR, Nullable) - URL tujuan saat notifikasi diklik.
- `icon` (VARCHAR) - Class ikon FontAwesome (contoh: `'fa-solid fa-cart-shopping'`).
- `color` (VARCHAR) - Warna tema notifikasi (pilihan yang valid: `'blue'`, `'green'`, `'red'`, `'orange'`, `'purple'`, `'yellow'`).
- `source_module` (VARCHAR) - Modul pengirim (contoh: `'purchasing'`, `'gudang'`, `'proyek'`).
- `is_read` (TINYINT) - Status dibaca (`0` = belum, `1` = sudah).
- `created_at` & `updated_at` (DATETIME)

### Service Notifikasi
Untuk mengirim notifikasi dari Controller atau Service baru di modul Purchasing, gunakan [NotificationService](file:///d:/laragon/www/konstruktor.id/app/Services/NotificationService.php).

#### Cara Penggunaan di Controller:
```php
use App\Services\NotificationService;

// Di dalam method controller Anda:
try {
    $notifService = new NotificationService();
    
    // Opsi A: Kirim ke seluruh divisi/role 'gudang'
    $notifService->sendToRole(
        'gudang',                                      // target role (otomatis di-lowercase)
        'Purchase Request Baru 📋',                     // Judul
        'Ada PR baru PR-0023 yang membutuhkan approval.', // Pesan
        '/gudang/pengadaan',                            // Link tujuan
        'fa-solid fa-file-invoice-dollar',             // Ikon FontAwesome
        'purple',                                      // Warna tema ('blue'|'green'|'red'|'orange'|'purple'|'yellow')
        'purchasing'                                   // Modul pengirim
    );

    // Opsi B: Kirim ke user spesifik (misal ke pemohon material di proyek)
    $notifService->sendToUser(
        $pemohonId,                                    // ID user target (INT)
        'Material Dalam Pengiriman 🚚',
        'Material semen untuk proyek Rumah Kaca sedang dikirim oleh purchasing.',
        '/permintaan',
        'fa-solid fa-truck',
        'green',
        'purchasing'
    );
} catch (\Throwable $e) {
    log_message('warning', 'Gagal mengirim notifikasi: ' . $e->getMessage());
}
```

---

## 2. API Endpoints yang Tersedia

Seluruh interaksi frontend ke backend untuk notifikasi dikelola oleh [NotifikasiApiController](file:///d:/laragon/www/konstruktor.id/app/Controllers/Api/NotifikasiApiController.php) dengan rute-rute berikut:
- **`GET /api/notifications`** - Mengambil seluruh daftar notifikasi milik user/role yang sedang login (dibatasi 50 terakhir).
- **`GET /api/notifications/unread`** - Mengambil jumlah notifikasi belum dibaca serta 5 notifikasi terbaru (digunakan untuk polling lonceng).
- **`POST /api/notifications/mark-read/(:num)`** - Menandai notifikasi tertentu sebagai sudah dibaca.
- **`POST /api/notifications/mark-all-read`** - Menandai seluruh notifikasi user/role saat ini sebagai sudah dibaca.
- **`DELETE /api/notifications/delete/(:num)`** - Menghapus notifikasi tertentu.

---

## 3. Integrasi Lonceng Notifikasi di Navbar Purchasing

Agar lonceng notifikasi muncul dan terupdate secara real-time di navbar modul Purchasing, ikuti langkah berikut:

### Langkah A: Tambahkan HTML Lonceng di Navbar Modul Anda
Salin potongan HTML dropdown berikut dan pasang di navbar layout Purchasing (contoh pola dari [navbar.php](file:///d:/laragon/www/konstruktor.id/app/Views/gudang/partials/navbar.php)):

```html
<!-- Dropdown Notifikasi -->
<div class="hs-dropdown [--strategy:static] md:[--strategy:fixed] [--adaptive:none] md:[--adaptive:adaptive] [--is-collapse:true] md:[--is-collapse:false]">
    <button id="hs-header-notification-dropdown" type="button"
        class="hs-dropdown-toggle relative w-full p-2 md:w-auto md:px-4 md:justify-center flex items-center gap-3 text-sm text-navbar-foreground hover:bg-navbar-hover focus:outline-none focus:bg-navbar-focus transition-colors duration-200"
        aria-haspopup="menu" aria-expanded="false" aria-label="Notifikasi">
        <div class="shrink-0 relative">
            <i class="fa-regular fa-bell text-white text-[1.1rem]"></i>
            <!-- Badge Angka Notifikasi -->
            <span class="notif-badge-count absolute top-0 right-0 inline-flex items-center justify-center w-3.5 h-3.5 text-[9px] font-bold text-white bg-red-500 border border-primary rounded-full -mt-1 -mr-1.5 hidden">0</span>
        </div>
        <span class="md:hidden">Notifikasi</span>
    </button>

    <div class="hs-dropdown-menu transition-[opacity,margin] duration-[0.1ms] md:duration-[150ms] hs-dropdown-open:opacity-100 opacity-0 relative w-full md:w-80 hidden z-10 top-full ps-7 md:ps-0 md:bg-white md:border md:border-gray-200 md:shadow-md md:rounded-xl before:absolute before:-top-4 before:start-0 before:w-full before:h-5"
        role="menu" aria-orientation="vertical" aria-labelledby="hs-header-notification-dropdown">
        <div class="p-3 border-b border-gray-100 flex justify-between items-center bg-white md:rounded-t-xl">
            <h3 class="text-sm font-bold text-gray-800">Notifikasi</h3>
            <span class="notif-header-count text-xs text-red-600 bg-red-50 px-2 py-0.5 rounded-full font-semibold">0 Baru</span>
        </div>
        <!-- List Notifikasi Terbaru -->
        <div class="notif-dropdown-list max-h-72 overflow-y-auto bg-white [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300">
            <div class="p-4 text-center text-gray-500">
                <i class="fa-solid fa-circle-notch fa-spin text-2xl mb-2 text-gray-300"></i>
                <p class="text-xs">Memuat notifikasi...</p>
            </div>
        </div>
        <!-- Link Lihat Semua -->
        <div class="p-2 border-t border-gray-100 text-center bg-gray-50 md:rounded-b-xl">
            <a class="text-xs font-bold text-primary hover:text-primary/80 flex items-center justify-center gap-1 transition-colors" href="<?= base_url('purchasing/notifikasi') ?>">
                Lihat Semua Notifikasi <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>
    </div>
</div>
```

### Langkah B: Load Script Poller di Footer Layout
Pastikan layout utama Purchasing me-load script polling otomatis yang terletak di `public/js/shared/notification-poll.js`:

```html
<script src="<?= base_url('js/shared/notification-poll.js') ?>" defer></script>
```

---

## 4. Membuat Halaman Pusat Notifikasi Purchasing

Agar user divisi Purchasing dapat mengelola notifikasinya (menandai dibaca, memfilter berdasarkan modul, atau menghapusnya), buatlah halaman pusat notifikasi.

### Langkah A: Daftarkan Route Baru
Tambahkan rute web berikut di dalam file rute Purchasing Anda di [Routes.php](file:///d:/laragon/www/konstruktor.id/app/Config/Routes.php):

```php
$routes->group('purchasing', function($routes) {
    // ... rute lainnya
    $routes->get('notifikasi', '\App\Controllers\menu\NotifikasiController::index');
});
```

### Langkah B: Daftarkan Layout di Controller
Buka controller [NotifikasiController.php](file:///d:/laragon/www/konstruktor.id/app/Controllers/menu/NotifikasiController.php) dan tambahkan penanganan layout untuk role `'purchasing'`:

```php
// Tambahkan kondisi berikut di NotifikasiController::index()
if ($userRole === 'purchasing') {
    $layout = 'purchasing/layouts/main'; // Sesuaikan dengan path layout utama modul Purchasing
}
```

Halaman view [menu-notifikasi.php](file:///d:/laragon/www/konstruktor.id/app/Views/proyek/menu/menu-notifikasi.php) akan otomatis menggunakan layout modul Purchasing Anda dan me-load script interaktif [index.js](file:///d:/laragon/www/konstruktor.id/public/js/notifikasi/index.js) secara modular.

---

## 5. Skenario Trigger Notifikasi Terkait Purchasing

Berikut beberapa skenario ideal yang disarankan untuk modul Purchasing demi kelancaran alur kerja tim lapangan (Proyek) dan Gudang:

| Skenario Event di Purchasing | Penerima Notifikasi | Fungsi Trigger Notifikasi |
| :--- | :--- | :--- |
| **Purchasing menerima/memproses PR otomatis** | Divisi `gudang` (Role) | Beritahu gudang bahwa kekurangan stok sedang diproses oleh Purchasing untuk dicarikan vendor. |
| **Purchasing menerbitkan PO / Membeli Barang** | Divisi `gudang` (Role) | Informasikan nomor PO dan estimasi tanggal kedatangan barang ke gudang. |
| **Barang dibeli & dikirim ke Lapangan** | Pemohon Proyek (User ID) | Informasikan ke tim proyek pemohon bahwa material yang diajukan sudah dibeli dan sedang dikirim. |
| **PR Ditolak/Ditunda oleh Purchasing** | Divisi `gudang` (Role) | Berikan alasan penolakan/penundaan agar gudang bisa meneruskan informasi tersebut ke proyek. |

> [!TIP]
> Gunakan class ikon FontAwesome yang relevan seperti `fa-solid fa-file-signature` (untuk PO), `fa-solid fa-truck-ramp-box` (untuk pengiriman), atau `fa-solid fa-ban` (untuk penolakan).
