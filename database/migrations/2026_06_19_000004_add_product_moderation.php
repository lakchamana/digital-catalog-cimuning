<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_admin_blocked')->default(false)->index()->after('is_active');
            $table->text('admin_block_reason')->nullable()->after('is_admin_blocked');
            $table->timestamp('admin_blocked_at')->nullable()->after('admin_block_reason');
            $table->foreignId('admin_blocked_by')->nullable()->after('admin_blocked_at')->constrained('users')->nullOnDelete();
        });

        Schema::create('moderation_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->morphs('subject');
            $table->string('action', 50);
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moderation_actions');

        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('admin_blocked_by');
            $table->dropColumn(['is_admin_blocked', 'admin_block_reason', 'admin_blocked_at']);
        });
    }
};
