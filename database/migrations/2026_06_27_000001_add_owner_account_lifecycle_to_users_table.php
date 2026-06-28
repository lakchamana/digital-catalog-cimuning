<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('account_status', 30)->default('active')->after('role')->index();
            $table->timestamp('suspended_at')->nullable()->after('account_status');
            $table->text('suspension_reason')->nullable()->after('suspended_at');
            $table->foreignId('suspended_by')->nullable()->after('suspension_reason')->constrained('users')->nullOnDelete();
            $table->timestamp('anonymization_requested_at')->nullable()->after('suspended_by');
            $table->timestamp('anonymized_at')->nullable()->after('anonymization_requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('suspended_by');
            $table->dropColumn([
                'account_status',
                'suspended_at',
                'suspension_reason',
                'anonymization_requested_at',
                'anonymized_at',
            ]);
        });
    }
};
