<?php

namespace Tests\Feature;

use App\Support\CloudinaryStorage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class MediaDiagnosticsTest extends TestCase
{
    public function test_diagnostic_reports_configuration_and_authentication_without_secrets(): void
    {
        config([
            'filesystems.default' => 'cloudinary',
            'livewire.temporary_file_upload.disk' => 'local',
            'cloudinary.cloud_name' => 'configured-cloud',
            'cloudinary.api_key' => 'configured-key',
            'cloudinary.api_secret' => 'configured-secret',
        ]);
        $disk = Mockery::mock(CloudinaryStorage::class);
        $disk->shouldReceive('ping')->once()->andReturnTrue();
        $disk->shouldNotReceive('put', 'delete');
        Storage::shouldReceive('disk')->once()->with('cloudinary')->andReturn($disk);

        $this->artisan('media:diagnose')
            ->expectsOutputToContain('Permanent disk: cloudinary')
            ->expectsOutputToContain('Temporary upload disk: local')
            ->expectsOutputToContain('Transfer mode: multipart-stream')
            ->expectsOutputToContain('Delivery transformation: f_auto/q_auto (no resize or crop)')
            ->expectsOutputToContain('Signed delivery URLs: enabled')
            ->expectsOutputToContain('CLOUDINARY_API_SECRET: configured')
            ->doesntExpectOutputToContain('configured-secret')
            ->expectsOutputToContain('Cloudinary authentication: OK')
            ->assertSuccessful();
    }

    public function test_upload_diagnostic_verifies_delivery_and_cleans_up(): void
    {
        config([
            'filesystems.default' => 'cloudinary',
            'cloudinary.cloud_name' => 'configured-cloud',
            'cloudinary.api_key' => 'configured-key',
            'cloudinary.api_secret' => 'configured-secret',
            'cloudinary.signed_urls' => true,
        ]);
        $url = 'https://res.cloudinary.com/demo/image/upload/s--abc123--/f_auto/q_auto/v1/cimuning/diagnostic';
        $disk = Mockery::mock(CloudinaryStorage::class);
        $disk->shouldReceive('ping')->once()->andReturnTrue();
        $disk->shouldReceive('put')->once()
            ->withArgs(fn (string $path, mixed $stream): bool => str_starts_with($path, 'diagnostics/media-diagnose-') && is_resource($stream))
            ->andReturnTrue();
        $disk->shouldReceive('url')->once()->andReturn($url);
        $disk->shouldReceive('delete')->once()->andReturnTrue();
        Storage::shouldReceive('disk')->once()->with('cloudinary')->andReturn($disk);
        Http::fake([$url => Http::response('', 200)]);

        $this->artisan('media:diagnose', ['--upload' => true])
            ->expectsOutputToContain('Diagnostic upload: OK')
            ->expectsOutputToContain('Signed delivery URL validation: OK')
            ->expectsOutputToContain('Diagnostic delivery: OK')
            ->expectsOutputToContain('Diagnostic cleanup: OK')
            ->assertSuccessful();
    }

    public function test_upload_diagnostic_cleans_up_and_fails_when_delivery_is_invalid(): void
    {
        config([
            'cloudinary.cloud_name' => 'configured-cloud',
            'cloudinary.api_key' => 'configured-key',
            'cloudinary.api_secret' => 'configured-secret',
            'cloudinary.signed_urls' => true,
        ]);
        $disk = Mockery::mock(CloudinaryStorage::class);
        $disk->shouldReceive('ping')->once()->andReturnTrue();
        $disk->shouldReceive('put')->once()->andReturnTrue();
        $disk->shouldReceive('url')->once()->andReturn('https://res.cloudinary.com/demo/image/upload/f_auto/q_auto/unsigned');
        $disk->shouldReceive('delete')->once()->andReturnTrue();
        Storage::shouldReceive('disk')->once()->with('cloudinary')->andReturn($disk);
        Http::preventStrayRequests();

        $this->artisan('media:diagnose', ['--upload' => true])
            ->expectsOutputToContain('Signed delivery URL validation: FAILED')
            ->expectsOutputToContain('Diagnostic cleanup: OK')
            ->assertFailed();
    }

    public function test_diagnostic_fails_safely_when_credentials_are_missing(): void
    {
        config([
            'cloudinary.cloud_name' => '',
            'cloudinary.api_key' => '',
            'cloudinary.api_secret' => '',
        ]);

        $this->artisan('media:diagnose')
            ->expectsOutputToContain('CLOUDINARY_API_SECRET: missing')
            ->expectsOutputToContain('Diagnosis dihentikan')
            ->assertFailed();
    }
}
