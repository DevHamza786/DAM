<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Asset;
use Spatie\Activitylog\Models\Activity;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function getFileStatistics()
    {
        return [
            'images' => Asset::where('mime_type', 'like', 'image/%')->count(),
            'videos' => Asset::where('mime_type', 'like', 'video/%')->count(),
            'pdfs' => Asset::where('mime_type', 'application/pdf')->count(),
            'presentations' => Asset::where(function($query) {
                $query->where('mime_type', 'application/vnd.ms-powerpoint')
                      ->orWhere('mime_type', 'application/vnd.openxmlformats-officedocument.presentationml.presentation');
            })->count(),
            'documents' => Asset::where(function($query) {
                $query->where('mime_type', 'application/msword')
                      ->orWhere('mime_type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            })->count(),
            'total_size' => Asset::sum('file_size'),
        ];
    }

    protected function getRecentActivity()
    {
        return Activity::with('causer')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }
}
