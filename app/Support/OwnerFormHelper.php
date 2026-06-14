<?php

namespace App\Support;

use Illuminate\Support\Str;

class OwnerFormHelper
{
    public static function normalizeInstagram(?string $value): ?string
    {
        return self::normalizeSocialUrl($value, 'https://instagram.com/');
    }

    public static function normalizeTiktok(?string $value): ?string
    {
        return self::normalizeSocialUrl($value, 'https://www.tiktok.com/@');
    }

    /**
     * @return array{latitude: string, longitude: string}|null
     */
    public static function coordinatesFromMapsText(?string $value): ?array
    {
        if (blank($value)) {
            return null;
        }

        $text = trim((string) $value);

        $patterns = [
            '/@(-?\d{1,2}\.\d+),\s*(-?\d{1,3}\.\d+)/',
            '/[?&]q=(-?\d{1,2}\.\d+),\s*(-?\d{1,3}\.\d+)/',
            '/[?&]query=(-?\d{1,2}\.\d+),\s*(-?\d{1,3}\.\d+)/',
            '/(-?\d{1,2}\.\d+)\s*,\s*(-?\d{1,3}\.\d+)/',
        ];

        foreach ($patterns as $pattern) {
            if (! preg_match($pattern, $text, $matches)) {
                continue;
            }

            $latitude = (float) $matches[1];
            $longitude = (float) $matches[2];

            if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
                continue;
            }

            return [
                'latitude' => (string) $latitude,
                'longitude' => (string) $longitude,
            ];
        }

        return null;
    }

    private static function normalizeSocialUrl(?string $value, string $baseUrl): ?string
    {
        if (blank($value)) {
            return null;
        }

        $value = trim((string) $value);

        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        $username = ltrim($value, '@/ ');

        return $username === '' ? null : $baseUrl.$username;
    }
}
