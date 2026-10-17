<?php
// -------------------------------------------------------------------------
// Admin panel (butuh login)
// -------------------------------------------------------------------------
$routes->group('admin', ['filter' => 'adminauth'], static function ($routes) {
    // ... semua route admin ...
    
    $routes->group('pengaturan', static function ($routes) {
        $routes->get('/', 'Admin\PengaturanController::index');
        $routes->post('update', 'Admin\PengaturanController::update');
    });
});  // ← TUTUP GRUP ADMIN DI SINI (cuma 1)

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
});