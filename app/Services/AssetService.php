<?php

namespace App\Services;

use App\Models\Asset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Queue;
use App\Jobs\GenerateThumbnailJob;

class AssetService
{
    protected $disk;
    protected $allowedMimeTypes = [
        'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        'video' => ['video/mp4', 'video/quicktime', 'video/x-msvideo'],
        'document' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'],
    ];

    public function __construct()
    {
        $this->disk = Storage::disk('public');
    }

    public function upload(UploadedFile $file, array $metadata = [], int $userId)
    {
        $mimeType = $file->getMimeType();
        $fileType = $this->getFileType($mimeType);

        // Generate unique filename
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = 'assets/' . date('Y/m/d') . '/' . $filename;

        // Upload to S3
        $this->disk->putFileAs(
            dirname($path),
            $file,
            basename($path),
            ['visibility' => 'public']
        );

        // Create asset record
        $asset = Asset::create([
            'name' => $file->getClientOriginalName(),
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $fileType,
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
            'description' => $metadata['description'],
            'metadata' => $metadata,
            'uploaded_by' => $userId,
            'category' => $metadata['category'],
        ]);

        // Queue thumbnail generation for images
        // if ($fileType === 'image') {
        //     Queue::push(new GenerateThumbnailJob($asset));
        // }

        return $asset;
    }

    protected function getFileType($mimeType)
    {
        foreach ($this->allowedMimeTypes as $type => $mimes) {
            if (in_array($mimeType, $mimes)) {
                return $type;
            }
        }
        return 'other';
    }

    public function generateThumbnail(Asset $asset)
    {
        // Thumbnail generation disabled
        return null;
    }

    public function delete(Asset $asset)
    {
        // Delete main file
        $this->disk->delete($asset->file_path);

        // Delete thumbnail if exists
        if ($asset->thumbnail_path) {
            $this->disk->delete($asset->thumbnail_path);
        }

        // Delete asset record
        $asset->delete();
    }

    public function getSignedUrl(Asset $asset, $expiresIn = 3600)
    {
        return $this->disk->temporaryUrl(
            $asset->file_path,
            now()->addSeconds($expiresIn)
        );
    }
}
