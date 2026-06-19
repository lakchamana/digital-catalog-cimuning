<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('umkm_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('umkm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 20)->default('initial');
            $table->string('status', 30)->default('pending');
            $table->json('payload');
            $table->text('review_notes')->nullable();
            $table->json('review_checklist')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'submitted_at']);
            $table->index(['umkm_id', 'status']);
        });

        $fields = [
            'category_id', 'name', 'description', 'owner_name', 'phone', 'whatsapp', 'email',
            'address', 'rw', 'latitude', 'longitude', 'instagram', 'tiktok', 'website',
            'cover_image', 'logo_image', 'service_delivery', 'service_cod',
            'service_custom_order', 'has_physical_store',
        ];

        DB::table('umkms')
            ->whereIn('status', ['pending', 'need_revision', 'rejected'])
            ->orderBy('id')
            ->each(function (object $umkm) use ($fields): void {
                $payload = [];

                foreach ($fields as $field) {
                    $payload[$field] = $umkm->{$field} ?? null;
                }

                DB::table('umkm_submissions')->insert([
                    'umkm_id' => $umkm->id,
                    'submitted_by' => $umkm->user_id,
                    'reviewed_by' => null,
                    'type' => 'initial',
                    'status' => $umkm->status,
                    'payload' => json_encode($payload, JSON_THROW_ON_ERROR),
                    'review_notes' => null,
                    'review_checklist' => null,
                    'submitted_at' => $umkm->updated_at ?? $umkm->created_at,
                    'reviewed_at' => $umkm->status === 'pending' ? null : ($umkm->updated_at ?? null),
                    'created_at' => $umkm->created_at,
                    'updated_at' => $umkm->updated_at,
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkm_submissions');
    }
};
