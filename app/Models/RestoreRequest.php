<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'backup_run_id', 'requested_by', 'status', 'reason', 'checksum_sha256',
    'manifest', 'validated_at', 'resolved_by', 'resolved_at', 'resolution_notes',
])]
class RestoreRequest extends Model
{
    protected function casts(): array
    {
        return [
            'manifest' => 'array',
            'validated_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function backupRun(): BelongsTo
    {
        return $this->belongsTo(BackupRun::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
