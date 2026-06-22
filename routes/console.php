<?php

use App\Support\CloudinaryStorage;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Command\Command;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('media:diagnose', function (): int {
    $this->info('Permanent disk: '.config('filesystems.default'));
    $this->info('Temporary upload disk: '.(config('livewire.temporary_file_upload.disk') ?: config('filesystems.default')));

    $credentials = [
        'CLOUDINARY_CLOUD_NAME' => config('cloudinary.cloud_name'),
        'CLOUDINARY_API_KEY' => config('cloudinary.api_key'),
        'CLOUDINARY_API_SECRET' => config('cloudinary.api_secret'),
    ];
    $missing = [];

    foreach ($credentials as $name => $value) {
        $configured = filled($value);
        $this->line($name.': '.($configured ? 'configured' : 'missing'));

        if (! $configured) {
            $missing[] = $name;
        }
    }

    if ($missing !== []) {
        $this->error('Diagnosis dihentikan karena konfigurasi Cloudinary belum lengkap.');

        return Command::FAILURE;
    }

    try {
        $disk = Storage::disk('cloudinary');

        if (! $disk instanceof CloudinaryStorage || ! $disk->ping()) {
            $this->error('Autentikasi Cloudinary tidak memberikan status yang diharapkan.');

            return Command::FAILURE;
        }

        $this->info('Cloudinary authentication: OK');

        return Command::SUCCESS;
    } catch (Throwable $exception) {
        $this->error('Cloudinary authentication: FAILED ('.$exception::class.')');

        return Command::FAILURE;
    }
})->purpose('Check media disk configuration and Cloudinary authentication without exposing secrets');
