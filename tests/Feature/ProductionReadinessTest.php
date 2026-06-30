<?php

namespace Tests\Feature;

use App\Http\Middleware\ProductionSecurityHeaders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Middleware\TrustHosts;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        TrustHosts::flushState();
        TrustProxies::flushState();
        Request::setTrustedHosts([]);
        Request::setTrustedProxies([], Request::HEADER_X_FORWARDED_FOR);
    }

    protected function tearDown(): void
    {
        TrustHosts::flushState();
        TrustProxies::flushState();
        Request::setTrustedHosts([]);
        Request::setTrustedProxies([], Request::HEADER_X_FORWARDED_FOR);

        parent::tearDown();
    }

    public function test_production_responses_have_safe_headers_without_csp(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');
        config()->set('production.hsts.enabled', true);
        config()->set('production.hsts.max_age', 31_536_000);

        $request = Request::create('https://localhost/');
        $response = app(ProductionSecurityHeaders::class)->handle(
            $request,
            fn (): Response => new Response('OK'),
        );

        $this->assertSame('max-age=31536000', $response->headers->get('Strict-Transport-Security'));
        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertSame('DENY', $response->headers->get('X-Frame-Options'));
        $this->assertSame('strict-origin-when-cross-origin', $response->headers->get('Referrer-Policy'));
        $this->assertSame(
            'geolocation=(self), camera=(), microphone=(), payment=(), usb=()',
            $response->headers->get('Permissions-Policy'),
        );

        $this->assertFalse($response->headers->has('Content-Security-Policy'));
        $this->assertFalse($response->headers->has('Content-Security-Policy-Report-Only'));
    }

    public function test_security_headers_are_not_forced_outside_production(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeaderMissing('Strict-Transport-Security')
            ->assertHeaderMissing('Permissions-Policy');
    }

    public function test_production_trusted_hosts_accept_configured_domain_and_reject_foreign_host(): void
    {
        $patterns = ['^cimuning\.test$', '^www\.cimuning\.test$'];
        TrustHosts::at($patterns, subdomains: false);

        $middleware = new TrustHosts($this->app);
        Request::setTrustedHosts($middleware->hosts());

        $this->assertSame('cimuning.test', Request::create('https://cimuning.test')->getHost());
        $this->assertSame('www.cimuning.test', Request::create('https://www.cimuning.test')->getHost());

        $this->expectException(SuspiciousOperationException::class);
        Request::create('https://attacker.test')->getHost();
    }

    public function test_forwarded_https_is_only_honored_from_a_trusted_proxy(): void
    {
        TrustProxies::at(['10.0.0.1']);
        $middleware = new TrustProxies;

        $untrusted = Request::create('http://localhost/', server: [
            'REMOTE_ADDR' => '203.0.113.10',
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ]);
        $middleware->handle($untrusted, fn (Request $request): Response => new Response(
            $request->isSecure() ? 'secure' : 'insecure',
        ));
        $this->assertFalse($untrusted->isSecure());

        $trusted = Request::create('http://localhost/', server: [
            'REMOTE_ADDR' => '10.0.0.1',
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ]);
        $middleware->handle($trusted, fn (Request $request): Response => new Response(
            $request->isSecure() ? 'secure' : 'insecure',
        ));
        $this->assertTrue($trusted->isSecure());
    }

    public function test_health_route_checks_database_and_cache(): void
    {
        $this->get('/up')->assertOk();
    }

    public function test_health_route_fails_without_leaking_exception_details(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');
        config()->set('app.debug', false);
        config()->set('production.trusted_hosts', ['^localhost$']);

        DB::shouldReceive('select')
            ->once()
            ->andThrow(new RuntimeException('database-password-must-not-leak'));

        $this->withHeader('Host', 'localhost')
            ->get('/up')
            ->assertStatus(500)
            ->assertDontSee('database-password-must-not-leak');
    }

    public function test_scheduler_writes_a_heartbeat(): void
    {
        Cache::forget(config('production.scheduler_heartbeat_key'));

        $this->artisan('schedule:run')->assertSuccessful();

        $this->assertNotNull(Cache::get(config('production.scheduler_heartbeat_key')));
    }

    public function test_production_check_fails_safely_without_printing_secrets(): void
    {
        config()->set('app.key', 'base64:'.base64_encode(str_repeat('k', 32)));
        config()->set('database.connections.mysql.password', 'super-secret-database-password');
        config()->set('cloudinary.api_secret', 'super-secret-cloudinary-key');

        $this->artisan('app:production-check')
            ->assertFailed()
            ->expectsOutputToContain('Environment production')
            ->expectsOutputToContain('Debug mode nonaktif')
            ->doesntExpectOutputToContain('super-secret-database-password')
            ->doesntExpectOutputToContain('super-secret-cloudinary-key');
    }

    public function test_deployment_files_use_public_webroot_and_gate_seeders(): void
    {
        $caddy = file_get_contents(base_path('Caddyfile'));
        $entrypoint = file_get_contents(base_path('docker-entrypoint.sh'));
        $dockerfile = file_get_contents(base_path('Dockerfile'));

        $this->assertStringContainsString('root * /app/public', $caddy);
        $this->assertStringContainsString('frankenphp run', $entrypoint);
        $this->assertStringContainsString('RUN_DATABASE_SEEDERS', $entrypoint);
        $this->assertStringContainsString('Seeders skipped.', $entrypoint);
        $this->assertStringNotContainsString('php -S', $entrypoint);
        $this->assertStringContainsString('HEALTHCHECK', $dockerfile);
        $this->assertStringContainsString('AS php-dependencies', $dockerfile);
        $this->assertStringContainsString('pdo_sqlite', $dockerfile);
        $this->assertStringContainsString('Pagination/resources/views', $dockerfile);
        $this->assertStringNotContainsString('storage/framework/{', $dockerfile);
        $this->assertStringContainsString('encode zstd gzip', $caddy);
        $this->assertFileDoesNotExist(base_path('server.php'));
    }
}
