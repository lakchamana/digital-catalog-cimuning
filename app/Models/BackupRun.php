<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'requested_by', 'status', 'file_name', 'size_bytes', 'checksum_sha256',
    'manifest', 'failure_code', 'generated_at', 'completed_at', 'downloaded_at',
    'expires_at', 'failed_at',
])]
class BackupRun extends Model
{
    protected function casts(): array
    {
        return [
            'manifest' => 'array',
            'generated_at' => 'datetime',
            'completed_at' => 'datetime',
            'downloaded_at' => 'datetime',
            'expires_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function restoreRequests(): HasMany
    {
        return $this->hasMany(RestoreRequest::class);
    }
}
