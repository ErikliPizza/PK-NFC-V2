<?php

namespace App\Http\Controllers\Auth;

use App\Events\Logged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;
class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Authenticate the user
        $request->authenticate();

        // Regenerate session
        $request->session()->regenerate();

        // Trigger Logged event
        event(new Logged($request->user()));

        // Determine the redirection message based on card creation
        $cardTitle = session()->pull('card');
        $message = $cardTitle
            ? __('notifications.card_created', ['title' => $cardTitle])
            : __('notifications.logged_in', ['name' => $request->user()->name]);

        return redirect()->intended(RouteServiceProvider::HOME)->with('success', $message);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $socialiteUser = Socialite::driver('google')->user();

        // Find the user by email or create a new user if not found
        $user = User::firstOrNew(['email' => $socialiteUser->getEmail()], [
            'name' => $socialiteUser->getName(),
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make(Str::random(12)), // Generate a random password with 12 characters
        ]);

        if (!$user->exists) {
            // The user was newly created, dispatch registered event
            event(new Registered($user));
        }

        // Log in the user and set the remember token
        Auth::login($user, true);
        event(new Logged($user)); // IMPORTANT - event will be executed synchronously

        // Determine the redirection message based on card creation
        $cardTitle = session()->pull('card');

        $message = $cardTitle
            ? __('notifications.card_created', ['title' => $cardTitle])
            : __('notifications.logged_in', ['name' => $user->name]);

        return redirect()->intended(RouteServiceProvider::HOME)->with('success', $message);
    }
}
