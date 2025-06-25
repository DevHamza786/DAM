<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function checkAdmin()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index(Request $request)
    {
        $this->checkAdmin();

        $query = User::query()
            ->with('roles')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('designation', 'like', "%{$search}%");
                });
            })
            ->when($request->sort && $request->order, function ($query) use ($request) {
                $query->orderBy($request->sort, $request->order);
            }, function ($query) {
                $query->orderBy('name', 'asc');
            });

        return Inertia::render('Users/Index', [
            'users' => $query->paginate($request->input('perPage', 10)),
            'filters' => $request->only(['search', 'sort', 'order'])
        ]);
    }

    public function create()
    {
        $this->checkAdmin();

        return Inertia::render('Users/Create', [
            'roles' => Role::where('name', 'uploader')->get()
        ]);
    }

    public function store(Request $request)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:100',
            'is_enabled' => 'boolean',
            'roles' => 'required|array|min:1'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'designation' => $validated['designation'],
            'is_enabled' => $validated['is_enabled']
        ]);

        $user->roles()->sync($validated['roles']);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $this->checkAdmin();

        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'designation' => $user->designation,
                'is_enabled' => $user->is_enabled,
                'roles' => $user->roles->pluck('id')
            ],
            'roles' => Role::where('name', 'uploader')->get()
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:100',
            'is_enabled' => 'boolean',
            'roles' => 'required|array|min:1',
            'password' => $request->password ? ['required', 'confirmed', Rules\Password::defaults()] : ''
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'designation' => $validated['designation'],
            'is_enabled' => $validated['is_enabled']
        ]);

        if ($request->password) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->roles()->sync($validated['roles']);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->checkAdmin();

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->isAdmin()) {
            return back()->with('error', 'Admin users cannot be deleted.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
