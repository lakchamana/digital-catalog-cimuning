<?php

namespace App\Support;

use App\Models\ModerationAction;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ContentModeration
{
    public static function setFeatured(Umkm $umkm, User $admin, bool $featured): void
    {
        abort_unless($admin->isAdmin(), 403);
        abort_unless($umkm->status === 'verified' && $umkm->is_active, 422);

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
            $product->update([
                'is_admin_blocked' => true,
                'admin_block_reason' => $reason,
                'admin_blocked_at' => now(),
                'admin_blocked_by' => $admin->id,
            ]);
            self::record($product, $admin, 'blocked', $reason);
        });

        self::notifyProductOwner($product->fresh('umkm.owner'), 'Produk dinonaktifkan admin', $reason, 'danger');
    }

    public static function unblockProduct(Product $product, User $admin, string $reason): void
    {
        abort_unless($admin->isAdmin(), 403);
        self::validateReason($reason);

        DB::transaction(function () use ($product, $admin, $reason): void {
            $product->update([
                'is_admin_blocked' => false,
                'admin_block_reason' => null,
                'admin_blocked_at' => null,
                'admin_blocked_by' => null,
            ]);
            self::record($product, $admin, 'unblocked', $reason);
        });

        self::notifyProductOwner($product->fresh('umkm.owner'), 'Produk dapat ditampilkan kembali', $reason, 'success');
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
}
