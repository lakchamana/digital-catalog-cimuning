<?php

use App\Models\User;
use App\Support\AdminActivityLogger;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust Railway reverse proxy agar Laravel baca X-Forwarded-Proto: https
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontFlash(['current_password', 'passphrase', 'passphrase_confirmation']);

        $exceptions->render(function (Throwable $exception, Request $request) {
            $status = match (true) {
                $exception instanceof AuthorizationException => 403,
                $exception instanceof HttpExceptionInterface => $exception->getStatusCode(),
                default => null,
            };
            $actor = auth()->user();

            if ($status === 403 && $actor instanceof User && $request->is('admin', 'admin/*')) {
                AdminActivityLogger::record(
                    event: 'admin_access_denied',
                    actor: $actor,
                    reason: 'Akses ditolak oleh policy atau middleware.',
                    metadata: ['exception' => $exception::class],
                    request: $request,
                );
            }

            return null;
        });

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
