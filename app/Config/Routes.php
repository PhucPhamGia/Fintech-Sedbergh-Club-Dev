<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$routes->get('/', 'C_Dashboard::Model_Data');
$routes->post('/', 'C_Dashboard::Model_Data');

$routes->get('/database/(:segment)/(:num)', 'C_Database::Database/$1/$2');
$routes->post('/database/(:segment)/(:num)', 'C_Database::Database/$1/$2');

$routes->post('/importbinance', 'C_Database::Binance_Import');
$routes->get('importbinance', 'C_Database::Binance_Import');
$routes->post('importma20', 'C_Database::MA20');
$routes->post('importma50', 'C_Database::MA50');
$routes->post('importbinancedaily', 'C_Database::Binance_Daily_Import');
