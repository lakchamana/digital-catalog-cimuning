<?php

namespace App\Providers;

use App\Http\Responses\OwnerRegistrationResponse;
use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Policies\CategoryPolicy;
use App\Policies\ProductPolicy;
use App\Policies\UmkmPolicy;
use App\Support\CloudinaryStorage;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
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
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Umkm::class, UmkmPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);

        // Daftarkan Cloudinary sebagai custom filesystem disk.
        // Aktif saat FILESYSTEM_DISK=cloudinary di environment production (Railway).
        Storage::extend('cloudinary', function () {
            return new CloudinaryStorage();
        });
    }
}
