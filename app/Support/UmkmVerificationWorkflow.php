<?php

namespace App\Support;

use App\Filament\Resources\Umkms\UmkmResource;
use App\Models\Umkm;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

class UmkmVerificationWorkflow
{
    public static function notifyAdminsOfRegistration(Umkm $umkm): void
    {
        $admins = User::query()
            ->where('role', 'admin')
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        self::databaseNotification(
            users: $admins,
            title: 'Pendaftaran UMKM baru',
            body: "{$umkm->name} menunggu verifikasi admin.",
            status: 'warning',
            url: self::editUrl($umkm),
        );
    }

    public static function verify(Umkm $umkm): void
    {
        $umkm->update([
            'status' => 'verified',
            'is_active' => true,
        ]);

        self::notifyOwner(
            umkm: $umkm->refresh(),
            title: 'UMKM terverifikasi',
            body: "{$umkm->name} sudah tampil di direktori publik Cimuning Digital Hub.",
            status: 'success',
        );
    }

    public static function requestRevision(Umkm $umkm): void
    {
        $umkm->update([
            'status' => 'need_revision',
            'is_active' => false,
        ]);

        self::notifyOwner(
            umkm: $umkm->refresh(),
            title: 'Profil UMKM perlu revisi',
            body: "{$umkm->name} perlu diperbaiki sebelum bisa tampil di direktori publik.",
            status: 'warning',
        );
    }

    public static function reject(Umkm $umkm): void
    {
        $umkm->update([
            'status' => 'rejected',
            'is_active' => false,
        ]);

        self::notifyOwner(
            umkm: $umkm->refresh(),
            title: 'Pendaftaran UMKM ditolak',
            body: "{$umkm->name} belum dapat ditampilkan di direktori publik.",
            status: 'danger',
        );
    }

    protected static function notifyOwner(Umkm $umkm, string $title, string $body, string $status): void
    {
        if (! $umkm->owner) {
            return;
        }

        self::databaseNotification(
            users: collect([$umkm->owner]),
            title: $title,
            body: $body,
            status: $status,
            url: self::editUrl($umkm),
        );
    }

    /**
     * @param  Collection<int, User>  $users
     */
    protected static function databaseNotification(Collection $users, string $title, string $body, string $status, string $url): void
    {
        Notification::make()
            ->title($title)
            ->body($body)
            ->status($status)
            ->actions([
                Action::make('open')
                    ->label('Buka UMKM')
                    ->url($url),
            ])
            ->sendToDatabase($users, isEventDispatched: true);
    }

    protected static function editUrl(Umkm $umkm): string
    {
        return UmkmResource::getUrl('edit', ['record' => $umkm]);
    }
}
