<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->timestamp('moderation_review_requested_at')->nullable()->index()->after('admin_blocked_by');
            $table->text('moderation_review_note')->nullable()->after('moderation_review_requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['moderation_review_requested_at', 'moderation_review_note']);
        });
    }
};
