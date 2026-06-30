<?php

namespace App\Filament\Resources\OwnerAccounts;

use App\Filament\Resources\OwnerAccounts\Pages\ListOwnerAccounts;
use App\Filament\Resources\OwnerAccounts\Pages\ViewOwnerAccount;
use App\Models\User;
use App\Support\OwnerAccountAdministration;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class OwnerAccountResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Akun Owner';

    protected static ?string $modelLabel = 'Akun Owner';

    protected static ?string $pluralModelLabel = 'Akun Owner';

    protected static string|UnitEnum|null $navigationGroup = 'Administrasi';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', 'umkm_owner')
            ->with(['umkm', 'suspendedBy']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->label('Nama owner')->searchable(),
                TextColumn::make('email')->label('Email login')->searchable()->copyable(),
                TextColumn::make('umkm.name')->label('UMKM')->placeholder('Belum mendaftarkan UMKM')->searchable(),
                TextColumn::make('account_status')->label('Status akun')->badge()
                    ->formatStateUsing(fn (string $state): string => self::statusLabel($state))
                    ->color(fn (string $state): string => self::statusColor($state)),
                TextColumn::make('privacy_accepted_at')->label('Persetujuan privasi')->dateTime()->placeholder('Belum tercatat')
                    ->toggleable(),
                TextColumn::make('privacy_version')->label('Versi privasi')->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('terms_accepted_at')->label('Persetujuan syarat')->dateTime()->placeholder('Belum tercatat')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('terms_version')->label('Versi syarat')->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('Terdaftar')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('account_status')->label('Status akun')->options([
                    'active' => 'Aktif',
                    'suspended' => 'Ditangguhkan',
                    'anonymization_pending' => 'Anonimisasi tertunda',
                    'anonymized' => 'Dianonimkan',
                ]),
            ])
            ->recordUrl(fn (User $record): string => static::getUrl('view', ['record' => $record]))
            ->recordActions([
                ViewAction::make(),
                self::correctIdentityAction(),
                self::suspendAction(),
                self::reactivateAction(),
                self::sendPasswordResetAction(),
                self::anonymizeAction(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identitas akun')
                ->description('Password tidak pernah ditampilkan atau ditentukan oleh admin.')
                ->schema([
                    TextEntry::make('name')->label('Nama owner'),
                    TextEntry::make('email')->label('Email login')->copyable(),
                    TextEntry::make('account_status')->label('Status akun')->badge()
                        ->formatStateUsing(fn (string $state): string => self::statusLabel($state))
                        ->color(fn (string $state): string => self::statusColor($state)),
                    TextEntry::make('created_at')->label('Waktu registrasi')->dateTime(),
                    TextEntry::make('privacy_accepted_at')->label('Persetujuan privasi')->dateTime()->placeholder('Belum tercatat'),
                    TextEntry::make('privacy_version')->label('Versi kebijakan')->placeholder('-'),
                    TextEntry::make('terms_accepted_at')->label('Persetujuan syarat')->dateTime()->placeholder('Belum tercatat'),
                    TextEntry::make('terms_version')->label('Versi syarat')->placeholder('-'),
                ])->columns(2),
            Section::make('Status operasional')
                ->schema([
                    TextEntry::make('umkm.name')->label('UMKM')->placeholder('Belum mendaftarkan UMKM'),
                    TextEntry::make('suspended_at')->label('Ditangguhkan pada')->dateTime()->placeholder('-'),
                    TextEntry::make('suspendedBy.name')->label('Ditangguhkan oleh')->placeholder('-'),
                    TextEntry::make('suspension_reason')->label('Alasan penangguhan')->placeholder('-')->columnSpanFull(),
                    TextEntry::make('anonymization_requested_at')->label('Anonimisasi dimulai')->dateTime()->placeholder('-'),
                    TextEntry::make('anonymized_at')->label('Anonimisasi selesai')->dateTime()->placeholder('-'),
                ])->columns(2),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOwnerAccounts::route('/'),
            'view' => ViewOwnerAccount::route('/{record}'),
        ];
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'active' => 'Aktif',
            'suspended' => 'Ditangguhkan',
            'anonymization_pending' => 'Anonimisasi tertunda',
            'anonymized' => 'Dianonimkan',
            default => $status,
        };
    }

    private static function statusColor(string $status): string
    {
        return match ($status) {
            'active' => 'success',
            'suspended', 'anonymization_pending' => 'warning',
            'anonymized' => 'gray',
            default => 'gray',
        };
    }

    private static function correctIdentityAction(): Action
    {
        return Action::make('correctIdentity')
            ->label('Koreksi identitas')
            ->icon('heroicon-o-pencil-square')
            ->color('info')
            ->fillForm(fn (User $record): array => ['name' => $record->name, 'email' => $record->email])
            ->schema([
                TextInput::make('name')->label('Nama owner')->required()->minLength(2)->maxLength(255),
                TextInput::make('email')->label('Email login')->email()->required()->maxLength(255),
                Textarea::make('reason')->label('Dasar koreksi')->helperText('Catat permintaan owner atau alasan administratif yang sah.')
                    ->required()->minLength(10)->maxLength(2000),
            ])
            ->visible(fn (User $record): bool => ! in_array($record->account_status, ['anonymization_pending', 'anonymized'], true))
            ->action(function (User $record, array $data): void {
                OwnerAccountAdministration::correctIdentity($record, auth()->user(), $data['name'], $data['email'], $data['reason']);
                self::notifySuccess('Identitas akun diperbarui', 'Perubahan telah dicatat dalam log administrasi.');
            });
    }

    private static function suspendAction(): Action
    {
        return Action::make('suspend')
            ->label('Tangguhkan akun')
            ->icon('heroicon-o-lock-closed')
            ->color('danger')
            ->requiresConfirmation()
            ->schema([
                Textarea::make('reason')->label('Alasan penangguhan')->required()->minLength(10)->maxLength(2000),
            ])
            ->visible(fn (User $record): bool => $record->account_status === 'active')
            ->action(function (User $record, array $data): void {
                OwnerAccountAdministration::suspend($record, auth()->user(), $data['reason']);
                self::notifySuccess('Akun berhasil ditangguhkan', 'Seluruh session owner telah dicabut. Publikasi UMKM tidak berubah.');
            });
    }

    private static function reactivateAction(): Action
    {
        return Action::make('reactivate')
            ->label('Aktifkan akun')
            ->icon('heroicon-o-lock-open')
            ->color('success')
            ->schema([
                Textarea::make('reason')->label('Catatan pengaktifan')->required()->minLength(10)->maxLength(2000),
            ])
            ->visible(fn (User $record): bool => $record->account_status === 'suspended')
            ->action(function (User $record, array $data): void {
                OwnerAccountAdministration::reactivate($record, auth()->user(), $data['reason']);
                self::notifySuccess('Akun berhasil diaktifkan kembali');
            });
    }

    private static function sendPasswordResetAction(): Action
    {
        return Action::make('sendPasswordReset')
            ->label('Kirim link reset')
            ->icon('heroicon-o-envelope')
            ->color('gray')
            ->requiresConfirmation()
            ->schema([
                Textarea::make('reason')->label('Catatan permintaan')->required()->minLength(10)->maxLength(2000),
            ])
            ->visible(fn (User $record): bool => (bool) config('auth.password_reset_enabled') && $record->account_status === 'active')
            ->action(function (User $record, array $data): void {
                OwnerAccountAdministration::sendPasswordReset($record, auth()->user(), $data['reason']);
                self::notifySuccess('Link reset password dikirim', 'Link dikirim ke email owner dan password tetap tidak diketahui admin.');
            });
    }

    private static function anonymizeAction(): Action
    {
        return Action::make('anonymize')
            ->label('Anonimkan akun')
            ->icon('heroicon-o-user-minus')
            ->color('danger')
            ->modalHeading('Anonimkan akun secara permanen')
            ->modalDescription('Tindakan ini membersihkan identitas, kontak, dan media owner. Audit keputusan minimum tetap disimpan.')
            ->schema([
                TextInput::make('confirmation')->label('Ketik ANONIMKAN')->required(),
                Textarea::make('reason')->label('Dasar anonimisasi')->helperText('Pastikan permintaan penghapusan telah diverifikasi.')
                    ->required()->minLength(10)->maxLength(2000),
            ])
            ->visible(fn (User $record): bool => in_array($record->account_status, ['suspended', 'anonymization_pending'], true))
            ->action(function (User $record, array $data): void {
                OwnerAccountAdministration::anonymize($record, auth()->user(), $data['confirmation'], $data['reason']);
                self::notifySuccess('Anonimisasi akun selesai', 'Data pribadi telah dibersihkan dan akun tidak dapat digunakan kembali.');
            });
    }

    private static function notifySuccess(string $title, ?string $body = null): void
    {
        Notification::make()->title($title)->body($body)->success()->send();
    }
}
