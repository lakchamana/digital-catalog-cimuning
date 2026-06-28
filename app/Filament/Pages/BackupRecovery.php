<?php

namespace App\Filament\Pages;

use App\Models\BackupRun;
use App\Models\RestoreRequest;
use App\Support\Backup\DatabaseBackupService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;
use UnitEnum;

class BackupRecovery extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-circle-stack';

    protected static string|UnitEnum|null $navigationGroup = 'Administrasi';

    protected static ?string $navigationLabel = 'Backup & Pemulihan';

    protected static ?int $navigationSort = 5;

    protected static ?string $title = 'Backup & Pemulihan';

    protected string $view = 'filament.pages.backup-recovery';

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        return $user?->isAdmin() && $user->hasActiveAccount();
    }

    /** @return Collection<int, BackupRun> */
    public function backupRuns(): Collection
    {
        return BackupRun::query()->with('requester')->latest()->limit(10)->get();
    }

    /** @return Collection<int, RestoreRequest> */
    public function restoreRequests(): Collection
    {
        return RestoreRequest::query()->with(['requester', 'backupRun'])->latest()->limit(10)->get();
    }

    /** @return array{level: string, title: string, description: string, last: ?BackupRun} */
    public function backupHealth(): array
    {
        $last = BackupRun::query()
            ->whereIn('status', ['completed', 'expired'])
            ->whereNotNull('generated_at')
            ->latest('generated_at')
            ->first();

        if (! $last) {
            return [
                'level' => 'danger',
                'title' => 'Backup database belum tersedia',
                'description' => 'Buat backup terenkripsi sebelum aplikasi digunakan secara luas.',
                'last' => null,
            ];
        }

        $hours = $last->generated_at->diffInHours(now());

        if ($hours >= (int) config('backup.critical_hours', 72)) {
            return [
                'level' => 'danger',
                'title' => 'Backup melewati batas 72 jam',
                'description' => 'Buat dan simpan backup baru sesegera mungkin.',
                'last' => $last,
            ];
        }

        if ($hours >= (int) config('backup.warning_hours', 48)) {
            return [
                'level' => 'warning',
                'title' => 'Backup perlu diperbarui',
                'description' => 'Usia backup sudah lebih dari 48 jam dan mendekati batas operasional.',
                'last' => $last,
            ];
        }

        return [
            'level' => 'success',
            'title' => 'Backup database masih terjaga',
            'description' => 'Backup terakhir masih berada dalam target maksimum 72 jam.',
            'last' => $last,
        ];
    }

    public function backupStatusLabel(string $status): string
    {
        return match ($status) {
            'processing' => 'Sedang diproses',
            'completed' => 'Selesai',
            'expired' => 'Arsip server dihapus',
            'failed' => 'Gagal',
            default => $status,
        };
    }

    public function restoreStatusLabel(string $status): string
    {
        return match ($status) {
            'validated' => 'Valid - menunggu prosedur manual',
            'executed' => 'Selesai melalui runbook',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            default => $status,
        };
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createBackup')
                ->label('Buat dan Unduh Backup')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->modalHeading('Buat backup database terenkripsi')
                ->modalDescription('Passphrase tidak disimpan. Simpan di password manager dan jangan menaruhnya bersama file backup.')
                ->schema([
                    TextInput::make('current_password')
                        ->label('Password akun admin')
                        ->password()
                        ->revealable()
                        ->currentPassword(guard: Filament::getAuthGuard())
                        ->required(),
                    TextInput::make('passphrase')
                        ->label('Passphrase enkripsi')
                        ->password()
                        ->revealable()
                        ->minLength(16)
                        ->required(),
                    TextInput::make('passphrase_confirmation')
                        ->label('Ulangi passphrase')
                        ->password()
                        ->revealable()
                        ->same('passphrase')
                        ->required(),
                    Checkbox::make('acknowledgement')
                        ->label('Saya akan menyimpan file dan passphrase secara terpisah.')
                        ->accepted()
                        ->required(),
                ])
                ->action(function (array $data): BinaryFileResponse {
                    try {
                        $service = app(DatabaseBackupService::class);
                        $service->cleanupExpired();
                        $artifact = $service->create(Filament::auth()->user(), $data['passphrase']);
                        $service->markDownloaded($artifact->run, Filament::auth()->user());

                        Notification::make()
                            ->title('Backup berhasil dibuat')
                            ->body('Unduhan dimulai. Simpan arsip di penyimpanan terenkripsi.')
                            ->success()
                            ->send();

                        return response()->download($artifact->path, $artifact->downloadName)->deleteFileAfterSend(true);
                    } catch (ValidationException $exception) {
                        throw $exception;
                    } catch (Throwable) {
                        Notification::make()
                            ->title('Backup tidak dapat dibuat')
                            ->body('Tidak ada file plaintext yang dipertahankan. Periksa konfigurasi server atau log aman.')
                            ->danger()
                            ->send();

                        throw ValidationException::withMessages([
                            'backup' => 'Proses backup gagal secara aman. Silakan coba lagi setelah memeriksa konfigurasi.',
                        ]);
                    }
                }),
        ];
    }
}
