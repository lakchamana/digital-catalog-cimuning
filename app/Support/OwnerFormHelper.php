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

        $text = urldecode(trim((string) $value));

        $patterns = [
            ['regex' => '/@(-?\d{1,2}\.\d+),\s*(-?\d{1,3}\.\d+)/', 'latitude' => 1, 'longitude' => 2],
            ['regex' => '/[?&](?:q|query|ll|center)=(-?\d{1,2}\.\d+),\s*(-?\d{1,3}\.\d+)/', 'latitude' => 1, 'longitude' => 2],
            ['regex' => '/!3d(-?\d{1,2}\.\d+)!4d(-?\d{1,3}\.\d+)/', 'latitude' => 1, 'longitude' => 2],
            ['regex' => '/!4d(-?\d{1,3}\.\d+)!3d(-?\d{1,2}\.\d+)/', 'latitude' => 2, 'longitude' => 1],
            ['regex' => '/(-?\d{1,2}\.\d+)\s*,\s*(-?\d{1,3}\.\d+)/', 'latitude' => 1, 'longitude' => 2],
        ];

        foreach ($patterns as $pattern) {
            if (! preg_match($pattern['regex'], $text, $matches)) {
                continue;
            }

            $latitude = (float) $matches[$pattern['latitude']];
            $longitude = (float) $matches[$pattern['longitude']];

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

    public static function mapsValidationMessage(?string $value): ?string
    {
        if (blank($value) || self::coordinatesFromMapsText($value)) {
            return null;
        }

        $text = strtolower((string) $value);

        if (str_contains($text, 'maps.app.goo.gl') || str_contains($text, 'goo.gl/maps')) {
            return 'Link pendek Google Maps belum berisi titik koordinat. Buka link sampai Google Maps terbuka, lalu salin URL lengkap dari address bar atau tempel koordinat lokasi usaha.';
        }

        return 'Link Google Maps belum dapat dibaca. Tempel URL Maps yang berisi koordinat, atau gunakan tombol lokasi saat Anda berada di tempat usaha.';
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
