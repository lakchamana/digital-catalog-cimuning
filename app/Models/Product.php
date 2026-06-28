<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable([
    'umkm_id', 'category_id', 'name', 'slug', 'description', 'price', 'image', 'is_active',
    'is_admin_blocked', 'admin_block_reason', 'admin_blocked_at', 'admin_blocked_by',
    'moderation_review_requested_at', 'moderation_review_note',
])]
class Product extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'is_active' => 'boolean',
            'is_admin_blocked' => 'boolean',
            'admin_blocked_at' => 'datetime',
            'moderation_review_requested_at' => 'datetime',
        ];
    }

    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function blockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_blocked_by');
    }

    public function moderationActions(): MorphMany
    {
        return $this->morphMany(ModerationAction::class, 'subject')->latest();
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('is_admin_blocked', false)
            ->whereHas('umkm', fn (Builder $umkmQuery): Builder => $umkmQuery->publiclyVisible());
    }
}
