<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// --------------------------------------------------------------------
// RUTE WEB (Menangani Tampilan / HTML / View)
// --------------------------------------------------------------------

// 1. Rute Publik (Tanpa JWT)
$routes->group('', function ($routes) {
    // Auth & Registrasi
    $routes->get('/', 'AuthController::login');
    $routes->get('Register', 'AuthController::register');
    $routes->post('auth/process-register', 'AuthController::processRegister');
    $routes->post('auth/process-login', 'AuthController::processLogin');
    $routes->get('logout', 'AuthController::logout');
    $routes->get('registrasi', 'Registrasi::index');
    $routes->post('registrasi/simpan', 'Registrasi::simpan');
});

// 2. Rute Terlindungi (Harus lewat Session Auth)
$routes->group('', ['filter' => 'auth'], function ($routes) {
    // Proyek
    $routes->get('data-empiris', 'Proyek::dataEmpiris');
    $routes->get('/proyek', 'menu\ProyekController::index');
    $routes->get('/proyek/create', 'menu\ProyekController::create');
    $routes->post('/proyek/store', 'menu\ProyekController::store');
    $routes->post('/proyek/complete/(:num)', 'menu\ProyekController::complete/$1');
    $routes->post('/proyek/delete/(:num)', 'menu\ProyekController::delete/$1');

    // Dashboard
    $routes->get('/dashboard', 'menu\DashboardController::index');

    // Menu RAP (RAB & RAP)
    $routes->get('/menu-rap', 'menu\MenuRapController::index');
    $routes->get('/menu-rap/rincian-ahs', 'menu\MenuRapController::rincianAHS');
    $routes->get('/menu-rap/tambah-ahs', 'menu\MenuRapController::tambahAHS');

    // Schedule & Monitoring
    $routes->get('/schedule', 'ScheduleController::index');
    $routes->get('/monitoring', 'MonitoringController::index');
});


// --------------------------------------------------------------------
// RUTE API (Menangani Data Mentah JSON)
// --------------------------------------------------------------------
$routes->group('api', ['namespace' => 'App\Controllers\Api', 'filter' => 'auth'], function ($routes) {
    $routes->get('pekerjaan', 'PekerjaanController::index');
    $routes->post('pekerjaan/kustom', 'PekerjaanController::store');
    $routes->put('pekerjaan/kustom/(:num)', 'PekerjaanController::update/$1');
    $routes->delete('pekerjaan/kustom/(:num)', 'PekerjaanController::destroy/$1');

    $routes->get('ahs', 'AhsController::index');

    // Kategori Pekerjaan RAB
    $routes->get('kategori', 'KategoriController::index');
    $routes->post('kategori', 'KategoriController::create');
    $routes->put('kategori/(:num)', 'KategoriController::update/$1');
    $routes->delete('kategori/(:num)', 'KategoriController::delete/$1');

    // RAP — Rencana Anggaran Pelaksanaan
    $routes->get('rap',           'RapController::index');
    $routes->post('rap',          'RapController::store');
    $routes->put('rap/(:num)',    'RapController::update/$1');
    $routes->delete('rap/(:num)', 'RapController::destroy/$1');
});
