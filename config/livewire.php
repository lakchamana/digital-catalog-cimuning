<?php

return [
    'temporary_file_upload' => [
        'disk' => env('LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK', 'local'),
        'rules' => [
            'required',
            'file',
            'mimes:jpg,jpeg,png,webp',
            'max:2048',
            'dimensions:max_width=5000,max_height=5000',
        ],
        'directory' => null,
        'middleware' => 'throttle:20,1',
        'preview_mimes' => [
            'png', 'jpg', 'jpeg', 'webp',
        ],
        'max_upload_time' => 5,
        'cleanup' => true,
    ],
];
