<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    | Kredensial diambil dari environment variable.
    | Jangan hardcode credentials di sini.
    */

    'cloud_name' => env('CLOUDINARY_CLOUD_NAME', ''),
    'api_key' => env('CLOUDINARY_API_KEY', ''),
    'api_secret' => env('CLOUDINARY_API_SECRET', ''),
    'signed_urls' => env('CLOUDINARY_SIGNED_URLS', true),
    'max_upload_bytes' => 2 * 1024 * 1024,
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'webp'],
    'allowed_mime_types' => ['image/jpeg', 'image/png', 'image/webp'],

    /*
    | Folder tempat semua upload disimpan di Cloudinary.
    | Berguna untuk memisahkan aset per project.
    */
    'folder' => env('CLOUDINARY_FOLDER', 'cimuning'),
];
