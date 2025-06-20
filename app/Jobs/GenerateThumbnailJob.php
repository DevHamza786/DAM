<?php

namespace App\Jobs;

use App\Models\Asset;
use App\Services\AssetService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateThumbnailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $asset;

    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
    }

    public function handle(AssetService $assetService)
    {
        try {
            $assetService->generateThumbnail($this->asset);
        } catch (\Exception $e) {
            Log::error('Thumbnail generation failed for asset: ' . $this->asset->id, [
                'error' => $e->getMessage(),
                'asset_id' => $this->asset->id
            ]);
            
            // You might want to notify the admin or retry the job
            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Thumbnail generation job failed', [
            'asset_id' => $this->asset->id,
            'error' => $exception->getMessage()
        ]);
    }
} 