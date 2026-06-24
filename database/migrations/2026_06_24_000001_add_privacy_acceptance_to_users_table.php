<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'privacy_accepted_at')) {
                $table->timestamp('privacy_accepted_at')->nullable()->after('remember_token');
            }

            if (! Schema::hasColumn('users', 'privacy_version')) {
                $table->string('privacy_version')->nullable()->after('privacy_accepted_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'privacy_version')) {
                $table->dropColumn('privacy_version');
            }

            if (Schema::hasColumn('users', 'privacy_accepted_at')) {
                $table->dropColumn('privacy_accepted_at');
            }
        });
    }
};
