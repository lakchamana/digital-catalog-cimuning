<?php

namespace Tests\Feature;

use App\Support\CloudinaryStorage;
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
        Storage::shouldReceive('disk')->once()->with('cloudinary')->andReturn($disk);

        $this->artisan('media:diagnose')
            ->expectsOutputToContain('Permanent disk: cloudinary')
            ->expectsOutputToContain('Temporary upload disk: local')
            ->expectsOutputToContain('CLOUDINARY_API_SECRET: configured')
            ->doesntExpectOutputToContain('configured-secret')
            ->expectsOutputToContain('Cloudinary authentication: OK')
            ->assertSuccessful();
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
