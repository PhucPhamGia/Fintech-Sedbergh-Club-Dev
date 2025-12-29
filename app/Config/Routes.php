<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

# Dashboard
$routes->get('/', 'C_View::Dashboard');
$routes->post('/', 'C_View::Dashboard');

# Database
$routes->get('/database/(:segment)/(:num)', 'C_Database::Database/$1/$2');
$routes->post('/database/(:segment)/(:num)', 'C_Database::Database/$1/$2');

$routes->post('/importbinance', 'C_Database::Binance_Import');
$routes->get('importbinance', 'C_Database::Binance_Import');
$routes->post('importma20', 'C_Database::MA20');
$routes->post('importma50', 'C_Database::MA50');
$routes->post('importbinancedaily', 'C_Database::Binance_Daily_Import');

// Authentication
$routes->get('/login', 'C_View::Login');
$routes->post('/auth/login', 'C_Auth::Login_Post');

$routes->get('/login/forgot-password', 'C_View::Forgot_Password');
$routes->post('/auth/forgot-password', 'C_Auth::Forgot_Password');

$routes->get('/logout', 'C_Auth::Logout');

$routes->get('/register', 'C_View::Register');
$routes->post('/auth/register', 'C_Auth::Register_Post');