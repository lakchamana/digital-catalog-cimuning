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

    protected static ?string $navigationLabel = 'Backup Data';

    protected static ?int $navigationSort = 5;

    protected static ?string $title = 'Backup Data';

    protected string $view = 'filament.pages.backup-recovery';

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        return $user?->isAdmin() && $user->hasActiveAccount();
    }

    public function getSubheading(): ?string
    {
        return 'Simpan salinan data secara berkala agar layanan lebih mudah dipulihkan saat terjadi kendala.';
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
                'title' => 'Belum ada backup data',
                'description' => 'Buat backup pertama agar data penting memiliki salinan yang aman.',
                'last' => null,
            ];
        }

        $hours = $last->generated_at->diffInHours(now());

        if ($hours >= (int) config('backup.critical_hours', 72)) {
            return [
                'level' => 'danger',
                'title' => 'Backup perlu dibuat sekarang',
                'description' => 'Backup terakhir sudah melewati jadwal tiga hari.',
                'last' => $last,
            ];
        }

        if ($hours >= (int) config('backup.warning_hours', 48)) {
            return [
                'level' => 'warning',
                'title' => 'Jadwal backup sudah dekat',
                'description' => 'Siapkan backup baru sebelum melewati batas tiga hari.',
                'last' => $last,
            ];
        }

        return [
            'level' => 'success',
            'title' => 'Backup data masih terjaga',
            'description' => 'Backup terakhir masih berada dalam jadwal yang disarankan.',
            'last' => $last,
        ];
    }

    public function backupStatusLabel(string $status): string
    {
        return match ($status) {
            'processing' => 'Sedang diproses',
            'completed' => 'Berhasil',
            'expired' => 'Berhasil',
            'failed' => 'Gagal',
            default => $status,
        };
    }

    public function restoreStatusLabel(string $status): string
    {
        return match ($status) {
            'validated' => 'Sudah diperiksa',
            'executed' => 'Pemulihan selesai',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            default => $status,
        };
    }

    public function createBackupAction(): Action
    {
        return Action::make('createBackup')
            ->label('Buat Backup Baru')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('primary')
            ->modalHeading('Buat backup baru')
            ->modalDescription('File akan langsung diunduh setelah selesai dibuat. Simpan password backup di tempat yang berbeda dari filenya.')
            ->modalSubmitActionLabel('Buat dan unduh')
            ->modalCancelActionLabel('Batal')
            ->schema([
                TextInput::make('current_password')
                    ->label('Password admin')
                    ->password()
                    ->revealable()
                    ->currentPassword(guard: Filament::getAuthGuard())
                    ->required(),
                TextInput::make('passphrase')
                    ->label('Password backup')
                    ->password()
                    ->revealable()
                    ->minLength(16)
                    ->required(),
                TextInput::make('passphrase_confirmation')
                    ->label('Ulangi password backup')
                    ->password()
                    ->revealable()
                    ->same('passphrase')
                    ->required(),
                Checkbox::make('acknowledgement')
                    ->label('Saya sudah menyiapkan tempat penyimpanan yang aman untuk file backup.')
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
                        ->body('Unduhan dimulai. Simpan file dan password backup secara terpisah.')
                        ->success()
                        ->send();

                    return response()->download($artifact->path, $artifact->downloadName)->deleteFileAfterSend(true);
                } catch (ValidationException $exception) {
                    throw $exception;
                } catch (Throwable) {
                    Notification::make()
                        ->title('Backup belum berhasil dibuat')
                        ->body('Tidak ada file yang disimpan. Silakan periksa pengaturan server lalu coba kembali.')
                        ->danger()
                        ->send();

                    throw ValidationException::withMessages([
                        'backup' => 'Backup belum berhasil dibuat. Silakan coba kembali setelah pengaturan server diperiksa.',
                    ]);
                }
            });
    }
}
