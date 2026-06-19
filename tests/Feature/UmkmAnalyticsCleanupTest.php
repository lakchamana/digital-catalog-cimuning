<?php

namespace Tests\Feature;

use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UmkmAnalyticsCleanupTest extends TestCase
{
    use RefreshDatabase;

    public function test_view_count_column_is_removed(): void
    {
        $this->assertFalse(Schema::hasColumn('umkms', 'view_count'));
        $this->assertNotContains('view_count', (new Umkm)->getFillable());
    }

    public function test_database_seeder_runs_after_view_count_removal(): void
    {
        $this->seed();

        $this->assertDatabaseHas('umkms', [
            'slug' => 'dapur-ibu-sari',
            'status' => 'verified',
        ]);
        $this->assertFalse(Schema::hasColumn('umkms', 'view_count'));
    }
}
