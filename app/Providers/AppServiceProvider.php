<?php

namespace App\Providers;

use App\Http\Responses\OwnerRegistrationResponse;
use App\Models\Category;
use App\Models\ModerationAction;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\UmkmSubmission;
use App\Models\User;
use App\Policies\CategoryPolicy;
use App\Policies\ModerationActionPolicy;
use App\Policies\ProductPolicy;
use App\Policies\UmkmPolicy;
use App\Policies\UmkmSubmissionPolicy;
use App\Policies\UserPolicy;
use App\Support\CloudinaryStorage;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
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

        // Daftarkan Cloudinary sebagai custom filesystem disk.
        // Aktif saat FILESYSTEM_DISK=cloudinary di environment production (Railway).
        Storage::extend('cloudinary', function () {
            return new CloudinaryStorage;
        });
    }
}
