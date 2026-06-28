<?php

namespace App\Http\Controllers\Admin;

use App\Filament\Pages\BackupRecovery;
use App\Http\Controllers\Controller;
use App\Support\AdminActivityLogger;
use App\Support\Backup\RestoreRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RestoreRequestController extends Controller
{
    public function __invoke(Request $request, RestoreRequestService $service): RedirectResponse
    {
        $admin = $request->user();

        abort_unless($admin?->isAdmin() && $admin->hasActiveAccount(), 403);

        $validator = Validator::make($request->all(), [
            'archive' => [
                'required',
                'file',
                'mimes:zip',
                'max:'.(int) ceil(((int) config('backup.maximum_archive_bytes', 262_144_000)) / 1024),
            ],
            'current_password' => ['required', 'current_password:web'],
            'passphrase' => ['required', 'string', 'min:16'],
            'reason' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'archive.required' => 'Pilih arsip backup terenkripsi.',
            'archive.mimes' => 'Arsip restore harus berupa file ZIP.',
            'current_password.current_password' => 'Password akun admin tidak sesuai.',
            'passphrase.min' => 'Passphrase minimal 16 karakter.',
            'reason.min' => 'Alasan restore minimal 10 karakter.',
        ]);

        if ($validator->fails()) {
            $failedUploadPath = $request->file('archive')?->getRealPath();

            if (is_string($failedUploadPath) && is_file($failedUploadPath)) {
                @unlink($failedUploadPath);
            }

            AdminActivityLogger::record(
                'restore_validation_failed',
                $admin,
                subjectLabel: 'Form restore ditolak',
                metadata: ['failure_code' => 'form_validation'],
            );

            throw new ValidationException($validator);
        }

        $archive = $request->file('archive');

        try {
            $service->validateAndCreate(
                $admin,
                $archive->getRealPath(),
                (string) $request->input('passphrase'),
                (string) $request->input('reason'),
            );
        } finally {
            $path = $archive->getRealPath();

            if (is_string($path) && is_file($path)) {
                @unlink($path);
            }
        }

        return redirect(BackupRecovery::getUrl(panel: 'admin'))
            ->with('status', 'Arsip valid dan permintaan restore berhasil dicatat.');
    }
}
