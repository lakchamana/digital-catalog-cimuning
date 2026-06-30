<?php

use App\Support\CloudinaryStorage;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Command\Command;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('media:diagnose {--upload : Upload, verify, and remove a temporary diagnostic image}', function (): int {
    $this->info('Permanent disk: '.config('filesystems.default'));
    $this->info('Temporary upload disk: '.(config('livewire.temporary_file_upload.disk') ?: config('filesystems.default')));
    $this->info('Transfer mode: multipart-stream');
    $this->info('Delivery transformation: f_auto/q_auto (no resize or crop)');
    $this->info('Signed delivery URLs: '.(config('cloudinary.signed_urls', true) ? 'enabled' : 'disabled'));

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

        if (! config('cloudinary.signed_urls', true)) {
            $this->error('Signed delivery URLs wajib aktif untuk konfigurasi production.');

            return Command::FAILURE;
        }

        if (! $this->option('upload')) {
            return Command::SUCCESS;
        }

        $path = 'diagnostics/media-diagnose-'.bin2hex(random_bytes(8)).'.png';
        $png = hex2bin('89504e470d0a1a0a0000000d4948445200000001000000010804000000b51c0c020000000b4944415478da63fcff1f0002eb01f569769cab0000000049454e44ae426082');
        $stream = fopen('php://temp', 'r+');
        $uploaded = false;
        $failed = false;

        if ($png === false || $stream === false) {
            $this->error('Gagal menyiapkan gambar diagnostik di memory.');

            return Command::FAILURE;
        }

        fwrite($stream, $png);
        rewind($stream);

        try {
            $uploaded = (bool) $disk->put($path, $stream);

            if (! $uploaded) {
                $this->error('Diagnostic upload: FAILED');
                $failed = true;
            } else {
                $this->info('Diagnostic upload: OK');
                $url = $disk->url($path);
                $validUrl = str_contains($url, '/f_auto/q_auto/')
                    && preg_match('#/s--[A-Za-z0-9_-]+--/#', $url) === 1
                    && preg_match('#/(?:w|h|c)_#', $url) !== 1;

                if (! $validUrl) {
                    $this->error('Signed delivery URL validation: FAILED');
                    $failed = true;
                } else {
                    $this->info('Signed delivery URL validation: OK');
                    $response = Http::timeout(15)->retry(3, 500, throw: false)->head($url);

                    if (! $response->successful()) {
                        $this->error('Diagnostic delivery: FAILED (HTTP '.$response->status().')');
                        $failed = true;
                    } else {
                        $this->info('Diagnostic delivery: OK');
                    }
                }
            }
        } catch (Throwable $exception) {
            $this->error('Diagnostic upload: FAILED ('.$exception::class.')');
            $failed = true;
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }

            if ($uploaded) {
                if ($disk->delete($path)) {
                    $this->info('Diagnostic cleanup: OK');
                } else {
                    $this->error('Diagnostic cleanup: FAILED');
                    $failed = true;
                }
            }
        }

        return $failed ? Command::FAILURE : Command::SUCCESS;
    } catch (Throwable $exception) {
        $this->error('Cloudinary authentication: FAILED ('.$exception::class.')');

        return Command::FAILURE;
    }
})->purpose('Check media disk configuration and Cloudinary authentication without exposing secrets');

// Effective when a scheduler worker/cron is configured. Admin actions also run cleanup defensively.
Schedule::call(function (): void {
    Cache::put(
        config('production.scheduler_heartbeat_key'),
        now()->toIso8601String(),
        now()->addMinutes(10),
    );
})
    ->name('production:scheduler-heartbeat')
    ->everyMinute()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('backup:cleanup')
    ->hourly()
    ->withoutOverlapping()
    ->onOneServer();
