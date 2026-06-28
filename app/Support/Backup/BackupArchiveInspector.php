<?php

namespace App\Support\Backup;

use Illuminate\Validation\ValidationException;
use JsonException;
use ZipArchive;

class BackupArchiveInspector
{
    /** @return array{checksum_sha256: string, size_bytes: int, manifest: array<string, mixed>} */
    public function inspect(string $path, string $passphrase): array
    {
        if (! is_file($path) || ! is_readable($path)) {
            $this->fail('Arsip backup tidak dapat dibaca.');
        }

        $size = filesize($path);
        $maximum = (int) config('backup.maximum_archive_bytes', 262_144_000);

        if ($size === false || $size < 1 || $size > $maximum) {
            $this->fail('Ukuran arsip backup tidak diizinkan.');
        }

        $handle = fopen($path, 'rb');
        $magic = is_resource($handle) ? fread($handle, 4) : false;

        if (is_resource($handle)) {
            fclose($handle);
        }

        if (! is_string($magic) || ! str_starts_with($magic, 'PK')) {
            $this->fail('File bukan arsip ZIP yang valid.');
        }

        $zip = new ZipArchive;

        if ($zip->open($path) !== true) {
            $this->fail('Struktur ZIP tidak dapat dibuka.');
        }

        try {
            $entries = [];

            for ($index = 0; $index < $zip->numFiles; $index++) {
                $name = $zip->getNameIndex($index);

                if (! is_string($name) || $this->unsafeEntry($name)) {
                    $this->fail('Arsip berisi path yang tidak aman.');
                }

                $entries[] = $name;
            }

            sort($entries);

            if ($entries !== ['database.sql', 'manifest.json']) {
                $this->fail('Isi arsip tidak sesuai format backup aplikasi.');
            }

            foreach ($entries as $entry) {
                $stat = $zip->statName($entry);

                if (! is_array($stat) || ($stat['encryption_method'] ?? null) !== ZipArchive::EM_AES_256) {
                    $this->fail('Arsip wajib memakai enkripsi ZIP AES-256.');
                }

                $entryLimit = $entry === 'manifest.json'
                    ? 1_048_576
                    : (int) config('backup.maximum_uncompressed_bytes', 1_073_741_824);

                if (! isset($stat['size']) || (int) $stat['size'] < 1 || (int) $stat['size'] > $entryLimit) {
                    $this->fail('Ukuran isi arsip tidak diizinkan.');
                }
            }

            // Both entries must be unreadable before a password is supplied.
            if (@$zip->getFromName('manifest.json') !== false || @$zip->getFromName('database.sql') !== false) {
                $this->fail('Arsip backup tidak terenkripsi dengan benar.');
            }

            if (! $zip->setPassword($passphrase)) {
                $this->fail('Passphrase arsip tidak dapat digunakan.');
            }

            $manifestJson = $zip->getFromName('manifest.json');

            if (! is_string($manifestJson)) {
                $this->fail('Passphrase salah atau manifest tidak dapat dibaca.');
            }

            try {
                $manifest = json_decode($manifestJson, true, flags: JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                $this->fail('Manifest backup tidak valid.');
            }

            if (! is_array($manifest)
                || ($manifest['format_version'] ?? null) !== 1
                || ($manifest['application'] ?? null) !== 'cimuning-digital-hub'
                || ! preg_match('/^[a-f0-9]{64}$/', (string) ($manifest['database_sha256'] ?? ''))) {
                $this->fail('Manifest backup tidak dikenali.');
            }

            $stream = $zip->getStream('database.sql');

            if (! is_resource($stream)) {
                $this->fail('Passphrase salah atau database.sql tidak dapat dibaca.');
            }

            $hash = hash_init('sha256');

            try {
                while (! feof($stream)) {
                    $chunk = fread($stream, 1024 * 1024);

                    if ($chunk === false) {
                        $this->fail('Isi database.sql tidak dapat diverifikasi.');
                    }

                    hash_update($hash, $chunk);
                }
            } finally {
                fclose($stream);
            }

            if (! hash_equals((string) $manifest['database_sha256'], hash_final($hash))) {
                $this->fail('Checksum database.sql tidak sesuai manifest.');
            }

            $archiveChecksum = hash_file('sha256', $path);

            if (! is_string($archiveChecksum)) {
                $this->fail('Checksum arsip tidak dapat dibuat.');
            }

            return [
                'checksum_sha256' => $archiveChecksum,
                'size_bytes' => (int) $size,
                'manifest' => $manifest,
            ];
        } finally {
            $zip->close();
        }
    }

    private function unsafeEntry(string $name): bool
    {
        return str_contains($name, '..')
            || str_contains($name, '\\')
            || str_starts_with($name, '/')
            || preg_match('/^[A-Za-z]:/', $name) === 1;
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['archive' => $message]);
    }
}
