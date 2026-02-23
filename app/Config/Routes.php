<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('registrasi', 'Registrasi::index');
$routes->post('registrasi/simpan', 'Registrasi::simpan');

$routes->get('data-empiris', 'Proyek::dataEmpiris');
