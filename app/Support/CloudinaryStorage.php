<?php

namespace App\Support;

use Cloudinary\Api\Exception\NotFound;
use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Format;
use Cloudinary\Transformation\Quality;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Stringable;
use Throwable;

class CloudinaryStorage implements Filesystem
{
    protected Cloudinary $client;

    protected string $folder;

    public function __construct(?Cloudinary $client = null, ?string $folder = null)
    {
        $this->folder = trim($folder ?? (string) config('cloudinary.folder', 'cimuning'), '/');

        if ($client) {
            $this->client = $client;

            return;
        }

        $credentials = [
            'cloud_name' => trim((string) config('cloudinary.cloud_name')),
            'api_key' => trim((string) config('cloudinary.api_key')),
            'api_secret' => trim((string) config('cloudinary.api_secret')),
        ];
        $missing = array_keys(array_filter($credentials, fn (string $value): bool => $value === ''));

        if ($missing !== []) {
            throw new InvalidArgumentException('Konfigurasi Cloudinary belum lengkap: '.implode(', ', $missing).'.');
        }

        $this->client = new Cloudinary([
            'cloud' => $credentials,
            'url' => ['secure' => true],
        ]);
    }

    public function put($path, $contents, $options = []): bool|string
    {
        try {
            $source = $this->validatedUploadSource((string) $path, $contents);
            $result = $this->client->uploadApi()->upload(
                $source,
                [
                    'public_id' => $this->publicId((string) $path),
                    'resource_type' => 'image',
                    'overwrite' => true,
                    'use_filename' => false,
                    'filename' => basename((string) $path),
                ],
            );

            return filled($result['secure_url'] ?? null);
        } catch (Throwable $exception) {
            Log::error('Cloudinary upload gagal.', [
                'exception' => $exception::class,
                'path' => basename((string) $path),
            ]);

            return false;
        }
    }

    public function putFile($path, $file = null, $options = []): bool|string
    {
        if (is_null($file) || is_array($file)) {
            [$path, $file, $options] = ['', $path, $file ?? []];
        }

        if (is_string($file)) {
            $contents = file_get_contents($file);

            return $contents === false ? false : $this->put($path, $contents, $options);
        }

        return $this->put($path, $file->get(), $options);
    }

    public function putFileAs($path, $file, $name = null, $options = []): bool|string
    {
        if (is_null($name) || is_array($name)) {
            [$path, $file, $name, $options] = ['', $path, $file, $name ?? []];
        }

        return $this->putFile(trim((string) $path, '/').'/'.$name, $file, $options);
    }

    public function delete($paths): bool
    {
        $success = true;

        foreach ((array) $paths as $path) {
            try {
                $this->client->uploadApi()->destroy($this->publicId((string) $path), [
                    'resource_type' => 'image',
                    'invalidate' => true,
                ]);
            } catch (Throwable $exception) {
                $success = false;
                Log::warning('Cloudinary delete gagal.', [
                    'exception' => $exception::class,
                    'path' => basename((string) $path),
                ]);
            }
        }

        return $success;
    }

    public function url($path): string
    {
        if (str_starts_with((string) $path, 'http://') || str_starts_with((string) $path, 'https://')) {
            return (string) $path;
        }

        $image = $this->client->image($this->publicId((string) $path));
        $image->format(Format::auto())->quality(Quality::auto());

        if (config('cloudinary.signed_urls', true)) {
            $image->signUrl();
        }

        return $image->toUrl();
    }

    public function exists($path): bool
    {
        try {
            $this->client->adminApi()->asset($this->publicId((string) $path), [
                'resource_type' => 'image',
            ]);

            return true;
        } catch (NotFound) {
            return false;
        }
    }

    public function missing($path): bool
    {
        return ! $this->exists($path);
    }

    public function ping(): bool
    {
        $response = $this->client->adminApi()->ping();

        return ($response['status'] ?? null) === 'ok';
    }

    protected function publicId(string $path): string
    {
        $name = pathinfo(parse_url($path, PHP_URL_PATH) ?: $path, PATHINFO_FILENAME);

        return $this->folder.'/'.$name;
    }

    protected function validatedUploadSource(string $path, mixed $contents): mixed
    {
        $this->validateUploadPath($path);

        if (is_resource($contents)) {
            return $this->validateResource($contents);
        }

        if ($contents instanceof Stringable) {
            $contents = (string) $contents;
        }

        if (! is_string($contents)) {
            throw new InvalidArgumentException('Konten upload harus berupa string atau stream yang dapat dibaca.');
        }

        return $this->validateStream(Utils::streamFor($contents));
    }

    protected function validateUploadPath(string $path): void
    {
        $path = ltrim(str_replace('\\', '/', $path), '/');
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $allowedExtensions = config('cloudinary.allowed_extensions', ['jpg', 'jpeg', 'png', 'webp']);

        if (
            str_contains($path, '..')
            || ! preg_match('/^[A-Za-z0-9_\/-]+\.[A-Za-z0-9]+$/', $path)
            || ! in_array($extension, $allowedExtensions, true)
        ) {
            throw new InvalidArgumentException('Path atau ekstensi upload tidak diizinkan.');
        }
    }

    protected function validateResource($resource)
    {
        $metadata = stream_get_meta_data($resource);

        if (! ($metadata['seekable'] ?? false) || rewind($resource) === false) {
            throw new InvalidArgumentException('Stream upload harus dapat dibaca ulang.');
        }

        $statistics = fstat($resource);
        $size = is_array($statistics) ? ($statistics['size'] ?? null) : null;
        $header = fread($resource, 65536);
        rewind($resource);

        $this->validateFileContent($size, $header === false ? '' : $header);

        return $resource;
    }

    protected function validateStream(StreamInterface $stream): StreamInterface
    {
        if (! $stream->isReadable() || ! $stream->isSeekable()) {
            throw new InvalidArgumentException('Stream upload harus dapat dibaca ulang.');
        }

        $stream->rewind();
        $header = $stream->read(65536);
        $stream->rewind();
        $this->validateFileContent($stream->getSize(), $header);

        return $stream;
    }

    protected function validateFileContent(?int $size, string $header): void
    {
        $maxBytes = (int) config('cloudinary.max_upload_bytes', 2 * 1024 * 1024);

        if ($size === null || $size <= 0 || $size > $maxBytes) {
            throw new InvalidArgumentException('Ukuran file upload tidak diizinkan.');
        }

        $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->buffer($header);
        $allowedMimeTypes = config('cloudinary.allowed_mime_types', ['image/jpeg', 'image/png', 'image/webp']);

        if (! is_string($mimeType) || ! in_array($mimeType, $allowedMimeTypes, true)) {
            throw new InvalidArgumentException('Isi file bukan gambar yang diizinkan.');
        }
    }

    public function get($path): ?string
    {
        return null;
    }

    public function readStream($path)
    {
        return null;
    }

    public function writeStream($path, $resource, array $options = []): bool
    {
        return (bool) $this->put($path, $resource, $options);
    }

    public function getVisibility($path): string
    {
        return 'public';
    }

    public function setVisibility($path, $visibility): bool
    {
        return true;
    }

    public function prepend($path, $data): bool
    {
        return false;
    }

    public function append($path, $data): bool
    {
        return false;
    }

    public function deleteDirectory($directory): bool
    {
        return true;
    }

    public function makeDirectory($path): bool
    {
        return true;
    }

    public function allFiles($directory = null): array
    {
        return [];
    }

    public function files($directory = null, $recursive = false): array
    {
        return [];
    }

    public function allDirectories($directory = null): array
    {
        return [];
    }

    public function directories($directory = null, $recursive = false): array
    {
        return [];
    }

    public function copy($from, $to): bool
    {
        return false;
    }

    public function move($from, $to): bool
    {
        return false;
    }

    public function size($path): int
    {
        return 0;
    }

    public function lastModified($path): int
    {
        return 0;
    }

    public function path($path): string
    {
        return $path;
    }

    public function temporaryUrl($path, $expiration, array $options = []): string
    {
        return $this->url($path);
    }

    public function checksum($path, $options = []): string
    {
        return '';
    }

    public function providesTemporaryUrls(): bool
    {
        return false;
    }
}
