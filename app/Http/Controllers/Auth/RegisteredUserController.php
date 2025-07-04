<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:'.User::class,
                function ($attribute, $value, $fail) {
                    $allowedDomains = [
                        'ahmedkapadia.com',
                        'brandsynariourdu.com',
                        'lkmwt.org',
                        'synchronizemedia.com',
                        'synergydentsu.com',
                        'synergyzer.com',
                        'synitedigital.com',
                        'syntaxcommunications.com',
                        'synergygroup.com.pk',
                        'synergymarketing.biz',
                        'brandsynario.com'
                    ];
                    $emailDomain = strtolower(substr(strrchr($value, '@'), 1));
                    if (!in_array($emailDomain, $allowedDomains)) {
                        $fail('You can only register using an official company email address.');
                    }
                },
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'designation' => $request->designation,
            'is_enabled' => false,
        ]);

        event(new Registered($user));

        return redirect(route('login'))->with('status', 'Your account is pending approval by an admin.');
    }
}
