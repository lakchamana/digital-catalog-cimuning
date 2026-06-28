<?php

namespace App\Support\Backup;

use App\Models\BackupRun;

readonly class BackupArtifact
{
    public function __construct(
        public BackupRun $run,
        public string $path,
        public string $downloadName,
    ) {}
}
