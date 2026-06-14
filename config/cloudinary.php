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
    'api_key'    => env('CLOUDINARY_API_KEY', ''),
    'api_secret' => env('CLOUDINARY_API_SECRET', ''),

    /*
    | Folder tempat semua upload disimpan di Cloudinary.
    | Berguna untuk memisahkan aset per project.
    */
    'folder' => env('CLOUDINARY_FOLDER', 'cimuning'),
];
