<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// login & register
$routes->get('/', 'Home::login');
$routes->get('Register', 'Home::register');

$routes->get('registrasi', 'Registrasi::index');
$routes->post('registrasi/simpan', 'Registrasi::simpan');

$routes->get('data-empiris', 'Proyek::dataEmpiris');

$routes->get('/proyek', 'ProyekController::index');
$routes->get('/proyek/create', 'ProyekController::create');
$routes->post('/proyek/store', 'ProyekController::store');

// menu rap
$routes->get('/menu-rap', 'menu\MenuRapController::index');
$routes->get('/menu-rap/create', 'menu\MenuRapController::create');
$routes->post('/menu-rap/store', 'menu\MenuRapController::store');

