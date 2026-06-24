<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --------------------------------------------------------------------
// RUTE WEB UTAMA (Landing & Auth)
// --------------------------------------------------------------------
$routes->group('', function ($routes) {
    // Landing Page
    $routes->get('/', 'Home::index');

    // Auth & Registrasi
    $routes->get('login', 'Home::login');
    $routes->post('auth/loginProcess', 'AuthController::loginProcess');
    $routes->get('logout', 'Home::logout');
    
    $routes->get('Register', 'Home::register');
    $routes->post('auth/registerProcess', 'AuthController::registerProcess');
    
    // Legacy route for reference
    $routes->get('registrasi', 'Registrasi::index');
    $routes->post('registrasi/simpan', 'Registrasi::simpan');

    // Data Empiris (Landing)
    $routes->get('data-empiris', 'Proyek::dataEmpiris');
});

// --------------------------------------------------------------------
// MODUL PROYEK & MENU UTAMA
// --------------------------------------------------------------------
$routes->group('', function ($routes) {
    // Proyek CRUD
    $routes->get('proyek', 'ProyekController::index');
    $routes->get('proyek/create', 'ProyekController::create');
    $routes->post('proyek/store', 'ProyekController::store');
    $routes->get('proyek/edit/(:num)', 'ProyekController::edit/$1');
    $routes->post('proyek/update/(:num)', 'ProyekController::update/$1');
    $routes->post('proyek/selesai/(:num)', 'ProyekController::selesai/$1');
    $routes->delete('proyek/delete/(:num)', 'ProyekController::destroy/$1');

    // Proyek Dashboard Context
    $routes->get('proyek/(:segment)', 'menu\DashboardController::index/$1');
    $routes->get('proyek/(:segment)/dashboard', 'menu\DashboardController::index/$1');
    $routes->get('proyek/(:segment)/dashboard/getData', 'menu\DashboardController::getData');
    $routes->get('proyek/(:segment)/dashboard/getCategoryDetail/(:num)', 'menu\DashboardController::getCategoryDetail/$2');
    
    // Global Dashboard
    $routes->get('dashboard', 'menu\DashboardController::index');

    // Menu RAP
    $routes->get('menu-rap', 'menu\MenuRapController::index');
    $routes->get('proyek/(:segment)/rap', 'menu\MenuRapController::index/$1');
    $routes->get('menu-rap/rincian-ahs', 'menu\MenuRapController::rincianAHS');
    $routes->get('menu-rap/tambah-ahs', 'menu\MenuRapController::tambahAHS');
    $routes->get('menu-rap/tambah-pekerjaan', 'menu\MenuRapController::tambahPekerjaan');
    $routes->get('menu-rap/atur-urutan', 'menu\MenuRapController::aturUrutan');

    // Schedule
    $routes->get('schedule', 'menu\ScheduleController::index');
    $routes->get('proyek/(:segment)/schedule', 'menu\ScheduleController::index/$1');
    
    // Realisasi
    $routes->get('realisasi', 'menu\RealisasiController::index');
    $routes->get('proyek/(:segment)/realisasi', 'menu\RealisasiController::index/$1');
    $routes->post('realisasi/(:segment)/store', 'menu\RealisasiController::store/$1');
    $routes->post('realisasi/(:segment)/store-sdm', 'menu\RealisasiController::storeSdm/$1');
    $routes->delete('realisasi/pekerjaan/log/(:num)', 'menu\RealisasiController::deleteLog/$1');
    $routes->delete('realisasi/sdm/item/(:num)', 'menu\RealisasiController::deleteSdmItem/$1');

    // Permintaan Barang ke Gudang
    $routes->get('permintaan', 'menu\PermintaanController::index');
    $routes->get('permintaan/deviasi', 'menu\PermintaanController::deviasi');
    $routes->get('permintaan/create', 'menu\PermintaanController::create');

    // Gudang Lapangan (Site Inventory per Proyek)
    $routes->get('proyek/(:segment)/gudang-lapangan', 'menu\GudangLapanganController::index/$1');

    // Notifikasi
    $routes->get('notifikasi', 'menu\NotifikasiController::index');
});

// --------------------------------------------------------------------
// PENGATURAN AKUN & PROFILE
// --------------------------------------------------------------------
$routes->group('', function ($routes) {
    // Profile
    $routes->get('profile', 'menu\ProfileController::index');
    $routes->get('profile/data', 'menu\ProfileController::getData');
    $routes->post('profile/update', 'menu\ProfileController::update');

    // Kelola Akun Tim
    $routes->get('kelola-akun', 'menu\TeamAccountsController::index');
    $routes->get('kelola-akun/data', 'menu\TeamAccountsController::getSubAccounts');
    $routes->post('kelola-akun/create', 'menu\TeamAccountsController::createSubAccount');
    $routes->delete('kelola-akun/delete/(:num)', 'menu\TeamAccountsController::deleteSubAccount/$1');
    $routes->get('kelola-akun/invitations', 'menu\TeamAccountsController::getInvitations');
    $routes->delete('kelola-akun/delete-invitation/(:num)', 'menu\TeamAccountsController::deleteInvitation/$1');

    // Accept Invite
    $routes->get('accept-invite', 'menu\AcceptInviteController::index');
    $routes->post('accept-invite/submit', 'menu\AcceptInviteController::submit');
});

// --------------------------------------------------------------------
// MODUL GUDANG (Pusat)
// --------------------------------------------------------------------
$routes->group('gudang', function($routes) {
    $routes->get('/', '\App\Controllers\gudang\GudangController::dashboard');
    $routes->get('dashboard', '\App\Controllers\gudang\GudangController::dashboard');
    $routes->get('permintaan', '\App\Controllers\gudang\GudangController::permintaan');
    $routes->get('stok', '\App\Controllers\gudang\GudangController::stok');
    $routes->get('pengadaan', '\App\Controllers\gudang\GudangController::pengadaan');
    $routes->get('riwayat', '\App\Controllers\gudang\GudangController::riwayat');
    
    // Shared Menu for Gudang
    $routes->get('notifikasi', '\App\Controllers\gudang\GudangController::notifikasi');
    $routes->get('profile', '\App\Controllers\menu\ProfileController::index');
});

// --------------------------------------------------------------------
// MODUL PURCHASING
// --------------------------------------------------------------------
$routes->group('purchasing', function($routes) {
    // Dashboard
    $routes->get('dashboard', '\App\Controllers\purchasing\DashboardController::index');

    // Purchase Request
    $routes->get('purchase-request', '\App\Controllers\purchasing\PurchaseRequestController::index');
    $routes->get('purchase-request/detail/(:num)', '\App\Controllers\purchasing\PurchaseRequestController::getDetail/$1');
    $routes->get('purchase-request/pending/(:num)', '\App\Controllers\purchasing\PurchaseRequestController::getPendingItems/$1');
    $routes->post('purchase-request/generate-po', '\App\Controllers\purchasing\PurchaseRequestController::generatePO');

    // PO Tracking
    $routes->get('po-tracking', '\App\Controllers\purchasing\POTrackingController::index');
    $routes->get('po-tracking/detail/(:num)', '\App\Controllers\purchasing\POTrackingController::getDetail/$1');
    $routes->put('po-tracking/status/(:num)', '\App\Controllers\purchasing\POTrackingController::updateStatus/$1');

    // Master Data
    $routes->get('master-data', '\App\Controllers\purchasing\MasterDataController::index');
    
    // Master Data - Supplier
    $routes->post('master-data/store', '\App\Controllers\purchasing\MasterDataController::storeSupplier');
    $routes->put('master-data/update/(:num)', '\App\Controllers\purchasing\MasterDataController::updateSupplier/$1');
    $routes->delete('master-data/delete/(:num)', '\App\Controllers\purchasing\MasterDataController::deleteSupplier/$1');

    // Master Data - Material
    $routes->get('master-data/material', '\App\Controllers\purchasing\MasterDataController::material');
    $routes->post('master-data/material/store', '\App\Controllers\purchasing\MasterDataController::storeMaterial');
    $routes->put('master-data/material/update/(:num)', '\App\Controllers\purchasing\MasterDataController::updateMaterial/$1');
    $routes->delete('master-data/material/delete/(:num)', '\App\Controllers\purchasing\MasterDataController::deleteMaterial/$1');

    // Master Data - Harga
    $routes->get('master-data/harga', '\App\Controllers\purchasing\MasterDataController::harga');
    $routes->post('master-data/harga/store', '\App\Controllers\purchasing\MasterDataController::storeHarga');
    $routes->put('master-data/harga/update/(:num)', '\App\Controllers\purchasing\MasterDataController::updateHarga/$1');
    $routes->delete('master-data/harga/delete/(:num)', '\App\Controllers\purchasing\MasterDataController::deleteHarga/$1');

    // Shared Menu for Purchasing
    $routes->get('notifikasi', '\App\Controllers\menu\NotifikasiController::index');
    $routes->get('profile', '\App\Controllers\menu\ProfileController::index');
});

// --------------------------------------------------------------------
// RUTE API (Menangani Data Mentah JSON)
// --------------------------------------------------------------------
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    
    // --- PROYEK & RAP API ---
    $routes->get('proyek/aktif', '\App\Controllers\ProyekController::getActiveProjects');
    $routes->post('proyek/selesai-reconcile/(:num)', '\App\Controllers\ProyekController::selesaiReconcile/$1');

    $routes->get('pekerjaan', 'PekerjaanController::index');
    $routes->post('pekerjaan/custom', 'PekerjaanController::storeCustom');
    $routes->put('pekerjaan/custom/(:num)', 'PekerjaanController::updateCustom/$1');
    $routes->delete('pekerjaan/custom/(:num)', 'PekerjaanController::deleteCustom/$1');
    
    $routes->get('wilayah', 'WilayahController::index');
    $routes->get('wilayah/provinces', 'WilayahController::provinces');
    $routes->get('wilayah/cities', 'WilayahController::cities');
    $routes->get('wilayah/templates', 'WilayahController::templates');

    $routes->get('ahs', 'AhsController::index');
    $routes->get('ahs/search-master-barang', 'AhsController::searchMasterBarang');
    $routes->get('ahs/proyek', 'AhsController::getProyek');
    $routes->get('ahs/shbj', 'AhsController::getShbj');
    $routes->get('ahs/survey', 'AhsController::getSurvey');
    $routes->get('ahs/estimatorid', 'AhsController::getEstimatorId');
    $routes->get('ahs/rincian/(:num)', 'AhsController::getRincian/$1');
    $routes->post('ahs/rincian', 'AhsController::saveRincian');
    $routes->delete('ahs/rincian/item/(:num)', 'AhsController::deleteItem/$1');

    $routes->get('rap', 'RapController::index');
    $routes->put('rap/format_penomoran', 'RapController::updateFormatPenomoran');
    $routes->get('rap/kategori-master', 'RapController::kategoriMaster');
    $routes->put('rap/kategori-master/(:num)', 'RapController::updateKategoriMaster/$1');
    $routes->delete('rap/kategori-master/(:num)', 'RapController::deleteKategoriMaster/$1');
    $routes->post('rap/kategori', 'RapController::tambahKategori');
    $routes->delete('rap/kategori/(:num)', 'RapController::deleteKategori/$1');
    $routes->post('rap/pekerjaan', 'RapController::tambahPekerjaan');
    $routes->delete('rap/pekerjaan/(:num)', 'RapController::deletePekerjaan/$1');
    $routes->put('rap/pekerjaan/(:num)/volume', 'RapController::updateVolumePekerjaan/$1');
    $routes->post('rap/pekerjaan/copy', 'RapController::copyPekerjaan');
    $routes->put('rap/reorder', 'RapController::reorderPekerjaan');
    $routes->post('rap/import', 'RapController::importBoq');
    $routes->put('rap/move', 'RapController::moveItem');
    $routes->post('rap/recalculate', 'RapController::recalculateFromAhs');
    $routes->delete('rap/reset/(:num)', 'RapController::reset/$1');
    $routes->post('rap/copy-ahs-estimator', 'RapController::copyAhsEstimator');
    
    // --- SCHEDULE API ---
    $routes->put('rap/schedule-dates', '\App\Controllers\menu\ScheduleController::updateScheduleDates');
    $routes->get('schedule/data', '\App\Controllers\menu\ScheduleController::getData');

    // --- REALISASI API ---
    $routes->get('realisasi/data', '\App\Controllers\menu\RealisasiController::getData');
    $routes->get('realisasi/sdm-resources', '\App\Controllers\menu\RealisasiController::getSdmResources');
    $routes->get('realisasi/sdm-data', '\App\Controllers\menu\RealisasiController::getSdmData');

    // --- GUDANG LAPANGAN API ---
    $routes->get('gudang-lapangan/stok',  '\App\Controllers\menu\GudangLapanganController::getStok');
    $routes->get('gudang-lapangan/kartu', '\App\Controllers\menu\GudangLapanganController::getKartu');
    $routes->post('gudang-lapangan/retur', '\App\Controllers\menu\GudangLapanganController::retur');
    $routes->get('gudang-lapangan/sisa-stok/(:num)', '\App\Controllers\menu\GudangLapanganController::getSisaStok/$1');

    // --- PERMINTAAN GUDANG (Proyek -> Gudang) API ---
    $routes->get('permintaan/stats', '\App\Controllers\menu\PermintaanController::getStats');
    $routes->get('permintaan/deviasi-data', '\App\Controllers\menu\PermintaanController::getDeviasiData');
    $routes->get('permintaan/data', '\App\Controllers\menu\PermintaanController::getData');
    $routes->get('permintaan/detail/(:num)', '\App\Controllers\menu\PermintaanController::getDetail/$1');
    $routes->post('permintaan/store', '\App\Controllers\menu\PermintaanController::store');
    $routes->post('permintaan/status/(:num)', '\App\Controllers\menu\PermintaanController::updateStatus/$1');
    $routes->post('permintaan/auto-procure/(:num)', '\App\Controllers\menu\PermintaanController::autoProcure/$1');
    $routes->delete('permintaan/delete/(:num)', '\App\Controllers\menu\PermintaanController::destroy/$1');
    $routes->get('permintaan/projects', '\App\Controllers\menu\PermintaanController::getProjects');
    $routes->get('permintaan/rap-items/(:num)', '\App\Controllers\menu\PermintaanController::getRapItems/$1');

    // --- GUDANG (Pusat) API ---
    $routes->get('gudang/dashboard/data', '\App\Controllers\gudang\GudangController::getDashboardData');
    $routes->get('stok/stats', '\App\Controllers\gudang\StokController::getStats');
    $routes->get('stok/data', '\App\Controllers\gudang\StokController::getData');
    $routes->put('stok/update-minimum', '\App\Controllers\gudang\StokController::updateMinimum');
    
    $routes->get('pengadaan/stats', '\App\Controllers\gudang\PengadaanController::getStats');
    $routes->get('pengadaan/data', '\App\Controllers\gudang\PengadaanController::getData');
    $routes->get('pengadaan/detail/(:num)', '\App\Controllers\gudang\PengadaanController::getDetail/$1');
    $routes->get('pengadaan/items-kritis', '\App\Controllers\gudang\PengadaanController::getItemsKritis');
    $routes->get('pengadaan/search-barang', '\App\Controllers\gudang\PengadaanController::searchBarang');
    $routes->post('pengadaan/store', '\App\Controllers\gudang\PengadaanController::store');
    $routes->delete('pengadaan/delete/(:num)', '\App\Controllers\gudang\PengadaanController::delete/$1');

    // --- NOTIFIKASI API ---
    $routes->get('notifications', 'NotifikasiApiController::index');
    $routes->get('notifications/unread', 'NotifikasiApiController::getUnread');
    $routes->post('notifications/mark-read/(:num)', 'NotifikasiApiController::markAsRead/$1');
    $routes->post('notifications/mark-all-read', 'NotifikasiApiController::markAllAsRead');
    $routes->delete('notifications/delete/(:num)', 'NotifikasiApiController::delete/$1');
});
