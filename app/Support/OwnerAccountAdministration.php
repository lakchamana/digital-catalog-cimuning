<?php

namespace App\Support;

use App\Models\ModerationAction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RuntimeException;
use Throwable;

class OwnerAccountAdministration
{
    public static function correctIdentity(User $owner, User $admin, string $name, string $email, string $reason): void
    {
        self::authorize($owner, $admin);
        self::validateReason($reason);

        $data = Validator::make(compact('name', 'email'), [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($owner->id)],
        ])->validate();

        DB::transaction(function () use ($owner, $admin, $data, $reason): void {
            $locked = User::query()->lockForUpdate()->findOrFail($owner->getKey());
            self::assertOwnerTarget($locked, $admin);
            self::assertManageable($locked);

            $changed = collect(['name', 'email'])
                ->filter(fn (string $field): bool => $locked->{$field} !== $data[$field])
                ->values()
                ->all();

            Validator::make(['changed' => $changed], ['changed' => ['required', 'array', 'min:1']], [
                'changed.required' => 'Tidak ada perubahan identitas untuk disimpan.',
                'changed.min' => 'Tidak ada perubahan identitas untuk disimpan.',
            ])->validate();

            $locked->update($data);
            self::record($locked, $admin, 'owner_identity_corrected', $reason, ['fields' => $changed]);
        });
    }

    public static function suspend(User $owner, User $admin, string $reason): void
    {
        self::authorize($owner, $admin);
        self::validateReason($reason);

        DB::transaction(function () use ($owner, $admin, $reason): void {
            $locked = User::query()->lockForUpdate()->findOrFail($owner->getKey());
            self::assertOwnerTarget($locked, $admin);
            self::assertState($locked->account_status === 'active', 'Akun ini tidak dalam keadaan aktif.');

            $locked->forceFill([
                'account_status' => 'suspended',
                'suspended_at' => now(),
                'suspension_reason' => $reason,
                'suspended_by' => $admin->id,
                'remember_token' => Str::random(60),
            ])->save();

            self::revokeAccess($locked);
            self::record($locked, $admin, 'owner_suspended', $reason);
        });
    }

    public static function reactivate(User $owner, User $admin, string $reason): void
    {
        self::authorize($owner, $admin);
        self::validateReason($reason);

        DB::transaction(function () use ($owner, $admin, $reason): void {
            $locked = User::query()->lockForUpdate()->findOrFail($owner->getKey());
            self::assertOwnerTarget($locked, $admin);
            self::assertState($locked->account_status === 'suspended', 'Hanya akun suspended yang dapat diaktifkan kembali.');

            $locked->forceFill([
                'account_status' => 'active',
                'suspended_at' => null,
                'suspension_reason' => null,
                'suspended_by' => null,
            ])->save();

            self::record($locked, $admin, 'owner_reactivated', $reason);
        });
    }

    public static function anonymize(User $owner, User $admin, string $confirmation, string $reason): void
    {
        self::authorize($owner, $admin);
        self::validateReason($reason);

        Validator::make(['confirmation' => $confirmation], [
            'confirmation' => ['required', 'in:ANONIMKAN'],
        ], [
            'confirmation.in' => 'Ketik ANONIMKAN untuk mengonfirmasi tindakan permanen ini.',
        ])->validate();

        $owner = DB::transaction(function () use ($owner, $admin, $reason): User {
            $locked = User::query()->with('umkm.products.images')->lockForUpdate()->findOrFail($owner->getKey());
            self::assertOwnerTarget($locked, $admin);
            self::assertState(
                in_array($locked->account_status, ['suspended', 'anonymization_pending'], true),
                'Suspend akun sebelum menjalankan anonimisasi.',
            );

            $locked->forceFill([
                'account_status' => 'anonymization_pending',
                'anonymization_requested_at' => $locked->anonymization_requested_at ?? now(),
                'remember_token' => Str::random(60),
            ])->save();

            self::revokeAccess($locked);
            self::record($locked, $admin, 'owner_anonymization_started', $reason);

            return $locked;
        });

        self::deleteOwnedMedia($owner);

        DB::transaction(function () use ($owner, $admin, $reason): void {
            $locked = User::query()->with('umkm.products.images')->lockForUpdate()->findOrFail($owner->getKey());
            self::assertOwnerTarget($locked, $admin);
            self::assertState($locked->account_status === 'anonymization_pending', 'Akun ini tidak menunggu anonimisasi.');

            $umkm = $locked->umkm;

            if ($umkm) {
                $umkm->products->each(function (Product $product) use ($admin): void {
                    $product->images()->delete();
                    $product->forceFill([
                        'category_id' => null,
                        'name' => "Produk dihapus #{$product->id}",
                        'slug' => "produk-dihapus-{$product->id}-".Str::lower(Str::random(8)),
                        'description' => null,
                        'price' => null,
                        'image' => null,
                        'is_active' => false,
                        'is_admin_blocked' => true,
                        'admin_block_reason' => 'Akun owner telah dianonimkan.',
                        'admin_blocked_at' => now(),
                        'admin_blocked_by' => $admin->id,
                        'moderation_review_requested_at' => null,
                        'moderation_review_note' => null,
                    ])->save();
                });

                $umkm->contacts()->delete();
                $umkm->socialLinks()->delete();
                $umkm->submissions()->update(['payload' => json_encode([], JSON_THROW_ON_ERROR)]);
                $umkm->forceFill([
                    'category_id' => null,
                    'name' => "UMKM dihapus #{$umkm->id}",
                    'slug' => "umkm-dihapus-{$umkm->id}-".Str::lower(Str::random(8)),
                    'description' => null,
                    'owner_name' => null,
                    'phone' => null,
                    'whatsapp' => null,
                    'email' => null,
                    'address' => null,
                    'rw' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'instagram' => null,
                    'tiktok' => null,
                    'website' => null,
                    'cover_image' => null,
                    'logo_image' => null,
                    'is_featured' => false,
                    'is_active' => false,
                    'is_admin_blocked' => true,
                    'admin_block_reason' => 'Akun owner telah dianonimkan.',
                    'admin_blocked_at' => now(),
                    'admin_blocked_by' => $admin->id,
                ])->save();
            }

            $originalEmail = $locked->email;
            $locked->notifications()->delete();
            $locked->forceFill([
                'name' => "Akun dihapus #{$locked->id}",
                'email' => "deleted-{$locked->id}-".Str::lower(Str::random(12)).'@invalid.local',
                'password' => Hash::make(Str::random(64)),
                'account_status' => 'anonymized',
                'suspended_at' => null,
                'suspension_reason' => null,
                'suspended_by' => null,
                'anonymized_at' => now(),
                'remember_token' => Str::random(60),
            ])->save();

            DB::table('password_reset_tokens')->where('email', $originalEmail)->delete();
            DB::table('sessions')->where('user_id', $locked->id)->delete();
            self::record($locked, $admin, 'owner_anonymized', $reason);
        });
    }

    public static function sendPasswordReset(User $owner, User $admin, string $reason): void
    {
        self::authorize($owner, $admin);
        self::validateReason($reason);
        self::assertState((bool) config('auth.password_reset_enabled'), 'Pengiriman reset password belum diaktifkan pada environment ini.');
        self::assertState($owner->account_status === 'active', 'Reset password hanya dapat dikirim untuk akun aktif.');

        $status = Password::sendResetLink(['email' => $owner->email]);

        self::assertState($status === Password::RESET_LINK_SENT, trans($status));
        self::record($owner, $admin, 'owner_password_reset_sent', $reason);
    }

    private static function deleteOwnedMedia(User $owner): void
    {
        $paths = collect([
            $owner->umkm?->logo_image,
            $owner->umkm?->cover_image,
            ...($owner->umkm?->products ?? collect())->pluck('image')->all(),
            ...($owner->umkm?->products ?? collect())->flatMap->images->pluck('path')->all(),
        ])->filter()->unique()->values();

        if ($paths->isEmpty()) {
            return;
        }

        $diskName = UploadDisk::name();
        $disk = Storage::disk($diskName);

        foreach ($paths as $path) {
            if ($diskName !== 'cloudinary' && Str::startsWith((string) $path, ['http://', 'https://'])) {
                continue;
            }

            try {
                if ($disk->missing($path)) {
                    continue;
                }

                if (! $disk->delete($path)) {
                    throw new RuntimeException('Filesystem menolak penghapusan media.');
                }
            } catch (Throwable $exception) {
                throw new RuntimeException(
                    'Pembersihan media belum selesai. Silakan coba anonimisasi kembali.',
                    previous: $exception,
                );
            }
        }
    }

    private static function authorize(User $owner, User $admin): void
    {
        abort_unless($admin->isAdmin() && $admin->hasActiveAccount(), 403);
        self::assertOwnerTarget($owner, $admin);
    }

    private static function assertOwnerTarget(User $owner, User $admin): void
    {
        abort_unless($owner->isUmkmOwner() && ! $owner->is($admin), 403);
    }

    private static function assertManageable(User $owner): void
    {
        self::assertState(! in_array($owner->account_status, ['anonymization_pending', 'anonymized'], true), 'Akun yang dianonimkan tidak dapat diubah.');
    }

    private static function revokeAccess(User $owner): void
    {
        DB::table('sessions')->where('user_id', $owner->id)->delete();
        DB::table('password_reset_tokens')->where('email', $owner->email)->delete();
    }

    private static function record(User $owner, User $admin, string $action, string $reason, array $metadata = []): void
    {
        ModerationAction::query()->create([
            'actor_id' => $admin->id,
            'subject_type' => User::class,
            'subject_id' => $owner->id,
            'action' => $action,
            'reason' => $reason,
            'metadata' => $metadata ?: null,
        ]);
    }

    private static function validateReason(string $reason): void
    {
        Validator::make(['reason' => $reason], [
            'reason' => ['required', 'string', 'min:10', 'max:2000'],
        ])->validate();
    }

    private static function assertState(bool $valid, string $message): void
    {
        Validator::make(['state' => $valid], ['state' => ['accepted']], [
            'state.accepted' => $message,
        ])->validate();
    }
}
