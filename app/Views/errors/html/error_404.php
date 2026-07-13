<?php

$statusCode  = '404';
$pageTitle   = 'Halaman Tidak Ditemukan';
$headline    = 'Halaman Tidak Ditemukan';
$icon        = 'fa-map-location-dot';
$description = ENVIRONMENT !== 'production'
    ? 'Halaman yang diminta tidak ditemukan.'
    : 'Maaf, halaman yang Anda cari tidak dapat ditemukan atau mungkin sudah dipindahkan.';
$detailHtml  = null;

if (ENVIRONMENT !== 'production' && ! empty($message)) {
    $detailHtml = '<pre><code>' . esc($message) . '</code></pre>';
}

include __DIR__ . '/public_layout.php';
