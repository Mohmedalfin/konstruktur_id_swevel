<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --------------------------------------------------------------------
// RUTE WEB (Menangani Tampilan / HTML / View)
// --------------------------------------------------------------------
$routes->group('', function ($routes) {

    // Landing Page
    $routes->get('/', 'Home::index');

    // Auth & Registrasi
    $routes->get('login', 'Home::login');
    $routes->get('Register', 'Home::register');
    $routes->get('registrasi', 'Registrasi::index');
    $routes->post('registrasi/simpan', 'Registrasi::simpan');

    // Proyek
    $routes->get('data-empiris', 'Proyek::dataEmpiris');
    $routes->get('proyek', 'ProyekController::index');
    $routes->get('proyek/create', 'ProyekController::create');
    $routes->post('proyek/store', 'ProyekController::store');
    $routes->get('proyek/edit/(:num)', 'ProyekController::edit/$1');
    $routes->post('proyek/update/(:num)', 'ProyekController::update/$1');

    // Detail proyek / menu RAP by slug
    $routes->get('proyek/(:segment)', 'menu\MenuRapController::index/$1');
    $routes->post('proyek/selesai/(:num)', 'ProyekController::selesai/$1');
    $routes->delete('proyek/delete/(:num)', 'ProyekController::destroy/$1');

    // Dashboard
    $routes->get('dashboard', 'menu\DashboardController::index');
    
    // Notifikasi
    $routes->get('notifikasi', 'menu\NotifikasiController::index');
    $routes->get('proyek/(:segment)/dashboard', 'menu\DashboardController::index/$1');
    $routes->get('proyek/(:segment)/dashboard/getData', 'menu\DashboardController::getData');
    $routes->get('proyek/(:segment)/dashboard/getCategoryDetail/(:num)', 'menu\DashboardController::getCategoryDetail/$2');

    // Menu RAP (RAB & RAP)
    $routes->get('menu-rap', 'menu\MenuRapController::index');
    $routes->get('menu-rap/rincian-ahs', 'menu\MenuRapController::rincianAHS');
    $routes->get('menu-rap/tambah-ahs', 'menu\MenuRapController::tambahAHS');
    $routes->get('menu-rap/tambah-pekerjaan', 'menu\MenuRapController::tambahPekerjaan');

    // Schedule
    $routes->get('schedule', 'menu\ScheduleController::index');
    $routes->get('proyek/(:segment)/schedule', 'menu\ScheduleController::index/$1');
    
    // Profile Perusahaan
    $routes->get('profile', 'menu\ProfileController::index');
    $routes->get('profile/data', 'menu\ProfileController::getData');
    $routes->post('profile/update', 'menu\ProfileController::update');

    // Kelola Akun Tim (Gudang & Purchasing)
    $routes->get('kelola-akun', 'menu\TeamAccountsController::index');
    $routes->get('kelola-akun/data', 'menu\TeamAccountsController::getSubAccounts');
    $routes->post('kelola-akun/create', 'menu\TeamAccountsController::createSubAccount');
    $routes->delete('kelola-akun/delete/(:num)', 'menu\TeamAccountsController::deleteSubAccount/$1');
    $routes->get('kelola-akun/invitations', 'menu\TeamAccountsController::getInvitations');
    $routes->delete('kelola-akun/delete-invitation/(:num)', 'menu\TeamAccountsController::deleteInvitation/$1');

    // Accept Invite
    $routes->get('accept-invite', 'menu\AcceptInviteController::index');
    $routes->post('accept-invite/submit', 'menu\AcceptInviteController::submit');

    // Realisasi
    $routes->get('realisasi', 'menu\RealisasiController::index');
    $routes->get('proyek/(:segment)/realisasi', 'menu\RealisasiController::index/$1');
    $routes->post('realisasi/(:segment)/store', 'menu\RealisasiController::store/$1');
    $routes->post('realisasi/(:segment)/store-sdm', 'menu\RealisasiController::storeSdm/$1');
    $routes->delete('realisasi/pekerjaan/log/(:num)', 'menu\RealisasiController::deleteLog/$1');
    $routes->delete('realisasi/sdm/item/(:num)', 'menu\RealisasiController::deleteSdmItem/$1');
    
    // Atur Urutan
    $routes->get('menu-rap/atur-urutan', 'menu\MenuRapController::aturUrutan');

    // Purchasing - Dashboard
    $routes->get('purchasing/dashboard', 'purchasing\DashboardController::index');

    // Purchasing - Purchase Request
    $routes->get('purchasing/purchase-request', 'purchasing\PurchaseRequestController::index');
    $routes->get('purchasing/purchase-request/detail/(:num)', 'purchasing\PurchaseRequestController::getDetail/$1');
    $routes->get('purchasing/purchase-request/pending/(:num)', 'purchasing\PurchaseRequestController::getPendingItems/$1');
    $routes->post('purchasing/purchase-request/generate-po', 'purchasing\PurchaseRequestController::generatePO');

    // Purchasing - PO Tracking
    $routes->get('purchasing/po-tracking', 'purchasing\POTrackingController::index');
    $routes->get('purchasing/po-tracking/detail/(:num)', 'purchasing\POTrackingController::getDetail/$1');
    $routes->put('purchasing/po-tracking/status/(:num)', 'purchasing\POTrackingController::updateStatus/$1');

    // Purchasing - Master Data
    $routes->get('purchasing/notification', 'purchasing\NotificationController::index');
    $routes->get('purchasing/master-data', 'purchasing\MasterDataController::index');
    $routes->post('purchasing/master-data/store', 'purchasing\MasterDataController::storeSupplier');
    $routes->put('purchasing/master-data/update/(:num)', 'purchasing\MasterDataController::updateSupplier/$1');
    $routes->delete('purchasing/master-data/delete/(:num)', 'purchasing\MasterDataController::deleteSupplier/$1');

    $routes->get('purchasing/master-data/material', 'purchasing\MasterDataController::material');
    $routes->post('purchasing/master-data/material/store', 'purchasing\MasterDataController::storeMaterial');
    $routes->put('purchasing/master-data/material/update/(:num)', 'purchasing\MasterDataController::updateMaterial/$1');
    $routes->delete('purchasing/master-data/material/delete/(:num)', 'purchasing\MasterDataController::deleteMaterial/$1');

    $routes->get('purchasing/master-data/harga', 'purchasing\MasterDataController::harga');
    $routes->post('purchasing/master-data/harga/store', 'purchasing\MasterDataController::storeHarga');
    $routes->put('purchasing/master-data/harga/update/(:num)', 'purchasing\MasterDataController::updateHarga/$1');
    $routes->delete('purchasing/master-data/harga/delete/(:num)', 'purchasing\MasterDataController::deleteHarga/$1');

    // Permintaan Barang ke Gudang
    $routes->get('permintaan', 'menu\PermintaanController::index');
    $routes->get('permintaan/create', 'menu\PermintaanController::create');
});


// --------------------------------------------------------------------
// RUTE API (Menangani Data Mentah JSON)
// --------------------------------------------------------------------
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    $routes->get('pekerjaan', 'PekerjaanController::index');
    $routes->post('pekerjaan/custom', 'PekerjaanController::storeCustom');
    $routes->put('pekerjaan/custom/(:num)', 'PekerjaanController::updateCustom/$1');
    $routes->delete('pekerjaan/custom/(:num)', 'PekerjaanController::deleteCustom/$1');
    
    // Wilayah & Template (untuk pemilihan harga resmi per proyek)
    $routes->get('wilayah', 'WilayahController::index');
    $routes->get('wilayah/provinces', 'WilayahController::provinces');
    $routes->get('wilayah/cities', 'WilayahController::cities');
    $routes->get('wilayah/templates', 'WilayahController::templates');

    $routes->get('ahs', 'AhsController::index');
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
    $routes->post('rap/pekerjaan/copy', 'RapController::copyPekerjaan');
    $routes->put('rap/reorder', 'RapController::reorderPekerjaan');
    $routes->post('rap/import', 'RapController::importBoq');
    $routes->put('rap/move', 'RapController::moveItem');
    $routes->post('rap/recalculate', 'RapController::recalculateFromAhs');
    $routes->delete('rap/reset/(:num)', 'RapController::reset/$1');

    $routes->post('rap/copy-ahs-estimator', 'RapController::copyAhsEstimator');
    $routes->put('rap/schedule-dates', '\App\Controllers\menu\ScheduleController::updateScheduleDates');


    $routes->get('stok/stats', '\App\Controllers\gudang\StokController::getStats');
    $routes->get('stok/data', '\App\Controllers\gudang\StokController::getData');
    $routes->put('stok/update-minimum', '\App\Controllers\gudang\StokController::updateMinimum');

    // Schedule Data
    $routes->get('schedule/data', '\App\Controllers\menu\ScheduleController::getData');

    // Realisasi Data
    $routes->get('realisasi/data', '\App\Controllers\menu\RealisasiController::getData');
    $routes->get('realisasi/sdm-resources', '\App\Controllers\menu\RealisasiController::getSdmResources');
    $routes->get('realisasi/sdm-data', '\App\Controllers\menu\RealisasiController::getSdmData');

    // Permintaan Gudang API
    $routes->get('permintaan/stats', '\App\Controllers\menu\PermintaanController::getStats');
    $routes->get('permintaan/data', '\App\Controllers\menu\PermintaanController::getData');
    $routes->get('permintaan/detail/(:num)', '\App\Controllers\menu\PermintaanController::getDetail/$1');
    $routes->post('permintaan/store', '\App\Controllers\menu\PermintaanController::store');
    $routes->post('permintaan/status/(:num)', '\App\Controllers\menu\PermintaanController::updateStatus/$1');
    $routes->post('permintaan/auto-procure/(:num)', '\App\Controllers\menu\PermintaanController::autoProcure/$1');
    $routes->delete('permintaan/delete/(:num)', '\App\Controllers\menu\PermintaanController::destroy/$1');
    $routes->get('permintaan/projects', '\App\Controllers\menu\PermintaanController::getProjects');
    $routes->get('permintaan/rap-items/(:num)', '\App\Controllers\menu\PermintaanController::getRapItems/$1');
});

// Modul Gudang Web Routes
$routes->group('gudang', function($routes) {
    $routes->get('/', '\App\Controllers\gudang\GudangController::dashboard');
    $routes->get('dashboard', '\App\Controllers\gudang\GudangController::dashboard');
    $routes->get('permintaan', '\App\Controllers\gudang\GudangController::permintaan');
    $routes->get('stok', '\App\Controllers\gudang\GudangController::stok');
    $routes->get('pengadaan', '\App\Controllers\gudang\GudangController::pengadaan');
    $routes->get('riwayat', '\App\Controllers\gudang\GudangController::riwayat');
});
