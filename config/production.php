<?php

$appUrlHost = parse_url((string) env('APP_URL', ''), PHP_URL_HOST);
$configuredHosts = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) env('TRUSTED_HOSTS', '')),
)));

if ($configuredHosts === [] && is_string($appUrlHost) && $appUrlHost !== '') {
    $configuredHosts = [$appUrlHost];

    if (! str_starts_with($appUrlHost, 'www.')) {
        $configuredHosts[] = 'www.'.$appUrlHost;
    }
}

return [
    'force_https' => env('FORCE_HTTPS', env('APP_ENV') === 'production'),
    'trusted_hosts' => array_map(
        fn (string $host): string => '^'.preg_quote($host, '/').'$',
        $configuredHosts,
    ),
    'hsts' => [
        'enabled' => env('SECURITY_HSTS_ENABLED', true),
        'max_age' => (int) env('SECURITY_HSTS_MAX_AGE', 31_536_000),
    ],
    'run_database_seeders' => env('RUN_DATABASE_SEEDERS', false),
    'scheduler_heartbeat_key' => 'cimuning:scheduler:last-run',
    'scheduler_max_age_minutes' => (int) env('SCHEDULER_MAX_AGE_MINUTES', 5),
];
