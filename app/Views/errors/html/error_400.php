<?php

$statusCode  = '400';
$pageTitle   = 'Permintaan Tidak Valid';
$headline    = 'Permintaan Tidak Valid';
$icon        = 'fa-circle-exclamation';
$description = ENVIRONMENT !== 'production'
    ? 'Permintaan tidak dapat diproses oleh server.'
    : 'Maaf, permintaan Anda tidak valid. Silakan periksa kembali lalu coba lagi.';
$detailHtml  = null;

if (ENVIRONMENT !== 'production' && ! empty($message)) {
    $detailHtml = '<pre><code>' . esc($message) . '</code></pre>';
}

include __DIR__ . '/public_layout.php';
