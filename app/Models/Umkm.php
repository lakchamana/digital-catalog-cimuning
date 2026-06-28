<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable([
    'user_id',
    'category_id',
    'name',
    'slug',
    'description',
    'owner_name',
    'phone',
    'whatsapp',
    'email',
    'address',
    'rw',
    'latitude',
    'longitude',
    'instagram',
    'tiktok',
    'website',
    'cover_image',
    'logo_image',
    'status',
    'is_featured',
    'is_active',
    'is_admin_blocked',
    'admin_block_reason',
    'admin_blocked_at',
    'admin_blocked_by',
    'service_delivery',
    'service_cod',
    'service_custom_order',
    'has_physical_store',
])]
class Umkm extends Model
{
    use HasFactory;

    /**
     * @return array<string, string>
     */
    public static function rwOptions(): array
    {
        return collect(range(1, 26))
            ->mapWithKeys(function (int $number): array {
                $rw = sprintf('RW %02d', $number);

                return [$rw => $rw];
            })
            ->all();
    }

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'is_admin_blocked' => 'boolean',
            'admin_blocked_at' => 'datetime',
            'service_delivery' => 'boolean',
            'service_cod' => 'boolean',
            'service_custom_order' => 'boolean',
            'has_physical_store' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function blockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_blocked_by');
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('status', 'verified')
            ->where('is_admin_blocked', false);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(UmkmSubmission::class)->latest('submitted_at')->latest('id');
    }

    public function latestSubmission(): HasOne
    {
        return $this->hasOne(UmkmSubmission::class)->latestOfMany('submitted_at');
    }

    public function moderationActions(): MorphMany
    {
        return $this->morphMany(ModerationAction::class, 'subject')->latest();
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(UmkmContact::class);
    }

    public function socialLinks(): HasMany
    {
        return $this->hasMany(UmkmSocialLink::class);
    }

    public function getIsVerifiedAttribute(): bool
    {
        return $this->status === 'verified';
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        if (! $this->whatsapp) {
            return null;
        }

        $number = preg_replace('/\D+/', '', $this->whatsapp);

        if (str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        }

        return "https://wa.me/{$number}";
    }
}
