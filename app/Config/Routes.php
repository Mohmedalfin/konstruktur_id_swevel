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
    $routes->get('notifikasi', 'NotifikasiController::index');
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

    // Realisasi
    $routes->get('realisasi', 'menu\RealisasiController::index');
    $routes->get('proyek/(:segment)/realisasi', 'menu\RealisasiController::index/$1');
    $routes->post('realisasi/(:segment)/store', 'menu\RealisasiController::store/$1');
    $routes->post('realisasi/(:segment)/store-sdm', 'menu\RealisasiController::storeSdm/$1');
    $routes->delete('realisasi/pekerjaan/log/(:num)', 'menu\RealisasiController::deleteLog/$1');
    $routes->delete('realisasi/sdm/item/(:num)', 'menu\RealisasiController::deleteSdmItem/$1');
    
    // Atur Urutan
    $routes->get('menu-rap/atur-urutan', 'menu\MenuRapController::aturUrutan');
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


    // Schedule Data
    $routes->get('schedule/data', '\App\Controllers\menu\ScheduleController::getData');

    // Realisasi Data
    $routes->get('realisasi/data', '\App\Controllers\menu\RealisasiController::getData');
    $routes->get('realisasi/sdm-resources', '\App\Controllers\menu\RealisasiController::getSdmResources');
    $routes->get('realisasi/sdm-data', '\App\Controllers\menu\RealisasiController::getSdmData');
});
