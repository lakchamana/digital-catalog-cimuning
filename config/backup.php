<?php

return [
    'enabled' => env('BACKUP_ENABLED', true),
    'mysqldump_path' => env('BACKUP_MYSQLDUMP_PATH'),
    'timeout_seconds' => (int) env('BACKUP_TIMEOUT_SECONDS', 120),
    'maximum_archive_bytes' => (int) env('BACKUP_MAX_ARCHIVE_BYTES', 262_144_000),
    'maximum_uncompressed_bytes' => (int) env('BACKUP_MAX_UNCOMPRESSED_BYTES', 1_073_741_824),
    'archive_ttl_minutes' => (int) env('BACKUP_ARCHIVE_TTL_MINUTES', 60),
    'cooldown_minutes' => (int) env('BACKUP_COOLDOWN_MINUTES', 15),
    'warning_hours' => 48,
    'critical_hours' => 72,
    'excluded_tables' => [
        'sessions',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
        'password_reset_tokens',
    ],
];
