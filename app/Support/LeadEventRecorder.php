<?php

namespace App\Support;

use App\Models\LeadEvent;
use App\Models\Product;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LeadEventRecorder
{
    public function record(Request $request, Umkm $umkm, string $type, string $targetUrl, ?Product $product = null, ?string $source = null): LeadEvent
    {
        return LeadEvent::query()->create([
            'umkm_id' => $umkm->id,
            'product_id' => $product?->id,
            'type' => $type,
            'source' => $source,
            'target_url' => $targetUrl,
            'ip_hash' => $this->ipHash($request),
            'user_agent' => Str::limit((string) $request->userAgent(), 500, ''),
            'referer' => Str::limit((string) $request->headers->get('referer'), 1000, ''),
        ]);
    }

    private function ipHash(Request $request): ?string
    {
        if (! $request->ip()) {
            return null;
        }

        return hash('sha256', config('app.key').'|'.$request->ip());
    }
}
