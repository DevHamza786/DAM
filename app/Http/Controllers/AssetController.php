<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Asset;
use App\Models\Company;
use App\Services\AssetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Jobs\GenerateThumbnailJob;

class AssetController extends Controller
{
    use AuthorizesRequests;

    protected $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Asset::class);

        $query = Asset::with('company')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->type, function ($query, $type) {
                $query->where('file_type', $type);
            })
            ->when($request->company_id, function ($query, $companyId) {
                $query->where('company_id', $companyId);
            })
            ->latest();

        $assets = $query->paginate(12);

        $assetsByCategory = $assets->groupBy('category');

        return Inertia::render('Assets/Index', [
            'assets' => $assets,
            'assetsByCategory' => $assetsByCategory,
            'filters' => $request->only(['search', 'type', 'company_id']),
            'userRole' => Auth::user()->roles->pluck('name')->first(),
            'companies' => Company::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name'])
        ]);
    }

    public function create()
    {
        $this->authorize('create', Asset::class);

        return Inertia::render('Assets/Create', [
            'companies' => Company::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name'])
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Asset::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:1048576',
            'category' => 'required|string',
            'company_id' => 'required|exists:companies,id'
        ]);

        $file = $request->file('file');
        $path = $file->store('assets', 'public');

        $asset = Asset::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_type' => $this->determineFileType($file->getMimeType()),
            'file_size' => $file->getSize(),
            'category' => $validated['category'],
            'company_id' => $validated['company_id'],
            'user_id' => auth()->id(),
            'uploaded_by' => auth()->id()
        ]);

        GenerateThumbnailJob::dispatch($asset);

        return redirect()->route('assets.index')
            ->with('message', 'Asset created successfully.');
    }

    private function determineFileType($mimeType)
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }
        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }
        if ($mimeType === 'application/pdf') {
            return 'pdf';
        }
        if (in_array($mimeType, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])) {
            return 'document';
        }
        if (in_array($mimeType, [
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ])) {
            return 'presentation';
        }
        return 'other';
    }

    public function show(Asset $asset)
    {
        $this->authorize('view', $asset);

        $asset->load(['uploader', 'shareableLinks' => function ($query) {
            $query->latest()->take(5);
        }]);

        return Inertia::render('Assets/Show', [
            'asset' => $asset,
            'signedUrl' => $this->assetService->getSignedUrl($asset),
        ]);
    }

    public function edit(Asset $asset)
    {
        $this->authorize('update', $asset);

        return Inertia::render('Assets/Edit', [
            'asset' => $asset,
            'companies' => Company::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name'])
        ]);
    }

    public function update(Request $request, Asset $asset)
    {
        $this->authorize('update', $asset);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'company_id' => 'required|exists:companies,id'
        ]);

        $asset->update($validated);

        return back()->with('message', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        $this->authorize('delete', $asset);

        if ($asset->file_path) {
            Storage::disk('public')->delete($asset->file_path);
        }

        $asset->delete();

        return back()->with('message', 'Asset deleted successfully.');
    }

    public function download(Asset $asset)
    {
        $this->authorize('download', $asset);

        return $this->assetService->getSignedUrl($asset);
    }

    public function createShareableLink(Request $request, Asset $asset)
    {
        $this->authorize('share', $asset);

        $validated = $request->validate([
            'expires_at' => 'required|date|after:now',
            'max_views' => 'nullable|integer|min:1',
        ]);

        $link = $asset->shareableLinks()->create([
            'created_by' => auth()->id(),
            'expires_at' => $validated['expires_at'],
            'max_views' => $validated['max_views'],
        ]);

        return back()->with('success', 'Shareable link created successfully.')
            ->with('shareableLink', $link);
    }
}
