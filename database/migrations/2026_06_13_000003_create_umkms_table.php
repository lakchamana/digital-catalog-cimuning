<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('umkms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('rw', 10)->nullable()->index();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('instagram')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('website')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('logo_image')->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected', 'need_revision'])->default('pending')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('service_delivery')->default(false);
            $table->boolean('service_cod')->default(false);
            $table->boolean('service_custom_order')->default(false);
            $table->boolean('has_physical_store')->default(false);
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'status']);
            $table->index(['name', 'rw']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkms');
    }
};
