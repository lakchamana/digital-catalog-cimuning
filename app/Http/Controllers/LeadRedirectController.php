<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Umkm;
use App\Support\LeadEventRecorder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LeadRedirectController extends Controller
{
    public function __invoke(Request $request, Umkm $umkm, string $type, LeadEventRecorder $leadEventRecorder): RedirectResponse
    {
        abort_unless(in_array($type, ['whatsapp', 'maps'], true), 404);
        abort_unless($umkm->is_active && $umkm->status === 'verified', 404);

        $product = $this->product($request, $umkm);
        $targetUrl = $this->targetUrl($umkm, $type, $product);

        abort_unless($targetUrl, 404);

        $leadEventRecorder->record($request, $umkm, $type, $targetUrl, $product, $this->source($request));

        return redirect()->away($targetUrl);
    }

    protected function product(Request $request, Umkm $umkm): ?Product
    {
        if (! $request->filled('product')) {
            return null;
        }

        return Product::query()
            ->where('id', $request->integer('product'))
            ->where('umkm_id', $umkm->id)
            ->where('is_active', true)
            ->firstOrFail();
    }

    protected function targetUrl(Umkm $umkm, string $type, ?Product $product): ?string
    {
        if ($type === 'whatsapp') {
            if (! $umkm->whatsapp_url) {
                return null;
            }

            $message = $product
                ? "Halo, saya ingin bertanya tentang {$product->name}."
                : "Halo, saya melihat profil {$umkm->name} di Cimuning Digital Hub.";

            return $umkm->whatsapp_url.'?text='.urlencode($message);
        }

        $query = filled($umkm->latitude) && filled($umkm->longitude)
            ? "{$umkm->latitude},{$umkm->longitude}"
            : $umkm->address;

        return $query
            ? 'https://www.google.com/maps/search/?api=1&query='.urlencode($query)
            : null;
    }

    protected function source(Request $request): ?string
    {
        $source = $request->query('source');

        return in_array($source, ['detail', 'card', 'product_card', 'sticky', 'maps_section'], true)
            ? $source
            : null;
    }
}
