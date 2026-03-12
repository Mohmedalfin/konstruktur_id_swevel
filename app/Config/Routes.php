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
    $routes->get('/proyek', 'ProyekController::index');
    $routes->get('/proyek/create', 'ProyekController::create');
    $routes->post('/proyek/store', 'ProyekController::store');
    $routes->get('/proyek/edit/(:num)', 'ProyekController::edit/$1');
    $routes->post('/proyek/update/(:num)', 'ProyekController::update/$1');

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
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    $routes->get('pekerjaan', 'PekerjaanController::index');
    $routes->get('ahs', 'AhsController::index');
});
