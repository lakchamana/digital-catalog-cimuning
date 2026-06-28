<?php

namespace App\Console\Commands;

use App\Support\Backup\DatabaseBackupService;
use Illuminate\Console\Command;

class CleanupBackups extends Command
{
    protected $signature = 'backup:cleanup';

    protected $description = 'Remove expired temporary backup archives and plaintext remnants';

    public function handle(DatabaseBackupService $service): int
    {
        $removed = $service->cleanupExpired();
        $this->info("Cleanup selesai. {$removed} arsip sementara dihapus.");

        return self::SUCCESS;
    }
}
