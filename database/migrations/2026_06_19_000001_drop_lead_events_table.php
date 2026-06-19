<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('lead_events');
    }

    public function down(): void
    {
        // Lead tracking was intentionally removed and must not be restored on rollback.
    }
};
