<?php

namespace Tests\Feature;

use App\Filament\Pages\BackupRecovery;
use App\Models\BackupRun;
use App\Models\User;
use App\Support\Backup\BackupArchiveInspector;
use App\Support\Backup\DatabaseBackupService;
use App\Support\Backup\DatabaseDumper;
use App\Support\Backup\MySqlDatabaseDumper;
use App\Support\Backup\RestoreRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use RuntimeException;
use Tests\TestCase;
use ZipArchive;

class DatabaseBackupSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('backup.enabled', true);
        config()->set('backup.cooldown_minutes', 15);
        config()->set('cache.default', 'array');

        $this->app->bind(DatabaseDumper::class, fn () => new class implements DatabaseDumper
        {
            public function dump(string $outputPath): void
            {
                file_put_contents($outputPath, "-- Cimuning test dump\nCREATE TABLE example (id INT);\n");
            }
        });
    }

    protected function tearDown(): void
    {
        RateLimiter::clear('database-backup:admin:1');

        parent::tearDown();
    }

    public function test_only_admin_can_open_backup_and_recovery_page(): void
    {
        $admin = $this->user('admin', 'admin@example.test');

        $this->actingAs($admin)->get('/admin/backup-recovery')
            ->assertOk()
            ->assertSee('Backup Data')
            ->assertSee('Buat Backup Baru')
            ->assertSee('Periksa file pemulihan')
            ->assertDontSee('Dashboard tidak dapat memulihkan database secara langsung.')
            ->assertDontSee('database.sql')
            ->assertDontSee('AES-256');

        Livewire::actingAs($admin)
            ->test(BackupRecovery::class)
            ->mountAction('createBackup')
            ->assertActionMounted('createBackup');
    }

    public function test_owner_cannot_open_backup_and_recovery_page(): void
    {
        $owner = $this->user('umkm_owner', 'owner@example.test');

        $this->actingAs($owner)->get('/admin/backup-recovery')->assertForbidden();
    }

    public function test_backup_is_aes_encrypted_and_plaintext_is_cleaned(): void
    {
        $admin = $this->user('admin', 'admin@example.test');
        $artifact = app(DatabaseBackupService::class)->create($admin, 'passphrase-sangat-aman-123');

        $this->assertFileExists($artifact->path);
        $this->assertDatabaseHas('backup_runs', [
            'id' => $artifact->run->id,
            'status' => 'completed',
            'requested_by' => $admin->id,
        ]);
        $this->assertDatabaseHas('admin_activity_logs', ['event' => 'database_backup_completed']);

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($artifact->path) === true);
        $this->assertFalse(@$zip->getFromName('database.sql'));
        $this->assertTrue($zip->setPassword('passphrase-sangat-aman-123'));
        $this->assertStringContainsString('CREATE TABLE example', (string) $zip->getFromName('database.sql'));
        $zip->close();

        $inspection = app(BackupArchiveInspector::class)->inspect($artifact->path, 'passphrase-sangat-aman-123');
        $this->assertSame($artifact->run->checksum_sha256, $inspection['checksum_sha256']);
        $this->assertSame('cimuning-digital-hub', $inspection['manifest']['application']);
    }

    public function test_wrong_passphrase_and_unencrypted_archive_are_rejected(): void
    {
        $admin = $this->user('admin', 'admin@example.test');
        $artifact = app(DatabaseBackupService::class)->create($admin, 'passphrase-sangat-aman-123');

        try {
            app(BackupArchiveInspector::class)->inspect($artifact->path, 'passphrase-yang-salah');
            $this->fail('Passphrase salah seharusnya ditolak.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('archive', $exception->errors());
        }

        $plainPath = storage_path('app/private/backups/plain-'.bin2hex(random_bytes(6)).'.zip');
        $zip = new ZipArchive;
        $zip->open($plainPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString('database.sql', 'SELECT 1;');
        $zip->addFromString('manifest.json', '{}');
        $zip->close();

        $this->expectException(ValidationException::class);
        app(BackupArchiveInspector::class)->inspect($plainPath, 'passphrase-sangat-aman-123');
    }

    public function test_inspector_rejects_an_oversized_uncompressed_entry(): void
    {
        $admin = $this->user('admin', 'admin@example.test');
        $artifact = app(DatabaseBackupService::class)->create($admin, 'passphrase-sangat-aman-123');
        config()->set('backup.maximum_uncompressed_bytes', 10);

        $this->expectException(ValidationException::class);
        app(BackupArchiveInspector::class)->inspect($artifact->path, 'passphrase-sangat-aman-123');
    }

    public function test_restore_request_requires_an_archive_from_application_history_and_never_executes_sql(): void
    {
        $admin = $this->user('admin', 'admin@example.test');
        $artifact = app(DatabaseBackupService::class)->create($admin, 'passphrase-sangat-aman-123');
        $request = app(RestoreRequestService::class)->validateAndCreate(
            $admin,
            $artifact->path,
            'passphrase-sangat-aman-123',
            'Pemulihan diperlukan setelah insiden database.',
        );

        $this->assertSame('validated', $request->status);
        $this->assertSame($artifact->run->id, $request->backup_run_id);
        $this->assertDatabaseHas('restore_requests', [
            'id' => $request->id,
            'status' => 'validated',
        ]);
        $this->assertDatabaseHas('admin_activity_logs', ['event' => 'restore_request_created']);
        $this->assertStringNotContainsString('SELECT', json_encode($request->toArray()));
    }

    public function test_admin_restore_endpoint_validates_archive_and_removes_uploaded_file(): void
    {
        $admin = $this->user('admin', 'admin@example.test');
        $artifact = app(DatabaseBackupService::class)->create($admin, 'passphrase-sangat-aman-123');
        $upload = new UploadedFile($artifact->path, $artifact->downloadName, 'application/zip', null, true);

        $this->actingAs($admin)->post(route('admin.backup.restore-request'), [
            'archive' => $upload,
            'current_password' => 'password',
            'passphrase' => 'passphrase-sangat-aman-123',
            'reason' => 'Pemulihan diperlukan untuk latihan staging.',
        ])->assertRedirect(BackupRecovery::getUrl(panel: 'admin'));

        $this->assertDatabaseCount('restore_requests', 1);
        $this->assertFileDoesNotExist($artifact->path);
        $this->assertFalse(session()->hasOldInput('passphrase'));
        $this->assertFalse(session()->hasOldInput('current_password'));
    }

    public function test_restore_rejects_an_archive_missing_from_application_history(): void
    {
        $admin = $this->user('admin', 'admin@example.test');
        $artifact = app(DatabaseBackupService::class)->create($admin, 'passphrase-sangat-aman-123');
        $artifact->run->delete();

        try {
            app(RestoreRequestService::class)->validateAndCreate(
                $admin,
                $artifact->path,
                'passphrase-sangat-aman-123',
                'Arsip ini wajib ditolak karena tidak memiliki riwayat.',
            );
            $this->fail('Arsip asing seharusnya ditolak.');
        } catch (ValidationException) {
            $this->assertDatabaseCount('restore_requests', 0);
            $this->assertDatabaseHas('admin_activity_logs', [
                'event' => 'restore_validation_failed',
                'subject_label' => 'Arsip restore tidak dikenal',
            ]);
        }
    }

    public function test_dump_failure_keeps_only_safe_metadata_and_removes_plaintext(): void
    {
        $admin = $this->user('admin', 'admin@example.test');
        $this->app->bind(DatabaseDumper::class, fn () => new class implements DatabaseDumper
        {
            public function dump(string $outputPath): void
            {
                file_put_contents($outputPath, 'partial plaintext');
                throw new RuntimeException('simulated database credential detail');
            }
        });

        try {
            app(DatabaseBackupService::class)->create($admin, 'passphrase-sangat-aman-123');
            $this->fail('Backup gagal seharusnya melempar exception.');
        } catch (RuntimeException) {
            $this->assertDatabaseHas('backup_runs', [
                'status' => 'failed',
                'failure_code' => 'RuntimeException',
            ]);
            $this->assertDatabaseMissing('admin_activity_logs', [
                'event' => 'database_backup_failed',
                'reason' => 'simulated database credential detail',
            ]);
        }
    }

    public function test_backup_rate_limit_prevents_repeated_requests(): void
    {
        $admin = $this->user('admin', 'admin@example.test');
        app(DatabaseBackupService::class)->create($admin, 'passphrase-sangat-aman-123');

        $this->expectException(ValidationException::class);
        app(DatabaseBackupService::class)->create($admin, 'passphrase-sangat-aman-456');
    }

    public function test_mysqldump_command_does_not_contain_database_credentials(): void
    {
        config()->set('database.connections.mysql.password', 'super-secret-password');
        config()->set('database.connections.mysql.username', 'private-user');

        $command = (new MySqlDatabaseDumper)->command('C:\\temp\\client.cnf', 'C:\\temp\\backup.sql', 'cimuning');
        $serialized = implode(' ', $command);

        $this->assertStringNotContainsString('super-secret-password', $serialized);
        $this->assertStringNotContainsString('private-user', $serialized);
        $this->assertStringContainsString('--defaults-extra-file=', $serialized);
        $this->assertStringContainsString('--single-transaction', $serialized);
        $this->assertStringContainsString('--no-tablespaces', $serialized);
        $this->assertStringContainsString('--ignore-table=cimuning.sessions', $serialized);
    }

    public function test_health_status_changes_at_48_and_72_hours(): void
    {
        $admin = $this->user('admin', 'admin@example.test');
        $this->actingAs($admin);
        $page = app(BackupRecovery::class);

        $this->assertSame('danger', $page->backupHealth()['level']);

        $run = BackupRun::query()->create([
            'requested_by' => $admin->id,
            'status' => 'completed',
            'generated_at' => now()->subHours(49),
        ]);
        $this->assertSame('warning', $page->backupHealth()['level']);

        $run->update(['generated_at' => now()->subHours(73)]);
        $this->assertSame('danger', $page->backupHealth()['level']);
    }

    private function user(string $role, string $email): User
    {
        return User::query()->create([
            'name' => ucfirst(str_replace('_', ' ', $role)),
            'email' => $email,
            'password' => 'password',
            'role' => $role,
        ]);
    }
}
