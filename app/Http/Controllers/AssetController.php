<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Asset;
use App\Services\AssetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

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
        $query = Asset::with('uploader')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->type, function ($query, $type) {
                $query->where('file_type', $type);
            })
            ->latest();

        $assets = $query->get();

        $assetsByCategory = $assets->groupBy('category');

        return Inertia::render('Assets/Index', [
            'assetsByCategory' => $assetsByCategory,
            'filters' => $request->only(['search', 'type']),
            'userRole' => Auth::user()->roles->pluck('name')->first(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Assets/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:102400', // 100MB max
            'description' => 'nullable|string|max:1000',
            'name' => 'required|string|max:255',
            'category' => 'required|in:media,tvc,ppt,reel',
        ]);

        $asset = $this->assetService->upload(
            $request->file('file'),
            [
                'description' => $request->description,
                'uploaded_at' => now()->toIso8601String(),
                'name' => $request->name,
                'category' => $request->category,
            ],
            auth()->id()
        );

        return redirect()->route('assets.index')
            ->with('success', 'Asset uploaded successfully.');
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

    public function update(Request $request, Asset $asset)
    {
        $this->authorize('update', $asset);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $asset->update($validated);

        return back()->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        $this->authorize('delete', $asset);

        $this->assetService->delete($asset);

        return back()->with('success', 'Asset deleted successfully.');
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
