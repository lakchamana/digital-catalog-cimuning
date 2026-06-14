<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Support\LeadEventRecorder;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class QrCodeController extends Controller
{
    public function svg(Request $request, Umkm $umkm): Response
    {
        $this->ensurePublicUmkm($umkm);

        $targetUrl = route('qr.umkm.open', $umkm->slug);
        $result = Builder::create()
            ->writer(new SvgWriter)
            ->writerOptions([
                SvgWriter::WRITER_OPTION_EXCLUDE_XML_DECLARATION => false,
                SvgWriter::WRITER_OPTION_COMPACT => true,
            ])
            ->data($targetUrl)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(360)
            ->margin(18)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->foregroundColor(new Color(30, 41, 59))
            ->backgroundColor(new Color(255, 255, 255))
            ->build();

        $headers = [
            'Content-Type' => $result->getMimeType(),
            'Cache-Control' => 'public, max-age=3600',
        ];

        if ($request->boolean('download')) {
            $filename = Str::slug($umkm->name).'-qr-cimuning-digital-hub.svg';
            $headers['Content-Disposition'] = 'attachment; filename="'.$filename.'"';
        }

        return response($this->accessibleSvg($result->getString(), $umkm, $targetUrl), 200, $headers);
    }

    public function open(Request $request, Umkm $umkm, LeadEventRecorder $leadEventRecorder): RedirectResponse
    {
        $this->ensurePublicUmkm($umkm);

        $targetUrl = route('umkm.show', $umkm->slug);

        $leadEventRecorder->record(
            request: $request,
            umkm: $umkm,
            type: 'qr_scan',
            targetUrl: $targetUrl,
            source: 'qr_profile',
        );

        return redirect()->to($targetUrl);
    }

    private function ensurePublicUmkm(Umkm $umkm): void
    {
        abort_unless($umkm->is_active && $umkm->status === 'verified', 404);
    }

    private function accessibleSvg(string $svg, Umkm $umkm, string $targetUrl): string
    {
        $title = htmlspecialchars("QR profil {$umkm->name}", ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $description = htmlspecialchars("Scan untuk membuka {$targetUrl}", ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $metadata = "<title>{$title}</title><desc>{$description}</desc>";

        return Str::replaceFirst('><rect', '>'.$metadata.'<rect', $svg);
    }
}
