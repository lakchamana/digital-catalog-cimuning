<?php

namespace App\Support;

use App\Filament\Resources\Umkms\UmkmResource;
use App\Filament\Resources\UmkmSubmissions\UmkmSubmissionResource;
use App\Models\Umkm;
use App\Models\UmkmSubmission;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UmkmSubmissionWorkflow
{
    public const OWNER_FIELDS = [
        'category_id', 'name', 'description', 'owner_name', 'phone', 'whatsapp', 'email',
        'address', 'rw', 'latitude', 'longitude', 'instagram', 'tiktok', 'website',
        'cover_image', 'logo_image', 'service_delivery', 'service_cod',
        'service_custom_order', 'has_physical_store',
    ];

    public static function payloadFromUmkm(Umkm $umkm): array
    {
        return Arr::only($umkm->attributesToArray(), self::OWNER_FIELDS);
    }

    public static function formData(Umkm $umkm): array
    {
        $submission = $umkm->submissions()
            ->whereIn('status', ['pending', 'need_revision', 'rejected'])
            ->first();

        return [
            ...$umkm->attributesToArray(),
            ...($submission?->payload ?? []),
        ];
    }

    public static function submit(Umkm $umkm, User $owner, array $data): UmkmSubmission
    {
        abort_unless($owner->isUmkmOwner() && $umkm->user_id === $owner->id, 403);

        return DB::transaction(function () use ($umkm, $owner, $data): UmkmSubmission {
            $lockedUmkm = Umkm::query()->lockForUpdate()->findOrFail($umkm->getKey());
            $payload = Arr::only($data, self::OWNER_FIELDS);
            $type = $lockedUmkm->status === 'verified' ? 'update' : 'initial';

            $lockedUmkm->submissions()
                ->where('status', 'pending')
                ->update(['status' => 'superseded']);

            if ($type === 'initial') {
                $lockedUmkm->update([
                    ...$payload,
                    'status' => 'pending',
                    'is_active' => false,
                ]);
            }

            $submission = $lockedUmkm->submissions()->create([
                'submitted_by' => $owner->id,
                'type' => $type,
                'status' => 'pending',
                'payload' => $payload,
                'submitted_at' => now(),
            ]);

            self::notifyAdmins($submission);

            return $submission;
        });
    }

    public static function approve(UmkmSubmission $submission, User $reviewer, ?string $notes, array $checklist): void
    {
        Validator::make($checklist, [
            'data_complete' => ['accepted'],
            'contact_valid' => ['accepted'],
            'content_appropriate' => ['accepted'],
        ])->validate();

        self::review($submission, $reviewer, 'approved', $notes, $checklist);
    }

    public static function requestRevision(UmkmSubmission $submission, User $reviewer, string $notes): void
    {
        self::validateReason($notes);
        self::review($submission, $reviewer, 'need_revision', $notes);
    }

    public static function reject(UmkmSubmission $submission, User $reviewer, string $notes): void
    {
        self::validateReason($notes);
        self::review($submission, $reviewer, 'rejected', $notes);
    }

    private static function review(
        UmkmSubmission $submission,
        User $reviewer,
        string $decision,
        ?string $notes,
        array $checklist = [],
    ): void {
        abort_unless($reviewer->isAdmin(), 403);

        DB::transaction(function () use ($submission, $reviewer, $decision, $notes, $checklist): void {
            $locked = UmkmSubmission::query()->lockForUpdate()->findOrFail($submission->getKey());

            if (! $locked->isPending()) {
                throw ValidationException::withMessages([
                    'submission' => 'Pengajuan ini sudah diproses atau telah digantikan pengajuan baru.',
                ]);
            }

            $umkm = Umkm::query()->lockForUpdate()->findOrFail($locked->umkm_id);

            if ($decision === 'approved') {
                $umkm->update([
                    ...Arr::only($locked->payload, self::OWNER_FIELDS),
                    'status' => 'verified',
                    'is_active' => true,
                ]);
            } elseif ($locked->type === 'initial') {
                $umkm->update([
                    'status' => $decision,
                    'is_active' => false,
                ]);
            }

            $locked->update([
                'status' => $decision,
                'reviewed_by' => $reviewer->id,
                'review_notes' => $notes,
                'review_checklist' => $checklist ?: null,
                'reviewed_at' => now(),
            ]);

            self::notifyOwner($locked->fresh(['umkm.owner']));
        });
    }

    private static function notifyAdmins(UmkmSubmission $submission): void
    {
        $admins = User::query()->where('role', 'admin')->get();

        if ($admins->isEmpty()) {
            return;
        }

        Notification::make()
            ->title($submission->type === 'update' ? 'Perubahan profil menunggu review' : 'Pendaftaran UMKM baru')
            ->body("{$submission->umkm->name} menunggu keputusan verifikasi.")
            ->warning()
            ->actions([
                Action::make('review')
                    ->label('Tinjau pengajuan')
                    ->url(UmkmSubmissionResource::getUrl('view', ['record' => $submission])),
            ])
            ->sendToDatabase($admins, isEventDispatched: true);
    }

    private static function validateReason(string $notes): void
    {
        Validator::make(['notes' => $notes], [
            'notes' => ['required', 'string', 'min:10', 'max:2000'],
        ])->validate();
    }

    private static function notifyOwner(UmkmSubmission $submission): void
    {
        $owner = $submission->umkm->owner;

        if (! $owner) {
            return;
        }

        [$title, $status] = match ($submission->status) {
            'approved' => ['Pengajuan UMKM disetujui', 'success'],
            'need_revision' => ['Profil UMKM perlu diperbaiki', 'warning'],
            default => ['Pengajuan UMKM ditolak', 'danger'],
        };

        $body = $submission->status === 'approved'
            ? "{$submission->umkm->name} sudah disetujui dan tampil di direktori."
            : (string) $submission->review_notes;

        Notification::make()
            ->title($title)
            ->body($body)
            ->status($status)
            ->actions([
                Action::make('open')
                    ->label('Buka profil UMKM')
                    ->url(UmkmResource::getUrl('edit', ['record' => $submission->umkm])),
            ])
            ->sendToDatabase(collect([$owner]), isEventDispatched: true);
    }
}
