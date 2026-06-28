<?php

namespace App\Support;

use App\Filament\Resources\Products\ProductResource;
use App\Models\ModerationAction;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ContentModeration
{
    public static function blockUmkm(Umkm $umkm, User $admin, string $reason): void
    {
        abort_unless($admin->isAdmin(), 403);
        self::validateReason($reason);

        DB::transaction(function () use ($umkm, $admin, $reason): void {
            $locked = Umkm::query()->lockForUpdate()->findOrFail($umkm->getKey());
            self::validateState($locked->status === 'verified' && $locked->is_active, 'Hanya UMKM yang sedang tayang dapat dinonaktifkan publikasinya.');
            self::validateState(! $locked->is_admin_blocked, 'UMKM ini sudah dinonaktifkan admin.');

            $locked->update([
                'is_admin_blocked' => true,
                'admin_block_reason' => $reason,
                'admin_blocked_at' => now(),
                'admin_blocked_by' => $admin->id,
                'is_featured' => false,
            ]);
            self::record($locked, $admin, 'umkm_blocked', $reason);
        });

        self::notifyUmkmOwner($umkm->fresh('owner'), 'Profil UMKM dinonaktifkan admin', $reason, 'danger');
    }

    public static function unblockUmkm(Umkm $umkm, User $admin, string $reason): void
    {
        abort_unless($admin->isAdmin(), 403);
        self::validateReason($reason);

        DB::transaction(function () use ($umkm, $admin, $reason): void {
            $locked = Umkm::query()->with('owner')->lockForUpdate()->findOrFail($umkm->getKey());
            self::validateState($locked->is_admin_blocked, 'UMKM ini tidak sedang dinonaktifkan admin.');
            self::validateState(
                ! $locked->owner || ! in_array($locked->owner->account_status, ['anonymization_pending', 'anonymized'], true),
                'Publikasi akun yang sedang atau sudah dianonimkan tidak dapat dipulihkan.',
            );

            $locked->update([
                'is_admin_blocked' => false,
                'admin_block_reason' => null,
                'admin_blocked_at' => null,
                'admin_blocked_by' => null,
            ]);
            self::record($locked, $admin, 'umkm_unblocked', $reason);
        });

        self::notifyUmkmOwner($umkm->fresh('owner'), 'Profil UMKM dapat tampil kembali', $reason, 'success');
    }

    public static function setFeatured(Umkm $umkm, User $admin, bool $featured): void
    {
        abort_unless($admin->isAdmin(), 403);
        abort_unless($umkm->status === 'verified' && $umkm->is_active && ! $umkm->is_admin_blocked, 422);

        DB::transaction(function () use ($umkm, $admin, $featured): void {
            $umkm->update(['is_featured' => $featured]);
            self::record($umkm, $admin, $featured ? 'featured' : 'unfeatured');
        });
    }

    public static function blockProduct(Product $product, User $admin, string $reason): void
    {
        abort_unless($admin->isAdmin(), 403);
        self::validateReason($reason);

        DB::transaction(function () use ($product, $admin, $reason): void {
            $locked = Product::query()->lockForUpdate()->findOrFail($product->getKey());
            self::validateState(! $locked->is_admin_blocked, 'Produk ini sudah diblokir admin.');

            $locked->update([
                'is_admin_blocked' => true,
                'admin_block_reason' => $reason,
                'admin_blocked_at' => now(),
                'admin_blocked_by' => $admin->id,
                'moderation_review_requested_at' => null,
                'moderation_review_note' => null,
            ]);
            self::record($locked, $admin, 'blocked', $reason);
        });

        self::notifyProductOwner($product->fresh('umkm.owner'), 'Produk dinonaktifkan admin', $reason, 'danger');
    }

    public static function unblockProduct(Product $product, User $admin, string $reason): void
    {
        abort_unless($admin->isAdmin(), 403);
        self::validateReason($reason);

        DB::transaction(function () use ($product, $admin, $reason): void {
            $locked = Product::query()->lockForUpdate()->findOrFail($product->getKey());
            self::validateState($locked->is_admin_blocked, 'Produk ini tidak sedang diblokir.');

            $locked->update([
                'is_admin_blocked' => false,
                'admin_block_reason' => null,
                'admin_blocked_at' => null,
                'admin_blocked_by' => null,
                'moderation_review_requested_at' => null,
                'moderation_review_note' => null,
            ]);
            self::record($locked, $admin, 'unblocked', $reason);
        });

        self::notifyProductOwner($product->fresh('umkm.owner'), 'Produk dapat ditampilkan kembali', $reason, 'success');
    }

    public static function requestProductReview(Product $product, User $owner, string $note): void
    {
        abort_unless($owner->isUmkmOwner() && $product->umkm?->user_id === $owner->id, 403);
        self::validateReason($note);

        DB::transaction(function () use ($product, $owner, $note): void {
            $locked = Product::query()->with('umkm')->lockForUpdate()->findOrFail($product->getKey());
            abort_unless($locked->umkm?->user_id === $owner->id, 403);

            Validator::make([
                'blocked' => $locked->is_admin_blocked,
                'not_requested' => blank($locked->moderation_review_requested_at),
            ], [
                'blocked' => ['accepted'],
                'not_requested' => ['accepted'],
            ], [
                'blocked.accepted' => 'Produk yang tidak diblokir tidak memerlukan peninjauan ulang.',
                'not_requested.accepted' => 'Permintaan peninjauan produk ini sudah dikirim.',
            ])->validate();

            $locked->update([
                'moderation_review_requested_at' => now(),
                'moderation_review_note' => $note,
            ]);
            self::record($locked, $owner, 'review_requested', $note);
        });

        self::notifyAdminsOfProductReview($product->fresh('umkm.owner'), $note);
    }

    public static function rejectProductReview(Product $product, User $admin, string $reason): void
    {
        abort_unless($admin->isAdmin(), 403);
        self::validateReason($reason);

        DB::transaction(function () use ($product, $admin, $reason): void {
            $locked = Product::query()->lockForUpdate()->findOrFail($product->getKey());
            self::validateState($locked->is_admin_blocked, 'Produk ini tidak sedang diblokir.');
            self::validateState(filled($locked->moderation_review_requested_at), 'Produk ini tidak memiliki permintaan peninjauan aktif.');

            $locked->update([
                'admin_block_reason' => $reason,
                'moderation_review_requested_at' => null,
                'moderation_review_note' => null,
            ]);
            self::record($locked, $admin, 'review_rejected', $reason);
        });

        self::notifyProductOwner($product->fresh('umkm.owner'), 'Peninjauan produk belum disetujui', $reason, 'warning');
    }

    private static function record(Umkm|Product $subject, User $actor, string $action, ?string $reason = null): void
    {
        ModerationAction::query()->create([
            'actor_id' => $actor->id,
            'subject_type' => $subject::class,
            'subject_id' => $subject->getKey(),
            'action' => $action,
            'reason' => $reason,
        ]);
    }

    private static function validateReason(string $reason): void
    {
        Validator::make(['reason' => $reason], [
            'reason' => ['required', 'string', 'min:10', 'max:2000'],
        ])->validate();
    }

    private static function validateState(bool $valid, string $message): void
    {
        Validator::make(['state' => $valid], ['state' => ['accepted']], [
            'state.accepted' => $message,
        ])->validate();
    }

    private static function notifyProductOwner(Product $product, string $title, string $body, string $status): void
    {
        $owner = $product->umkm?->owner;

        if (! $owner) {
            return;
        }

        Notification::make()
            ->title($title)
            ->body("{$product->name}: {$body}")
            ->status($status)
            ->sendToDatabase(collect([$owner]), isEventDispatched: true);
    }

    private static function notifyUmkmOwner(Umkm $umkm, string $title, string $body, string $status): void
    {
        if (! $umkm->owner) {
            return;
        }

        Notification::make()
            ->title($title)
            ->body("{$umkm->name}: {$body}")
            ->status($status)
            ->sendToDatabase(collect([$umkm->owner]), isEventDispatched: true);
    }

    private static function notifyAdminsOfProductReview(Product $product, string $note): void
    {
        $admins = User::query()->where('role', 'admin')->get();

        if ($admins->isEmpty()) {
            return;
        }

        Notification::make()
            ->title('Produk meminta peninjauan ulang')
            ->body("{$product->name}: {$note}")
            ->warning()
            ->actions([
                Action::make('review')
                    ->label('Tinjau produk')
                    ->url(ProductResource::getUrl('view', ['record' => $product])),
            ])
            ->sendToDatabase($admins, isEventDispatched: true);
    }
}
