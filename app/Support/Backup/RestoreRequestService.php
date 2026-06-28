<?php

namespace App\Support\Backup;

use App\Models\BackupRun;
use App\Models\RestoreRequest;
use App\Models\User;
use App\Support\AdminActivityLogger;
use Illuminate\Validation\ValidationException;
use Throwable;

class RestoreRequestService
{
    public function __construct(private readonly BackupArchiveInspector $inspector) {}

    public function validateAndCreate(User $admin, string $path, string $passphrase, string $reason): RestoreRequest
    {
        if (! $admin->isAdmin() || ! $admin->hasActiveAccount()) {
            abort(403);
        }

        if (mb_strlen($reason) < 10) {
            throw ValidationException::withMessages(['reason' => 'Alasan restore minimal 10 karakter.']);
        }

        try {
            $inspection = $this->inspector->inspect($path, $passphrase);
        } catch (Throwable $exception) {
            AdminActivityLogger::record(
                'restore_validation_failed',
                $admin,
                subjectLabel: 'Arsip restore ditolak',
                metadata: ['failure_code' => class_basename($exception)],
            );

            throw $exception;
        }
        $run = BackupRun::query()
            ->whereIn('status', ['completed', 'expired'])
            ->where('checksum_sha256', $inspection['checksum_sha256'])
            ->first();

        if (! $run) {
            AdminActivityLogger::record(
                'restore_validation_failed',
                $admin,
                subjectLabel: 'Arsip restore tidak dikenal',
                metadata: ['failure_code' => 'archive_history_mismatch'],
            );

            throw ValidationException::withMessages([
                'archive' => 'Arsip tidak cocok dengan riwayat backup yang dibuat aplikasi ini.',
            ]);
        }

        $request = RestoreRequest::query()->create([
            'backup_run_id' => $run->id,
            'requested_by' => $admin->id,
            'status' => 'validated',
            'reason' => $reason,
            'checksum_sha256' => $inspection['checksum_sha256'],
            'manifest' => $inspection['manifest'],
            'validated_at' => now(),
        ]);

        AdminActivityLogger::record(
            'restore_request_created',
            $admin,
            $request,
            'Permintaan restore #'.$request->id,
            reason: $reason,
            metadata: ['backup_run_id' => $run->id, 'checksum_sha256' => $inspection['checksum_sha256']],
        );

        return $request;
    }
}
