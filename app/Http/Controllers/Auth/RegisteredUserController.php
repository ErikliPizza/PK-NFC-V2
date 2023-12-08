<?php

namespace App\Http\Controllers\Auth;

use App\Events\Logged;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
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
            'email' => 'required|string|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Trigger Registered event
        event(new Registered($user));

        // Log in the user
        Auth::login($user);

        // Trigger Logged event
        event(new Logged($user));

        // Determine the redirection message based on card creation
        $cardTitle = session()->pull('card');
        $message = $cardTitle
            ? __('notifications.card_created', ['title' => $cardTitle])
            : __('notifications.logged_in', ['name' => $user->name]);

        return redirect()->intended(RouteServiceProvider::HOME)->with('success', $message);
    }
}
