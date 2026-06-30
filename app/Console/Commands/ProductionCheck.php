<?php

namespace App\Console\Commands;

use App\Support\Backup\MySqlDatabaseDumper;
use App\Support\CloudinaryStorage;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Throwable;
use ZipArchive;

class ProductionCheck extends Command
{
    protected $signature = 'app:production-check
        {--require-scheduler : Fail when the scheduler heartbeat is missing or stale}
        {--with-external : Authenticate against Cloudinary in addition to checking configuration}';

    protected $description = 'Validate production readiness without exposing credentials or changing business data';

    /** @var array<int, string> */
    private array $failures = [];

    /** @var array<int, string> */
    private array $warnings = [];

    public function handle(MySqlDatabaseDumper $dumper): int
    {
        $this->components->info('Cimuning Digital Hub production readiness');

        $this->check('Environment production', app()->environment('production'));
        $this->check('Debug mode nonaktif', ! (bool) config('app.debug'));
        $this->check('Timezone Asia/Jakarta', config('app.timezone') === 'Asia/Jakarta');
        $this->check('APP_URL menggunakan HTTPS', Str::startsWith((string) config('app.url'), 'https://'));
        $this->check('APP_KEY valid', $this->validAppKey());
        $this->check('Trusted host terkonfigurasi', config('production.trusted_hosts') !== []);
        $this->check('Session memakai database', config('session.driver') === 'database');
        $this->check('Cache memakai database', config('cache.default') === 'database');
        $this->check('Session cookie secure', (bool) config('session.secure'));
        $this->check('Session cookie terenkripsi', (bool) config('session.encrypt'));
        $this->check('Session cookie HttpOnly', (bool) config('session.http_only'));
        $this->check('Session SameSite aman', in_array(config('session.same_site'), ['lax', 'strict'], true));
        $this->check('Reset password email nonaktif', ! (bool) config('auth.password_reset_enabled'));
        $this->check('Seeder production nonaktif', ! (bool) config('production.run_database_seeders'));
        $this->check('Asset production tersedia', is_file(public_path('build/manifest.json')));

        $this->checkPhpExtensions();
        $this->checkWritableDirectories();
        $this->checkDatabaseAndMigrations();
        $this->checkCache();
        $this->checkCloudinary();
        $this->checkBackup($dumper);
        $this->checkScheduler();

        if ($this->warnings !== []) {
            $this->newLine();
            $this->components->warn('Peringatan:');

            foreach ($this->warnings as $warning) {
                $this->line('  - '.$warning);
            }
        }

        if ($this->failures !== []) {
            $this->newLine();
            $this->components->error(count($this->failures).' pemeriksaan wajib belum terpenuhi.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->components->info('Semua pemeriksaan wajib terpenuhi.');

        return self::SUCCESS;
    }

    private function checkPhpExtensions(): void
    {
        $extensions = [
            'ctype', 'dom', 'fileinfo', 'filter', 'hash', 'iconv', 'intl', 'json',
            'libxml', 'openssl', 'pcre', 'pdo_mysql', 'session', 'tokenizer',
            'xmlreader', 'zip',
        ];

        $missing = array_values(array_filter($extensions, fn (string $extension): bool => ! extension_loaded($extension)));
        $this->check('Extension PHP production lengkap', $missing === [], $missing === [] ? null : implode(', ', $missing));
    }

    private function checkWritableDirectories(): void
    {
        $directories = [storage_path(), storage_path('framework'), storage_path('logs'), base_path('bootstrap/cache')];
        $unwritable = [];

        foreach ($directories as $directory) {
            $probe = $directory.DIRECTORY_SEPARATOR.'.production-check-'.Str::random(16);

            try {
                if (! is_dir($directory) || @file_put_contents($probe, 'ok') !== 2) {
                    $unwritable[] = $directory;
                }
            } catch (Throwable) {
                $unwritable[] = $directory;
            } finally {
                if (is_file($probe)) {
                    @unlink($probe);
                }
            }
        }

        $this->check('Storage dan cache dapat ditulis', $unwritable === []);
    }

    private function checkDatabaseAndMigrations(): void
    {
        $this->check(
            'Database production MySQL/MariaDB',
            in_array(config('database.default'), ['mysql', 'mariadb'], true),
        );

        try {
            DB::select('select 1');
            $this->check('Koneksi database', true);

            $migrator = app('migrator');
            $repository = $migrator->getRepository();

            if (! $repository->repositoryExists()) {
                $this->check('Migration database lengkap', false);

                return;
            }

            $files = array_keys($migrator->getMigrationFiles(database_path('migrations')));
            $pending = array_diff($files, $repository->getRan());
            $this->check('Migration database lengkap', $pending === [], $pending === [] ? null : count($pending).' pending');
        } catch (Throwable $exception) {
            $this->check('Koneksi database', false, $exception::class);
        }
    }

    private function checkCache(): void
    {
        $key = 'production-check:'.bin2hex(random_bytes(8));

        try {
            Cache::put($key, 'ok', now()->addMinute());
            $healthy = Cache::get($key) === 'ok';
            Cache::forget($key);
            $this->check('Cache read/write', $healthy);
        } catch (Throwable $exception) {
            $this->check('Cache read/write', false, $exception::class);
        }
    }

    private function checkCloudinary(): void
    {
        $configured = collect([
            config('cloudinary.cloud_name'),
            config('cloudinary.api_key'),
            config('cloudinary.api_secret'),
        ])->every(fn ($value): bool => filled($value));

        $this->check('Credential Cloudinary lengkap', $configured);
        $this->check('Cloudinary signed URL aktif', (bool) config('cloudinary.signed_urls'));

        if (! $configured || ! $this->option('with-external')) {
            return;
        }

        try {
            $disk = Storage::disk('cloudinary');
            $authenticated = $disk instanceof CloudinaryStorage && $disk->ping();
            $this->check('Autentikasi Cloudinary', $authenticated);
        } catch (Throwable $exception) {
            $this->check('Autentikasi Cloudinary', false, $exception::class);
        }
    }

    private function checkBackup(MySqlDatabaseDumper $dumper): void
    {
        $this->check('Backup admin aktif', (bool) config('backup.enabled'));
        $this->check('Fungsi proc_open tersedia', function_exists('proc_open'));
        $this->check(
            'ZIP AES-256 tersedia',
            class_exists(ZipArchive::class) && ZipArchive::isEncryptionMethodSupported(ZipArchive::EM_AES_256),
        );

        try {
            $process = new Process([$dumper->binary(), '--version']);
            $process->setTimeout(10);
            $process->run();
            $this->check('Binary mysqldump tersedia', $process->isSuccessful());
        } catch (Throwable $exception) {
            $this->check('Binary mysqldump tersedia', false, $exception::class);
        }
    }

    private function checkScheduler(): void
    {
        $lastRun = Cache::get((string) config('production.scheduler_heartbeat_key'));
        $healthy = false;

        if (is_string($lastRun)) {
            try {
                $healthy = CarbonImmutable::parse($lastRun)
                    ->greaterThanOrEqualTo(now()->subMinutes((int) config('production.scheduler_max_age_minutes', 5)));
            } catch (Throwable) {
                $healthy = false;
            }
        }

        if ($healthy) {
            $this->check('Scheduler heartbeat aktif', true);

            return;
        }

        if ($this->option('require-scheduler')) {
            $this->check('Scheduler heartbeat aktif', false);

            return;
        }

        $this->warnings[] = 'Scheduler heartbeat belum terlihat. Jalankan cron schedule:run lalu ulangi dengan --require-scheduler.';
        $this->components->twoColumnDetail('Scheduler heartbeat aktif', '<fg=yellow;options=bold>WARN</>');
    }

    private function validAppKey(): bool
    {
        $key = (string) config('app.key');

        if (Str::startsWith($key, 'base64:')) {
            $decoded = base64_decode(Str::after($key, 'base64:'), true);
            $key = $decoded === false ? '' : $decoded;
        }

        return Encrypter::supported($key, (string) config('app.cipher'));
    }

    private function check(string $label, bool $passed, ?string $detail = null): void
    {
        $status = $passed ? '<fg=green;options=bold>PASS</>' : '<fg=red;options=bold>FAIL</>';
        $this->components->twoColumnDetail($label, $status);

        if (! $passed) {
            $this->failures[] = $detail ? "{$label}: {$detail}" : $label;
        }
    }
}
