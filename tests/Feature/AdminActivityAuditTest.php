<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Resources\AdminActivityLogs\AdminActivityLogResource;
use App\Models\AdminActivityLog;
use App\Models\Category;
use App\Models\User;
use App\Support\AdminActivityLogger;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

class AdminActivityAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_activity_resource_is_read_only_and_admin_only(): void
    {
        $admin = $this->user('admin', 'admin@example.test');
        $owner = $this->user('umkm_owner', 'owner@example.test');
        AdminActivityLogger::record('admin_login', $admin, $admin);
        $log = AdminActivityLog::query()->firstOrFail();

        $this->actingAs($admin)
            ->get(AdminActivityLogResource::getUrl('index'))
            ->assertOk()
            ->assertSee('Log Aktivitas Admin');
        $this->get(AdminActivityLogResource::getUrl('view', ['record' => $log]))
            ->assertOk()
            ->assertSee('Password, token, secret, IP mentah');

        $this->assertTrue(Gate::forUser($owner)->denies('viewAny', AdminActivityLog::class));
        $this->assertFalse(AdminActivityLogResource::canCreate());
        $this->assertFalse(AdminActivityLogResource::canEdit($log));
        $this->assertFalse(AdminActivityLogResource::canDelete($log));
    }

    public function test_category_create_update_and_delete_are_audited_with_safe_diffs(): void
    {
        $admin = $this->user('admin', 'admin@example.test');
        $this->actingAs($admin);

        $category = Category::query()->create([
            'name' => 'Kuliner',
            'slug' => 'kuliner',
            'description' => 'Makanan lokal',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $created = AdminActivityLog::query()->where('event', 'category_created')->firstOrFail();
        $this->assertSame($admin->id, $created->actor_id);
        $this->assertSame('Kuliner', $created->after['name']);

        $category->update(['name' => 'Kuliner & Minuman', 'sort_order' => 2]);
        $updated = AdminActivityLog::query()->where('event', 'category_updated')->firstOrFail();
        $this->assertSame('Kuliner', $updated->before['name']);
        $this->assertSame('Kuliner & Minuman', $updated->after['name']);
        $this->assertSame(['name', 'sort_order'], $updated->metadata['changed_fields']);

        $category->delete();
        $deleted = AdminActivityLog::query()->where('event', 'category_deleted')->firstOrFail();
        $this->assertSame('Kuliner & Minuman', $deleted->subject_label);
        $this->assertSame($category->id, $deleted->subject_id);
    }

    public function test_admin_authentication_events_are_recorded_without_raw_credentials(): void
    {
        $admin = $this->user('admin', 'admin@example.test');

        Event::dispatch(new Login('web', $admin, false));
        Event::dispatch(new Logout('web', $admin));

        $request = Request::create('/admin/login', 'POST');
        app()->instance('request', $request);
        Event::dispatch(new Failed('web', $admin, [
            'email' => 'admin@example.test',
            'password' => 'rahasia-yang-tidak-boleh-tersimpan',
        ]));

        $this->assertDatabaseHas('admin_activity_logs', ['event' => 'admin_login', 'actor_id' => $admin->id]);
        $this->assertDatabaseHas('admin_activity_logs', ['event' => 'admin_logout', 'actor_id' => $admin->id]);
        $failed = AdminActivityLog::query()->where('event', 'admin_login_failed')->firstOrFail();
        $serialized = json_encode($failed->toArray(), JSON_THROW_ON_ERROR);

        $this->assertNull($failed->actor_id);
        $this->assertNotNull($failed->metadata['identity_hash']);
        $this->assertStringNotContainsString('admin@example.test', $serialized);
        $this->assertStringNotContainsString('rahasia-yang-tidak-boleh-tersimpan', $serialized);
    }

    public function test_admin_profile_change_is_logged_without_password_values(): void
    {
        $admin = $this->user('admin', 'admin@example.test');

        Livewire::actingAs($admin)
            ->test(EditProfile::class)
            ->fillForm([
                'name' => 'Admin Baru',
                'email' => 'admin@example.test',
                'password' => null,
                'passwordConfirmation' => null,
                'currentPassword' => null,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $log = AdminActivityLog::query()->where('event', 'admin_profile_updated')->firstOrFail();
        $this->assertSame('Admin', $log->before['name']);
        $this->assertSame('Admin Baru', $log->after['name']);
        $this->assertArrayNotHasKey('password', $log->before ?? []);
        $this->assertArrayNotHasKey('password', $log->after ?? []);
    }

    public function test_denied_sensitive_panel_access_is_audited(): void
    {
        $owner = $this->user('umkm_owner', 'owner@example.test');

        $this->actingAs($owner)->get('/admin/categories')->assertForbidden();

        $log = AdminActivityLog::query()->where('event', 'admin_access_denied')->firstOrFail();
        $this->assertSame($owner->id, $log->actor_id);
        $this->assertSame('filament.admin.resources.categories.index', $log->metadata['route']);
        $this->assertSame('GET', $log->metadata['method']);
        $this->assertArrayNotHasKey('ip', $log->metadata);
    }

    public function test_logger_sanitizes_sensitive_keys_recursively(): void
    {
        $admin = $this->user('admin', 'admin@example.test');

        AdminActivityLogger::record(
            event: 'admin_profile_updated',
            actor: $admin,
            subject: $admin,
            before: ['name' => 'Admin', 'password' => 'jangan-simpan'],
            metadata: ['nested' => ['token' => 'jangan-simpan', 'safe' => true]],
        );

        $log = AdminActivityLog::query()->firstOrFail();
        $this->assertSame(['name' => 'Admin'], $log->before);
        $this->assertSame(['safe' => true], $log->metadata['nested']);
        $this->assertNotNull($log->request_id);
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
