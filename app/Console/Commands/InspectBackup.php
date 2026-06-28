<?php

namespace App\Console\Commands;

use App\Support\Backup\BackupArchiveInspector;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

class InspectBackup extends Command
{
    protected $signature = 'backup:inspect {file : Path to an encrypted backup ZIP}';

    protected $description = 'Validate an encrypted backup archive without executing SQL';

    public function handle(BackupArchiveInspector $inspector): int
    {
        $path = (string) $this->argument('file');
        $passphrase = (string) $this->secret('Passphrase arsip');

        try {
            $result = $inspector->inspect($path, $passphrase);
        } catch (ValidationException $exception) {
            $this->error(collect($exception->errors())->flatten()->first() ?? 'Arsip tidak valid.');

            return self::FAILURE;
        }

        $this->info('Arsip valid. SQL tidak dijalankan.');
        $this->table(['Atribut', 'Nilai'], [
            ['Checksum ZIP', $result['checksum_sha256']],
            ['Ukuran', $result['size_bytes'].' byte'],
            ['Dibuat', $result['manifest']['created_at'] ?? '-'],
            ['Checksum database', $result['manifest']['database_sha256'] ?? '-'],
        ]);

        return self::SUCCESS;
    }
}
