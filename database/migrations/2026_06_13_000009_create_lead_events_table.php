<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('umkm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 20)->index();
            $table->string('source', 50)->nullable()->index();
            $table->string('target_url', 2048);
            $table->string('ip_hash')->nullable()->index();
            $table->string('user_agent', 500)->nullable();
            $table->string('referer', 1000)->nullable();
            $table->timestamps();

            $table->index(['umkm_id', 'type', 'created_at']);
            $table->index(['product_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_events');
    }
};
