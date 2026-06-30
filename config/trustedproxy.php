<?php

$configured = env(
    'TRUSTED_PROXIES',
    filled(env('RAILWAY_ENVIRONMENT')) ? '*' : null,
);

return [
    'proxies' => is_string($configured) && str_contains($configured, ',')
        ? array_values(array_filter(array_map('trim', explode(',', $configured))))
        : $configured,
];
