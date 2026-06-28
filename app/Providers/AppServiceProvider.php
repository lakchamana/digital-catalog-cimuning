<?php

namespace App\Providers;

use App\Http\Responses\OwnerRegistrationResponse;
use App\Models\AdminActivityLog;
use App\Models\BackupRun;
use App\Models\Category;
use App\Models\ModerationAction;
use App\Models\Product;
use App\Models\RestoreRequest;
use App\Models\Umkm;
use App\Models\UmkmSubmission;
use App\Models\User;
use App\Observers\CategoryObserver;
use App\Policies\AdminActivityLogPolicy;
use App\Policies\BackupRunPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\ModerationActionPolicy;
use App\Policies\ProductPolicy;
use App\Policies\RestoreRequestPolicy;
use App\Policies\UmkmPolicy;
use App\Policies\UmkmSubmissionPolicy;
use App\Policies\UserPolicy;
use App\Support\AdminActivityLogger;
use App\Support\Backup\DatabaseDumper;
use App\Support\Backup\MySqlDatabaseDumper;
use App\Support\CloudinaryStorage;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(RegistrationResponse::class, OwnerRegistrationResponse::class);
        $this->app->bind(DatabaseDumper::class, MySqlDatabaseDumper::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS di production — Railway proxy terminates SSL,
        // jadi PHP built-in server hanya melihat HTTP.
        // Tanpa ini, semua URL generate http:// → Mixed Content error.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Umkm::class, UmkmPolicy::class);
        Gate::policy(UmkmSubmission::class, UmkmSubmissionPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(ModerationAction::class, ModerationActionPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(AdminActivityLog::class, AdminActivityLogPolicy::class);
        Gate::policy(BackupRun::class, BackupRunPolicy::class);
        Gate::policy(RestoreRequest::class, RestoreRequestPolicy::class);

        Category::observe(CategoryObserver::class);

        Event::listen(Login::class, function (Login $event): void {
            if ($event->user instanceof User && $event->user->isAdmin()) {
                AdminActivityLogger::authentication('admin_login', $event->user, $event->guard);
            }
        });

        Event::listen(Logout::class, function (Logout $event): void {
            if ($event->user instanceof User && $event->user->isAdmin()) {
                AdminActivityLogger::authentication('admin_logout', $event->user, $event->guard);
            }
        });

        Event::listen(Failed::class, function (Failed $event): void {
            if (! request()->is('admin', 'admin/*')) {
                return;
            }

            $target = $event->user instanceof User ? $event->user : null;

            AdminActivityLogger::authentication('admin_login_failed', $target, $event->guard, [
                'identity_hash' => AdminActivityLogger::failedIdentityHash($event->credentials['email'] ?? null),
            ]);
        });

        // Daftarkan Cloudinary sebagai custom filesystem disk.
        // Aktif saat FILESYSTEM_DISK=cloudinary di environment production (Railway).
        Storage::extend('cloudinary', function () {
            return new CloudinaryStorage;
        });
    }
}
