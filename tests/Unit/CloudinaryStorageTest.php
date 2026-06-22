<?php

namespace Tests\Unit;

use App\Support\CloudinaryStorage;
use App\Support\MediaUrl;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\ApiResponse;
use Cloudinary\Api\Exception\NotFound;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Psr\Http\Message\StreamInterface;
use Tests\TestCase;

class CloudinaryStorageTest extends TestCase
{
    private const PNG = '89504e470d0a1a0a0000000d4948445200000001000000010804000000b51c0c020000000b4944415478da63fcff1f0002eb01f569769cab0000000049454e44ae426082';

    public function test_it_uploads_a_readable_stream(): void
    {
        $binary = hex2bin(self::PNG);
        $uploadApi = Mockery::mock(UploadApi::class);
        $uploadApi->shouldReceive('upload')
            ->once()
            ->withArgs(fn (mixed $source, array $options): bool => is_resource($source)
                && stream_get_contents($source) === $binary
                && $options['public_id'] === 'cimuning/photo'
                && $options['resource_type'] === 'image'
                && $options['filename'] === 'photo.jpg')
            ->andReturn(new ApiResponse(['secure_url' => 'https://res.cloudinary.test/photo.jpg'], []));
        $client = Mockery::mock(Cloudinary::class);
        $client->shouldReceive('uploadApi')->once()->andReturn($uploadApi);
        $storage = new CloudinaryStorage($client, 'cimuning');
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $binary);
        rewind($stream);

        $this->assertTrue($storage->put('products/photo.jpg', $stream));

        fclose($stream);
    }

    public function test_it_wraps_string_contents_as_a_stream_without_base64(): void
    {
        $binary = hex2bin(self::PNG);
        $uploadApi = Mockery::mock(UploadApi::class);
        $uploadApi->shouldReceive('upload')
            ->once()
            ->withArgs(fn (mixed $source, array $options): bool => $source instanceof StreamInterface
                && (string) $source === $binary
                && ! str_contains((string) $source, 'data:image')
                && $options['public_id'] === 'cimuning/photo')
            ->andReturn(new ApiResponse(['secure_url' => 'https://res.cloudinary.test/photo.png'], []));
        $client = Mockery::mock(Cloudinary::class);
        $client->shouldReceive('uploadApi')->once()->andReturn($uploadApi);

        $this->assertTrue((new CloudinaryStorage($client, 'cimuning'))->put('products/photo.png', $binary));
    }

    public function test_it_returns_false_when_cloudinary_upload_fails(): void
    {
        $uploadApi = Mockery::mock(UploadApi::class);
        $uploadApi->shouldReceive('upload')->once()->andThrow(new \RuntimeException('Upload failed'));
        $client = Mockery::mock(Cloudinary::class);
        $client->shouldReceive('uploadApi')->once()->andReturn($uploadApi);

        $this->assertFalse((new CloudinaryStorage($client, 'cimuning'))->put('products/photo.png', hex2bin(self::PNG)));
    }

    public function test_it_rejects_unsafe_paths_mime_spoofing_oversized_and_unseekable_streams(): void
    {
        $client = Mockery::mock(Cloudinary::class);
        $client->shouldNotReceive('uploadApi');
        $storage = new CloudinaryStorage($client, 'cimuning');

        $this->assertFalse($storage->put('../photo.php', hex2bin(self::PNG)));
        $this->assertFalse($storage->put('products/photo.jpg', 'not-an-image'));
        $this->assertFalse($storage->put('products/photo.jpg', str_repeat('a', (2 * 1024 * 1024) + 1)));

        $stream = fopen('php://output', 'w');
        $this->assertFalse($storage->put('products/photo.jpg', $stream));
        fclose($stream);
    }

    public function test_it_checks_asset_existence_with_the_normalized_public_id(): void
    {
        $adminApi = Mockery::mock(AdminApi::class);
        $adminApi->shouldReceive('asset')
            ->twice()
            ->with('cimuning/photo', ['resource_type' => 'image'])
            ->andReturn(new ApiResponse(['public_id' => 'cimuning/photo'], []));
        $client = Mockery::mock(Cloudinary::class);
        $client->shouldReceive('adminApi')->twice()->andReturn($adminApi);
        $storage = new CloudinaryStorage($client, 'cimuning');

        $this->assertTrue($storage->exists('products/photo.jpg'));
        $this->assertFalse($storage->missing('products/photo.jpg'));
    }

    public function test_it_reports_a_missing_cloudinary_asset(): void
    {
        $adminApi = Mockery::mock(AdminApi::class);
        $adminApi->shouldReceive('asset')->once()->andThrow(new NotFound('Missing asset'));
        $client = Mockery::mock(Cloudinary::class);
        $client->shouldReceive('adminApi')->once()->andReturn($adminApi);

        $this->assertFalse((new CloudinaryStorage($client, 'cimuning'))->exists('products/missing.jpg'));
    }

    public function test_media_url_uses_the_active_disk_and_preserves_full_urls(): void
    {
        config(['filesystems.default' => 'local']);

        $this->assertSame(
            asset('storage/products/photo.jpg'),
            MediaUrl::get('products/photo.jpg'),
        );
        $this->assertSame(
            'https://res.cloudinary.com/demo/image/upload/photo.jpg',
            MediaUrl::get('https://res.cloudinary.com/demo/image/upload/photo.jpg'),
        );
    }

    public function test_media_url_builds_a_cloudinary_delivery_url_without_network_access(): void
    {
        config([
            'filesystems.default' => 'cloudinary',
            'cloudinary.cloud_name' => 'demo-cloud',
            'cloudinary.api_key' => '123456',
            'cloudinary.api_secret' => 'test-secret',
            'cloudinary.folder' => 'cimuning',
        ]);
        Storage::forgetDisk('cloudinary');

        $url = MediaUrl::get('products/photo.webp');

        $this->assertStringStartsWith('https://res.cloudinary.com/demo-cloud/image/upload/', $url);
        $this->assertStringContainsString('/f_auto/q_auto/', $url);
        $this->assertMatchesRegularExpression('#/s--[A-Za-z0-9_-]+--/#', $url);
        $this->assertStringContainsString('cimuning/photo', $url);
        $this->assertStringNotContainsString('/c_', $url);
        $this->assertStringNotContainsString('/w_', $url);
        $this->assertStringNotContainsString('/h_', $url);
    }

    public function test_livewire_temporary_uploads_default_to_local_disk(): void
    {
        $this->assertSame('local', config('livewire.temporary_file_upload.disk'));
        $this->assertSame('throttle:20,1', config('livewire.temporary_file_upload.middleware'));
        $this->assertSame(['png', 'jpg', 'jpeg', 'webp'], config('livewire.temporary_file_upload.preview_mimes'));
        $this->assertContains('max:2048', config('livewire.temporary_file_upload.rules'));
        $this->assertContains('mimes:jpg,jpeg,png,webp', config('livewire.temporary_file_upload.rules'));
        $this->assertContains('dimensions:max_width=5000,max_height=5000', config('livewire.temporary_file_upload.rules'));
    }
}
