<?php

namespace App\Support;

class UploadDisk
{
    public static function name(): string
    {
        $disk = config('filesystems.default', 'public');

        return $disk === 'local' ? 'public' : $disk;
    }
}
