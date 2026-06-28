<?php

namespace App\Support\Backup;

interface DatabaseDumper
{
    public function dump(string $outputPath): void;
}
