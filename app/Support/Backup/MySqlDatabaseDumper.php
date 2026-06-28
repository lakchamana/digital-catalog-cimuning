<?php

namespace App\Support\Backup;

use RuntimeException;
use Symfony\Component\Process\Process;

class MySqlDatabaseDumper implements DatabaseDumper
{
    public function dump(string $outputPath): void
    {
        if (config('database.default') !== 'mysql') {
            throw new RuntimeException('Backup database hanya mendukung koneksi MySQL.');
        }

        $connection = config('database.connections.mysql');
        $optionFile = tempnam(dirname($outputPath), 'mysql-client-');

        if ($optionFile === false) {
            throw new RuntimeException('File konfigurasi sementara tidak dapat dibuat.');
        }

        try {
            $contents = "[client]\n"
                .'host='.$this->optionValue((string) $connection['host'])."\n"
                .'port='.(int) $connection['port']."\n"
                .'user='.$this->optionValue((string) $connection['username'])."\n"
                .'password='.$this->optionValue((string) $connection['password'])."\n"
                .'default-character-set=utf8mb4'."\n";

            if (file_put_contents($optionFile, $contents, LOCK_EX) === false) {
                throw new RuntimeException('File konfigurasi sementara tidak dapat ditulis.');
            }

            @chmod($optionFile, 0600);

            $process = new Process($this->command($optionFile, $outputPath, (string) $connection['database']));
            $process->setTimeout((int) config('backup.timeout_seconds', 120));
            $process->mustRun();

            if (! is_file($outputPath) || filesize($outputPath) === 0) {
                throw new RuntimeException('Dump database tidak menghasilkan data.');
            }
        } finally {
            if (is_file($optionFile)) {
                @unlink($optionFile);
            }
        }
    }

    /** @return array<int, string> */
    public function command(string $optionFile, string $outputPath, string $database): array
    {
        $command = [
            $this->binary(),
            '--defaults-extra-file='.$optionFile,
            '--single-transaction',
            '--quick',
            '--no-tablespaces',
            '--hex-blob',
            '--default-character-set=utf8mb4',
            '--result-file='.$outputPath,
        ];

        foreach ((array) config('backup.excluded_tables', []) as $table) {
            if (preg_match('/^[A-Za-z0-9_]+$/', (string) $table) !== 1) {
                throw new RuntimeException('Nama tabel pengecualian tidak aman.');
            }

            $command[] = '--ignore-table='.$database.'.'.$table;
        }

        $command[] = $database;

        return $command;
    }

    private function binary(): string
    {
        if (filled($configured = config('backup.mysqldump_path'))) {
            return (string) $configured;
        }

        $xampp = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';

        return PHP_OS_FAMILY === 'Windows' && is_file($xampp) ? $xampp : 'mysqldump';
    }

    private function optionValue(string $value): string
    {
        if (str_contains($value, "\0")) {
            throw new RuntimeException('Konfigurasi database tidak valid.');
        }

        return '"'.addcslashes($value, "\\\"\n\r").'"';
    }
}
