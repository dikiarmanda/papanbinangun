<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// -------------------------------------------------------------------------
// Frontend publik
// -------------------------------------------------------------------------
$routes->get('/', 'Home::index');
$routes->get('tentang', 'TentangController::index');
$routes->get('kontak', 'KontakController::index');
$routes->get('sitemap\.xml', 'SitemapController::index');
$routes->get('robots\.txt', 'SitemapController::robots');

$routes->group('wisata', static function ($routes) {
    $routes->get('/', 'WisataController::index');
    $routes->get('(:segment)', 'WisataController::detail/$1');
});

$routes->group('artikel', static function ($routes) {
    $routes->get('/', 'ArtikelController::index');
    $routes->get('(:segment)', 'ArtikelController::detail/$1');
});

$routes->get('galeri', 'GaleriController::index');

// -------------------------------------------------------------------------
// Admin auth (tanpa filter adminauth pada login)
// -------------------------------------------------------------------------
$routes->group('admin', static function ($routes) {
    $routes->get('login', 'Admin\AuthController::login');
    $routes->post('login', 'Admin\AuthController::attemptLogin');
    $routes->get('logout', 'Admin\AuthController::logout', ['filter' => 'adminauth']);
});

// -------------------------------------------------------------------------
// Admin panel (butuh login)
// -------------------------------------------------------------------------
$routes->group('admin', ['filter' => 'adminauth'], static function ($routes) {
    $routes->get('/', 'Admin\DashboardController::index');
    $routes->get('dashboard', 'Admin\DashboardController::index');

    $routes->group('artikel', static function ($routes) {
        $routes->get('/', 'Admin\ArtikelAdminController::index');
        $routes->get('create', 'Admin\ArtikelAdminController::create');
        $routes->post('store', 'Admin\ArtikelAdminController::store');
        $routes->get('edit/(:num)', 'Admin\ArtikelAdminController::edit/$1');
        $routes->post('update/(:num)', 'Admin\ArtikelAdminController::update/$1');
        $routes->post('delete/(:num)', 'Admin\ArtikelAdminController::delete/$1');
    });

    $routes->group('kategori', static function ($routes) {
        $routes->get('/', 'Admin\KategoriAdminController::index');
        $routes->post('store', 'Admin\KategoriAdminController::store');
        $routes->post('update/(:num)', 'Admin\KategoriAdminController::update/$1');
        $routes->post('delete/(:num)', 'Admin\KategoriAdminController::delete/$1');
    });

    $routes->group('wisata', static function ($routes) {
        $routes->get('/', 'Admin\WisataAdminController::index');
        $routes->get('create', 'Admin\WisataAdminController::create');
        $routes->post('store', 'Admin\WisataAdminController::store');
        $routes->get('edit/(:num)', 'Admin\WisataAdminController::edit/$1');
        $routes->post('update/(:num)', 'Admin\WisataAdminController::update/$1');
        $routes->post('delete/(:num)', 'Admin\WisataAdminController::delete/$1');
    });

    $routes->group('galeri', static function ($routes) {
        $routes->get('/', 'Admin\GaleriAdminController::index');
        $routes->post('store', 'Admin\GaleriAdminController::store');
        $routes->post('update/(:num)', 'Admin\GaleriAdminController::update/$1');
        $routes->post('delete/(:num)', 'Admin\GaleriAdminController::delete/$1');
    });

    $routes->group('banner', static function ($routes) {
        $routes->get('/', 'Admin\BannerAdminController::index');
        $routes->post('store', 'Admin\BannerAdminController::store');
        $routes->post('update/(:num)', 'Admin\BannerAdminController::update/$1');
        $routes->post('delete/(:num)', 'Admin\BannerAdminController::delete/$1');
    });

    $routes->group('users', static function ($routes) {
        $routes->get('/', 'Admin\UserAdminController::index');
        $routes->get('create', 'Admin\UserAdminController::create');
        $routes->post('store', 'Admin\UserAdminController::store');
        $routes->get('edit/(:num)', 'Admin\UserAdminController::edit/$1');
        $routes->post('update/(:num)', 'Admin\UserAdminController::update/$1');
        $routes->post('delete/(:num)', 'Admin\UserAdminController::delete/$1');
    });

    $routes->group('pengaturan', static function ($routes) {
        $routes->get('/', 'Admin\PengaturanController::index');
        $routes->post('update', 'Admin\PengaturanController::update');
    });
});
