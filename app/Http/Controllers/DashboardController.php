<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Asset;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $statistics = [
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

            $query = Activity::with('causer')->orderBy('created_at', 'desc');

            if ($request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('properties->attributes->name', 'like', '%' . $request->search . '%')
                      ->orWhere('properties->old->name', 'like', '%' . $request->search . '%');
                });
            }

            $recentActivity = $query->paginate(5)->withQueryString();

            // Get companies with asset counts
            $companies = Company::where('is_active', true)
                ->withCount('assets')
                ->orderBy('name')
                ->get();

            return Inertia::render('Dashboard', [
                'statistics' => $statistics,
                'recentActivity' => $recentActivity,
                'companies' => $companies,
                'search' => $request->search,
                'user' => Auth::user(),
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Dashboard', [
                'statistics' => [
                    'images' => 0,
                    'videos' => 0,
                    'pdfs' => 0,
                    'presentations' => 0,
                    'documents' => 0,
                    'total_size' => 0,
                ],
                'recentActivity' => [],
                'search' => null,
                'user' => Auth::user(),
                'error' => 'Failed to load dashboard data. Please try again later.'
            ]);
        }
    }

    public function store(Request $request)
    {
        // ...validate credentials...

        $user = // ...get user by credentials...

        // Check if user already has an active session
        $activeSession = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('last_activity', '>', now()->subMinutes(config('session.lifetime'))->getTimestamp())
            ->first();

        if ($activeSession) {
            return back()->withErrors([
                'email' => 'Uploader is already logged in somewhere else.'
            ]);
        }

        Auth::login($user, $remember);

        // Store user_id in session table
        DB::table('sessions')->where('id', Session::getId())->update([
            'user_id' => $user->id
        ]);

        // ...redirect...
    }
}
