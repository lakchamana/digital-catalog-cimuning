<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    'service_delivery',
    'service_cod',
    'service_custom_order',
    'has_physical_store',
    'view_count',
])]
class Umkm extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'service_delivery' => 'boolean',
            'service_cod' => 'boolean',
            'service_custom_order' => 'boolean',
            'has_physical_store' => 'boolean',
            'view_count' => 'integer',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(UmkmContact::class);
    }

    public function socialLinks(): HasMany
    {
        return $this->hasMany(UmkmSocialLink::class);
    }

    public function leadEvents(): HasMany
    {
        return $this->hasMany(LeadEvent::class);
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
