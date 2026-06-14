<?php

namespace App\Support;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;

/**
 * CloudinaryStorage — adapter ringan untuk upload/delete file ke Cloudinary.
 * Dipakai sebagai pengganti disk "public" saat FILESYSTEM_DISK=cloudinary di production.
 *
 * Hanya mengimplementasikan method yang dipakai oleh Filament file upload dan
 * helper Storage::url(). Method lain dikembalikan nilai default aman.
 */
class CloudinaryStorage implements Filesystem
{
    protected Cloudinary $client;
    protected string $folder;

    public function __construct()
    {
        Configuration::instance([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key'    => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ],
            'url' => ['secure' => true],
        ]);

        $this->client = new Cloudinary();
        $this->folder = config('cloudinary.folder', 'cimuning');
    }

    /** Upload file ke Cloudinary. $path dipakai sebagai public_id. */
    public function put($path, $contents, $options = []): bool|string
    {
        try {
            $publicId = $this->folder . '/' . pathinfo($path, PATHINFO_FILENAME);

            $result = $this->client->uploadApi()->upload(
                'data:application/octet-stream;base64,' . base64_encode($contents),
                [
                    'public_id'       => $publicId,
                    'resource_type'   => 'auto',
                    'overwrite'       => true,
                    'use_filename'    => false,
                ]
            );

            return $result['secure_url'] ?? false;
        } catch (\Throwable $e) {
            Log::error('Cloudinary upload error: ' . $e->getMessage());

            return false;
        }
    }

    /** Upload dari path file lokal. */
    public function putFile($path, $file, $options = []): bool|string
    {
        if (is_string($file)) {
            return $this->put($path, file_get_contents($file), $options);
        }

        return $this->put($path, $file->get(), $options);
    }

    /** Upload dengan nama file tetap (Filament memakai ini). */
    public function putFileAs($path, $file, $name, $options = []): bool|string
    {
        $fullPath = rtrim($path, '/') . '/' . $name;

        return $this->putFile($fullPath, $file, $options);
    }

    /** Hapus file dari Cloudinary berdasarkan public_id. */
    public function delete($paths): bool
    {
        $paths = is_array($paths) ? $paths : [$paths];

        foreach ($paths as $path) {
            try {
                $publicId = $this->folder . '/' . pathinfo($path, PATHINFO_FILENAME);
                $this->client->uploadApi()->destroy($publicId);
            } catch (\Throwable $e) {
                Log::warning('Cloudinary delete error: ' . $e->getMessage());
            }
        }

        return true;
    }

    /** Kembalikan URL publik dari Cloudinary berdasarkan path. */
    public function url($path): string
    {
        // Jika sudah berupa URL lengkap (disimpan dari hasil upload), kembalikan langsung
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        $publicId = $this->folder . '/' . pathinfo($path, PATHINFO_FILENAME);

        return $this->client->image($publicId)->toUrl();
    }

    public function exists($path): bool { return false; }
    public function get($path): ?string { return null; }
    public function readStream($path) { return null; }
    public function writeStream($path, $resource, array $options = []): bool { return false; }
    public function getVisibility($path): string { return 'public'; }
    public function setVisibility($path, $visibility): bool { return true; }
    public function prepend($path, $data): bool { return false; }
    public function append($path, $data): bool { return false; }
    public function deleteDirectory($directory): bool { return true; }
    public function makeDirectory($path): bool { return true; }
    public function allFiles($directory = null): array { return []; }
    public function files($directory = null, $recursive = false): array { return []; }
    public function allDirectories($directory = null): array { return []; }
    public function directories($directory = null, $recursive = false): array { return []; }
    public function copy($from, $to): bool { return false; }
    public function move($from, $to): bool { return false; }
    public function size($path): int { return 0; }
    public function lastModified($path): int { return 0; }
    public function path($path): string { return $path; }
    public function missing($path): bool { return true; }
    public function temporaryUrl($path, $expiration, array $options = []): string { return $this->url($path); }
    public function checksum($path, $options = []): string { return ''; }
    public function providesTemporaryUrls(): bool { return false; }
}
