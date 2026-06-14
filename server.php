<?php

/**
 * Router script untuk PHP built-in server di Railway.
 *
 * PHP built-in server punya quirk: request dengan ekstensi file (.js, .css, dll)
 * yang tidak ada sebagai file statis langsung di-404, tanpa diteruskan ke index.php.
 * Ini menyebabkan route Livewire (/livewire-xxx/livewire.js) dan Filament gagal.
 *
 * Script ini memeriksa apakah file statis benar-benar ada di public/.
 * Jika ada → serve langsung. Jika tidak → forward ke public/index.php (Laravel).
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Jika file statis benar-benar ada di folder public, serve langsung
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    // Tentukan MIME type yang benar
    $mimeTypes = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'json' => 'application/json',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'webp' => 'image/webp',
        'ico'  => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'  => 'font/ttf',
        'eot'  => 'application/vnd.ms-fontobject',
        'map'  => 'application/json',
    ];

    $ext = strtolower(pathinfo($uri, PATHINFO_EXTENSION));

    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
        readfile(__DIR__ . '/public' . $uri);
        return true;
    }

    // Untuk ekstensi lain yang tidak dikenal, biarkan PHP serve langsung
    return false;
}

// Semua request lain (termasuk /livewire-xxx/livewire.js yang BUKAN file statis)
// diteruskan ke Laravel via public/index.php
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/public/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';

require __DIR__ . '/public/index.php';
