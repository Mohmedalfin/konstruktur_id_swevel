<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --------------------------------------------------------------------
// RUTE WEB (Menangani Tampilan / HTML / View)
// --------------------------------------------------------------------
$routes->group('', function ($routes) {

    // Auth & Registrasi
    $routes->get('/', 'Home::login');
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

    // Dashboard
    $routes->get('dashboard', 'menu\DashboardController::index');

    // Menu RAP (RAB & RAP)
    $routes->get('menu-rap', 'menu\MenuRapController::index');
    $routes->get('menu-rap/rincian-ahs', 'menu\MenuRapController::rincianAHS');
    $routes->get('menu-rap/tambah-pekerjaan', 'menu\MenuRapController::tambahPekerjaan');

    // Schedule & Monitoring
    $routes->get('schedule', 'ScheduleController::index');
    $routes->get('monitoring', 'MonitoringController::index');
    $routes->get('proyek/menu/main-pekerjaan', 'menu\MenuRapController::mainPekerjaan');
});


// --------------------------------------------------------------------
// RUTE API (Menangani Data Mentah JSON)
// --------------------------------------------------------------------
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    $routes->get('pekerjaan', 'PekerjaanController::index');
    $routes->get('ahs', 'AhsController::index');

    $routes->get('rap', 'RapController::index');

    $routes->get('rap/kategori-master', 'RapController::kategoriMaster');
    $routes->post('rap/kategori', 'RapController::tambahKategori');
    $routes->delete('rap/kategori/(:num)', 'RapController::deleteKategori/$1');

    $routes->post('rap/pekerjaan', 'RapController::tambahPekerjaan');
    $routes->delete('rap/pekerjaan/(:num)', 'RapController::deletePekerjaan/$1');

    $routes->post('rap/copy-ahs-estimator', 'RapController::copyAhsEstimator');
});