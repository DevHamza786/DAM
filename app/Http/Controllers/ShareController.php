<?php

namespace App\Http\Controllers;

use App\Models\ShareableLink;
use App\Services\AssetService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ShareController extends Controller
{
    protected $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }

    public function show($token)
    {
        $shareableLink = ShareableLink::where('token', $token)
            ->with('asset')
            ->firstOrFail();

        if (!$shareableLink->isValid()) {
            abort(403, 'This share link has expired or reached its view limit.');
        }

        // Record the view
        $shareableLink->views()->create([
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referer' => request()->header('referer'),
        ]);

        $shareableLink->increment('view_count');

        return Inertia::render('Share/Show', [
            'asset' => $shareableLink->asset,
            'signedUrl' => $this->assetService->getSignedUrl($shareableLink->asset),
            'shareableLink' => $shareableLink,
        ]);
    }

    public function qrCode($token)
    {
        $shareableLink = ShareableLink::where('token', $token)
            ->with('asset')
            ->firstOrFail();

        if (!$shareableLink->isValid()) {
            abort(403, 'This share link has expired or reached its view limit.');
        }

        $qrCode = QrCode::format('png')
            ->size(300)
            ->generate(route('share.asset', $token));

        return response($qrCode)
            ->header('Content-Type', 'image/png');
    }
} 