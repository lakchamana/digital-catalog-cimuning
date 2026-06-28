<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('umkms', function (Blueprint $table): void {
            $table->boolean('is_admin_blocked')->default(false)->after('is_active')->index();
            $table->text('admin_block_reason')->nullable()->after('is_admin_blocked');
            $table->timestamp('admin_blocked_at')->nullable()->after('admin_block_reason');
            $table->foreignId('admin_blocked_by')->nullable()->after('admin_blocked_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('umkms', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('admin_blocked_by');
            $table->dropColumn([
                'is_admin_blocked',
                'admin_block_reason',
                'admin_blocked_at',
            ]);
        });
    }
};
