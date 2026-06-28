<?php

namespace App\Support\Backup;

use App\Models\BackupRun;
use App\Models\User;
use App\Support\AdminActivityLogger;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;
use ZipArchive;

class DatabaseBackupService
{
    public function __construct(private readonly DatabaseDumper $dumper) {}

    public function create(User $admin, string $passphrase): BackupArtifact
    {
        $this->assertAvailable($admin, $passphrase);

        $cooldown = (int) config('backup.cooldown_minutes', 15);
        $rateKey = 'database-backup:admin:'.$admin->getKey();
        RateLimiter::hit($rateKey, $cooldown * 60);

        $lock = Cache::lock('database-backup:global', (int) config('backup.timeout_seconds', 120) + 60);

        if (! $lock->get()) {
            throw ValidationException::withMessages([
                'backup' => 'Backup lain sedang diproses. Tunggu hingga proses tersebut selesai.',
            ]);
        }

        $run = BackupRun::query()->create([
            'requested_by' => $admin->getKey(),
            'status' => 'processing',
        ]);

        AdminActivityLogger::record('database_backup_started', $admin, $run, 'Backup database #'.$run->id);

        $temporaryDirectory = storage_path('app/private/backup-temp/'.Str::uuid());
        $archiveDirectory = storage_path('app/private/backups');
        $sqlPath = $temporaryDirectory.'/database.sql';
        $manifestPath = $temporaryDirectory.'/manifest.json';
        $archivePath = $archiveDirectory.'/backup-'.$run->id.'-'.now()->utc()->format('Ymd-His').'.zip';

        try {
            $this->makeDirectory($temporaryDirectory);
            $this->makeDirectory($archiveDirectory);
            $this->dumper->dump($sqlPath);

            $sqlSize = filesize($sqlPath);

            if ($sqlSize === false || $sqlSize < 1 || $sqlSize > (int) config('backup.maximum_uncompressed_bytes', 1_073_741_824)) {
                throw new RuntimeException('Ukuran dump database tidak diizinkan.');
            }

            $databaseChecksum = hash_file('sha256', $sqlPath);

            if (! is_string($databaseChecksum)) {
                throw new RuntimeException('Checksum dump database gagal dibuat.');
            }

            $manifest = [
                'format_version' => 1,
                'application' => 'cimuning-digital-hub',
                'created_at' => now()->utc()->toIso8601String(),
                'database_sha256' => $databaseChecksum,
                'excluded_tables' => array_values((array) config('backup.excluded_tables', [])),
                'migration_count' => $this->migrationCount(),
            ];

            $manifestJson = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

            if (file_put_contents($manifestPath, $manifestJson, LOCK_EX) === false) {
                throw new RuntimeException('Manifest backup tidak dapat ditulis.');
            }

            $this->createEncryptedArchive($archivePath, $sqlPath, $manifestPath, $passphrase);

            $size = filesize($archivePath);
            $maximum = (int) config('backup.maximum_archive_bytes', 262_144_000);

            if ($size === false || $size < 1 || $size > $maximum) {
                throw new RuntimeException('Ukuran arsip backup tidak diizinkan.');
            }

            $checksum = hash_file('sha256', $archivePath);

            if (! is_string($checksum)) {
                throw new RuntimeException('Checksum arsip backup gagal dibuat.');
            }

            $run->update([
                'status' => 'completed',
                'file_name' => basename($archivePath),
                'size_bytes' => $size,
                'checksum_sha256' => $checksum,
                'manifest' => $manifest,
                'generated_at' => now(),
                'completed_at' => now(),
                'expires_at' => now()->addMinutes((int) config('backup.archive_ttl_minutes', 60)),
            ]);

            AdminActivityLogger::record(
                'database_backup_completed',
                $admin,
                $run,
                'Backup database #'.$run->id,
                metadata: ['size_bytes' => $size, 'checksum_sha256' => $checksum],
            );

            return new BackupArtifact($run->fresh(), $archivePath, basename($archivePath));
        } catch (Throwable $exception) {
            if (is_file($archivePath)) {
                @unlink($archivePath);
            }

            $failureCode = class_basename($exception);
            $run->update([
                'status' => 'failed',
                'failure_code' => Str::limit($failureCode, 100, ''),
                'failed_at' => now(),
            ]);

            AdminActivityLogger::record(
                'database_backup_failed',
                $admin,
                $run,
                'Backup database #'.$run->id,
                metadata: ['failure_code' => $failureCode],
            );

            throw $exception;
        } finally {
            $this->removeDirectory($temporaryDirectory);
            $lock->release();
        }
    }

    public function markDownloaded(BackupRun $run, User $admin): void
    {
        $run->update(['downloaded_at' => now()]);
        AdminActivityLogger::record('database_backup_downloaded', $admin, $run, 'Backup database #'.$run->id, metadata: [
            'checksum_sha256' => $run->checksum_sha256,
        ]);
    }

    public function cleanupExpired(): int
    {
        $removed = 0;
        $directory = storage_path('app/private/backups');

        if (is_dir($directory)) {
            foreach (glob($directory.'/*.zip') ?: [] as $path) {
                if (is_file($path) && filemtime($path) < now()->subMinutes((int) config('backup.archive_ttl_minutes', 60))->timestamp) {
                    $removed += @unlink($path) ? 1 : 0;
                }
            }
        }

        BackupRun::query()
            ->where('status', 'completed')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'expired']);

        BackupRun::query()
            ->where('status', 'processing')
            ->where('created_at', '<=', now()->subSeconds((int) config('backup.timeout_seconds', 120) + 60))
            ->update([
                'status' => 'failed',
                'failure_code' => 'interrupted_backup',
                'failed_at' => now(),
            ]);

        foreach (glob(storage_path('app/private/backup-temp/*')) ?: [] as $path) {
            if (is_dir($path) && filemtime($path) < now()->subHour()->timestamp) {
                $this->removeDirectory($path);
            }
        }

        return $removed;
    }

    private function assertAvailable(User $admin, string $passphrase): void
    {
        if (! config('backup.enabled')) {
            throw ValidationException::withMessages(['backup' => 'Fitur backup sedang dinonaktifkan.']);
        }

        if (! $admin->isAdmin() || ! $admin->hasActiveAccount()) {
            abort(403);
        }

        if (mb_strlen($passphrase) < 16) {
            throw ValidationException::withMessages(['passphrase' => 'Passphrase minimal 16 karakter.']);
        }

        $rateKey = 'database-backup:admin:'.$admin->getKey();

        if (RateLimiter::tooManyAttempts($rateKey, 1)) {
            throw ValidationException::withMessages([
                'backup' => 'Backup baru dapat dibuat lagi dalam '.RateLimiter::availableIn($rateKey).' detik.',
            ]);
        }

        if (! ZipArchive::isEncryptionMethodSupported(ZipArchive::EM_AES_256, true)) {
            throw ValidationException::withMessages([
                'backup' => 'Server tidak mendukung enkripsi ZIP AES-256. Backup dibatalkan tanpa membuat file plaintext.',
            ]);
        }
    }

    private function createEncryptedArchive(
        string $archivePath,
        string $sqlPath,
        string $manifestPath,
        string $passphrase,
    ): void {
        $zip = new ZipArchive;

        if ($zip->open($archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Arsip backup tidak dapat dibuat.');
        }

        try {
            foreach ([$sqlPath => 'database.sql', $manifestPath => 'manifest.json'] as $source => $name) {
                if (! $zip->addFile($source, $name)
                    || ! $zip->setEncryptionName($name, ZipArchive::EM_AES_256, $passphrase)) {
                    throw new RuntimeException('Enkripsi arsip backup gagal diterapkan.');
                }
            }
        } finally {
            if (! $zip->close()) {
                throw new RuntimeException('Arsip backup tidak dapat diselesaikan.');
            }
        }
    }

    private function migrationCount(): int
    {
        try {
            return DB::table('migrations')->count();
        } catch (Throwable) {
            return 0;
        }
    }

    private function makeDirectory(string $path): void
    {
        if (! is_dir($path) && ! mkdir($path, 0700, true) && ! is_dir($path)) {
            throw new RuntimeException('Direktori backup sementara tidak dapat dibuat.');
        }

        @chmod($path, 0700);
    }

    private function removeDirectory(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }

        foreach (scandir($path) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $target = $path.DIRECTORY_SEPARATOR.$entry;
            is_dir($target) ? $this->removeDirectory($target) : @unlink($target);
        }

        @rmdir($path);
    }
}
