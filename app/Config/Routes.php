<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Frontend publik
$routes->get('/', 'Home::index');
$routes->get('tentang', 'TentangController::index');
$routes->get('wisata', 'WisataController::index');
$routes->get('wisata/(:segment)', 'WisataController::detail/$1');
$routes->get('artikel', 'ArtikelController::index');
$routes->get('artikel/(:segment)', 'ArtikelController::detail/$1');
$routes->get('galeri', 'GaleriController::index');
$routes->get('kontak', 'KontakController::index');
$routes->get('sitemap.xml', 'SitemapController::index');
$routes->get('robots.txt', 'SitemapController::robots');

// Admin auth (tanpa filter)
$routes->get('admin/login', 'Admin\AuthController::login');
$routes->post('admin/login', 'Admin\AuthController::attemptLogin');
$routes->get('admin/logout', 'Admin\AuthController::logout', ['filter' => 'adminauth']);

// Admin panel (butuh login)
$routes->group('admin', ['filter' => 'adminauth'], static function ($routes) {
    $routes->get('/', 'Admin\DashboardController::index');
    $routes->get('dashboard', 'Admin\DashboardController::index');

    // Artikel
    $routes->get('artikel', 'Admin\ArtikelAdminController::index');
    $routes->get('artikel/create', 'Admin\ArtikelAdminController::create');
    $routes->post('artikel/store', 'Admin\ArtikelAdminController::store');
    $routes->get('artikel/edit/(:num)', 'Admin\ArtikelAdminController::edit/$1');
    $routes->post('artikel/update/(:num)', 'Admin\ArtikelAdminController::update/$1');
    $routes->post('artikel/delete/(:num)', 'Admin\ArtikelAdminController::delete/$1');

    // Kategori
    $routes->get('kategori', 'Admin\KategoriAdminController::index');
    $routes->post('kategori/store', 'Admin\KategoriAdminController::store');
    $routes->post('kategori/update/(:num)', 'Admin\KategoriAdminController::update/$1');
    $routes->post('kategori/delete/(:num)', 'Admin\KategoriAdminController::delete/$1');

    // Wisata
    $routes->get('wisata', 'Admin\WisataAdminController::index');
    $routes->get('wisata/create', 'Admin\WisataAdminController::create');
    $routes->post('wisata/store', 'Admin\WisataAdminController::store');
    $routes->get('wisata/edit/(:num)', 'Admin\WisataAdminController::edit/$1');
    $routes->post('wisata/update/(:num)', 'Admin\WisataAdminController::update/$1');
    $routes->post('wisata/delete/(:num)', 'Admin\WisataAdminController::delete/$1');

    // Galeri
    $routes->get('galeri', 'Admin\GaleriAdminController::index');
    $routes->post('galeri/store', 'Admin\GaleriAdminController::store');
    $routes->post('galeri/update/(:num)', 'Admin\GaleriAdminController::update/$1');
    $routes->post('galeri/delete/(:num)', 'Admin\GaleriAdminController::delete/$1');

    // Banner beranda
    $routes->get('banner', 'Admin\BannerAdminController::index');
    $routes->post('banner/store', 'Admin\BannerAdminController::store');
    $routes->post('banner/update/(:num)', 'Admin\BannerAdminController::update/$1');
    $routes->post('banner/delete/(:num)', 'Admin\BannerAdminController::delete/$1');

    // Akun admin (hanya kelola role admin, superadmin disembunyikan)
    $routes->get('users', 'Admin\UserAdminController::index');
    $routes->get('users/create', 'Admin\UserAdminController::create');
    $routes->post('users/store', 'Admin\UserAdminController::store');
    $routes->get('users/edit/(:num)', 'Admin\UserAdminController::edit/$1');
    $routes->post('users/update/(:num)', 'Admin\UserAdminController::update/$1');
    $routes->post('users/delete/(:num)', 'Admin\UserAdminController::delete/$1');

    // Pengaturan
    $routes->get('pengaturan', 'Admin\PengaturanController::index');
    $routes->post('pengaturan/update', 'Admin\PengaturanController::update');
    // Superadmin only
    // $routes->group('', ['filter' => 'superadmin'], static function ($routes) {
    // });
});
