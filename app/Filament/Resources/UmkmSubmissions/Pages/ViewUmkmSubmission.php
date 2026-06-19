<?php

namespace App\Filament\Resources\UmkmSubmissions\Pages;

use App\Filament\Resources\UmkmSubmissions\UmkmSubmissionResource;
use App\Support\UmkmSubmissionWorkflow;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewUmkmSubmission extends ViewRecord
{
    protected static string $resource = UmkmSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Verifikasi')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->schema([
                    Checkbox::make('data_complete')->label('Data usaha sudah lengkap dan masuk akal')->accepted()->required(),
                    Checkbox::make('contact_valid')->label('Kontak dan lokasi dapat ditindaklanjuti')->accepted()->required(),
                    Checkbox::make('content_appropriate')->label('Konten sesuai untuk direktori UMKM Cimuning')->accepted()->required(),
                    Textarea::make('notes')->label('Catatan internal atau untuk owner')->maxLength(2000),
                ])
                ->visible(fn (): bool => $this->record->status === 'pending')
                ->action(function (array $data): void {
                    UmkmSubmissionWorkflow::approve(
                        $this->record,
                        Filament::auth()->user(),
                        $data['notes'] ?? null,
                        [
                            'data_complete' => true,
                            'contact_valid' => true,
                            'content_appropriate' => true,
                        ],
                    );
                    $this->record->refresh();
                    Notification::make()->title('Pengajuan berhasil diverifikasi')->success()->send();
                }),
            Action::make('requestRevision')
                ->label('Minta revisi')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning')
                ->schema([
                    Textarea::make('notes')
                        ->label('Bagian yang harus diperbaiki owner')
                        ->required()->minLength(10)->maxLength(2000),
                ])
                ->visible(fn (): bool => $this->record->status === 'pending')
                ->action(function (array $data): void {
                    UmkmSubmissionWorkflow::requestRevision($this->record, Filament::auth()->user(), $data['notes']);
                    $this->record->refresh();
                    Notification::make()->title('Permintaan revisi dikirim')->warning()->send();
                }),
            Action::make('reject')
                ->label('Tolak')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->schema([
                    Textarea::make('notes')
                        ->label('Alasan penolakan')
                        ->required()->minLength(10)->maxLength(2000),
                ])
                ->visible(fn (): bool => $this->record->status === 'pending')
                ->action(function (array $data): void {
                    UmkmSubmissionWorkflow::reject($this->record, Filament::auth()->user(), $data['notes']);
                    $this->record->refresh();
                    Notification::make()->title('Pengajuan ditolak')->danger()->send();
                }),
        ];
    }
}
