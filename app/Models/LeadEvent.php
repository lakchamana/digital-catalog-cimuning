<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['umkm_id', 'product_id', 'type', 'source', 'target_url', 'ip_hash', 'user_agent', 'referer'])]
class LeadEvent extends Model
{
    use HasFactory;

    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        if ($user?->isAdmin()) {
            return $query;
        }

        if ($user?->isUmkmOwner()) {
            return $query->whereHas('umkm', fn (Builder $query) => $query->where('user_id', $user->id));
        }

        return $query->whereRaw('1 = 0');
    }
}
