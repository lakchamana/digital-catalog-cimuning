<?php

namespace App\Support;

use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class AdminActivityLogger
{
    public static function record(
        string $event,
        ?User $actor = null,
        ?Model $subject = null,
        ?string $subjectLabel = null,
        ?string $reason = null,
        array $before = [],
        array $after = [],
        array $metadata = [],
        ?Request $request = null,
    ): void {
        try {
            if (! Schema::hasTable('admin_activity_logs')) {
                return;
            }

            $request ??= request();
            $context = self::requestContext($request);

            AdminActivityLog::query()->create([
                'actor_id' => $actor?->getKey(),
                'event' => Str::limit($event, 80, ''),
                'subject_type' => $subject?->getMorphClass(),
                'subject_id' => $subject?->getKey(),
                'subject_label' => Str::limit($subjectLabel ?? self::subjectLabel($subject), 255, ''),
                'reason' => filled($reason) ? Str::limit($reason, 2000, '') : null,
                'before' => self::sanitize($before) ?: null,
                'after' => self::sanitize($after) ?: null,
                'metadata' => self::sanitize([...$context['metadata'], ...$metadata]) ?: null,
                'request_id' => $context['request_id'],
                'created_at' => now(),
            ]);
        } catch (Throwable $exception) {
            Log::warning('Pencatatan audit admin gagal.', [
                'event' => Str::limit($event, 80, ''),
                'exception' => $exception::class,
            ]);
        }
    }

    public static function authentication(string $event, ?User $user, string $guard, array $metadata = []): void
    {
        self::record(
            event: $event,
            actor: in_array($event, ['admin_login', 'admin_logout'], true) ? $user : null,
            subject: $user,
            subjectLabel: $user?->name,
            metadata: ['guard' => $guard, ...$metadata],
        );
    }

    public static function failedIdentityHash(mixed $email): ?string
    {
        $email = Str::lower(trim((string) $email));

        if ($email === '') {
            return null;
        }

        return hash_hmac('sha256', $email, (string) config('app.key'));
    }

    private static function requestContext(Request $request): array
    {
        $requestId = $request->attributes->get('admin_audit_request_id');

        if (! is_string($requestId) || $requestId === '') {
            $requestId = (string) Str::uuid();
            $request->attributes->set('admin_audit_request_id', $requestId);
        }

        return [
            'request_id' => $requestId,
            'metadata' => [
                'route' => $request->route()?->getName(),
                'method' => $request->method(),
                'path' => $request->path(),
            ],
        ];
    }

    private static function subjectLabel(?Model $subject): ?string
    {
        if (! $subject) {
            return null;
        }

        foreach (['name', 'title', 'email'] as $attribute) {
            if (filled($subject->getAttribute($attribute))) {
                return (string) $subject->getAttribute($attribute);
            }
        }

        return class_basename($subject).' #'.$subject->getKey();
    }

    private static function sanitize(array $data): array
    {
        return collect($data)
            ->reject(fn (mixed $value, string|int $key): bool => self::isSensitiveKey((string) $key))
            ->map(function (mixed $value): mixed {
                if (is_array($value)) {
                    return self::sanitize($value);
                }

                if (is_string($value)) {
                    return Str::limit($value, 2000, '');
                }

                return is_scalar($value) || is_null($value) ? $value : (string) $value;
            })
            ->all();
    }

    private static function isSensitiveKey(string $key): bool
    {
        return Str::contains(Str::lower($key), [
            'password', 'token', 'secret', 'credential', 'remember', 'binary',
            'contents', 'api_key', 'authorization', 'cookie',
        ]);
    }
}
