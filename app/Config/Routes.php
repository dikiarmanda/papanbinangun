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
$routes->get('download/apk', 'DownloadController::apk');
$routes->get('download/papan_binangun\.apk', 'DownloadController::apk');
$routes->get('papan_binangun\.apk', 'DownloadController::apk');

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

    $routes->group('users', ['filter' => 'superadmin'], static function ($routes) {
        $routes->get('/', 'Admin\UserAdminController::index');
        $routes->get('create', 'Admin\UserAdminController::create');
        $routes->post('store', 'Admin\UserAdminController::store');
        $routes->get('edit/(:num)', 'Admin\UserAdminController::edit/$1');
        $routes->post('update/(:num)', 'Admin\UserAdminController::update/$1');
        $routes->post('delete/(:num)', 'Admin\UserAdminController::delete/$1');
    });

    $routes->group('pengaturan', ['filter' => 'superadmin'], static function ($routes) {
        $routes->get('/', 'Admin\PengaturanController::index');
        $routes->post('update', 'Admin\PengaturanController::update');
    });

    $routes->group('transaksi', static function ($routes) {
        $routes->get('/', 'Admin\TransaksiAdminController::index');
        $routes->post('update-status', 'Admin\TransaksiAdminController::updateStatus');
    });
});

// -------------------------------------------------------------------------
// SEWU DATA — Auth (tanpa filter)
// -------------------------------------------------------------------------
$routes->get('sewu-data/login', 'SewuAuthController::login');
$routes->post('sewu-data/login', 'SewuAuthController::attemptLogin');
$routes->get('sewu-data/logout', 'SewuAuthController::logout');

// -------------------------------------------------------------------------
// SEWU DATA — Dashboard & API (butuh login)
// -------------------------------------------------------------------------
$routes->group('sewu-data', ['filter' => 'sewuauth'], static function ($routes) {
    $routes->get('/', 'SewuDataController::index');
    
    $routes->get('api/data', 'SewuDataApiController::data');
    $routes->get('api/statistik', 'SewuDataApiController::statistik');
    $routes->get('api/pedagang', 'SewuDataApiController::pedagang');

    // Admin SEWU DATA
    $routes->get('admin', 'SewuAdminController::index');
    $routes->post('admin/simpan', 'SewuAdminController::simpan');
    $routes->get('admin/hapus/(:num)', 'SewuAdminController::hapus/$1');
    $routes->get('admin/edit/(:num)', 'SewuAdminController::edit/$1');
    $routes->post('admin/update/(:num)', 'SewuAdminController::update/$1');
});

// -------------------------------------------------------------------------
// API REST MOBILE (Flutter) — Tanpa CSRF
// -------------------------------------------------------------------------
$routes->group('api', static function ($routes) {
    $routes->options('(:any)', 'Api\BaseApiController::options');

    // Auth
    $routes->post('auth/register(\.php)?', 'Api\AuthController::register');
    $routes->post('auth/login(\.php)?', 'Api\AuthController::login');

    // Kategori & Zona
    $routes->get('kategori/list(\.php)?', 'Api\KategoriController::list');
    $routes->get('zona/list(\.php)?', 'Api\ZonaController::list');

    // Produk
    $routes->get('produk/list(\.php)?', 'Api\ProdukController::list');
    $routes->get('produk/detail(\.php)?', 'Api\ProdukController::detail');

    // Homestay
    $routes->get('homestay/list(\.php)?', 'Api\HomestayController::list');
    $routes->get('homestay/detail(\.php)?', 'Api\HomestayController::detail');
    $routes->post('homestay/booking_create(\.php)?', 'Api\HomestayController::bookingCreate');
    $routes->get('homestay/booking_track(\.php)?', 'Api\HomestayController::bookingTrack');
    $routes->post('homestay/bukti_bayar(\.php)?', 'Api\HomestayController::buktiBayar');

    // Order
    $routes->post('order/create(\.php)?', 'Api\OrderController::create');
    $routes->get('order/detail(\.php)?', 'Api\OrderController::detail');
    $routes->get('order/track(\.php)?', 'Api\OrderController::track');
    $routes->post('order/bukti_bayar(\.php)?', 'Api\OrderController::buktiBayar');

    // Chat
    $routes->post('chat/kirim(\.php)?', 'Api\ChatController::kirim');
    $routes->get('chat/riwayat(\.php)?', 'Api\ChatController::riwayat');

    // Admin In-App
    $routes->post('admin/login(\.php)?', 'Api\AdminApiController::login');
    $routes->match(['get', 'post'], 'admin/list(\.php)?', 'Api\AdminApiController::list');
    $routes->post('admin/update_status(\.php)?', 'Api\AdminApiController::updateStatus');
    $routes->post('admin/chat_list(\.php)?', 'Api\AdminApiController::chatList');
    $routes->post('admin/chat_detail(\.php)?', 'Api\AdminApiController::chatDetail');
    $routes->post('admin/chat_balas(\.php)?', 'Api\AdminApiController::chatBalas');
});