<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class MediaUploadSecurityTest extends TestCase
{
    private const PNG = '89504e470d0a1a0a0000000d4948445200000001000000010804000000b51c0c020000000b4944415478da63fcff1f0002eb01f569769cab0000000049454e44ae426082';

    private array $temporaryFiles = [];

    public function test_temporary_upload_rules_accept_safe_images_and_reject_unsafe_files(): void
    {
        $rules = config('livewire.temporary_file_upload.rules');
        $valid = $this->uploadedFile(hex2bin(self::PNG), 'valid.png', 'image/png');
        $wideBinary = substr_replace(hex2bin(self::PNG), pack('N', 5001), 16, 4);
        $wide = $this->uploadedFile($wideBinary, 'wide.png', 'image/png');
        $oversized = $this->uploadedFile(hex2bin(self::PNG).str_repeat("\0", (2 * 1024 * 1024) + 1), 'large.png', 'image/png');
        $spoofed = $this->uploadedFile('<?php echo "not an image"; ?>', 'photo.jpg', 'image/jpeg');

        $this->assertTrue(Validator::make(['upload' => $valid], ['upload' => $rules])->passes());
        $this->assertTrue(Validator::make(['upload' => $wide], ['upload' => $rules])->fails());
        $this->assertTrue(Validator::make(['upload' => $oversized], ['upload' => $rules])->fails());
        $this->assertTrue(Validator::make(['upload' => $spoofed], ['upload' => $rules])->fails());
    }

    public function test_livewire_upload_route_uses_the_hardened_rate_limit(): void
    {
        $route = Route::getRoutes()->getByName('livewire.upload-file');

        $this->assertNotNull($route);
        $this->assertContains('throttle:20,1', $route->gatherMiddleware());
    }

    private function uploadedFile(string $contents, string $name, string $mimeType): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'media-security-');
        file_put_contents($path, $contents);
        $this->temporaryFiles[] = $path;

        return new UploadedFile($path, $name, $mimeType, null, true);
    }

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }

        parent::tearDown();
    }
}
