<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Card;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $cards = $request->user()->cards->map(function ($card) {
            return [
                'id' => $card->id,
                'title' => $card->title,
            ];
        })->toArray();
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'cards' => $cards,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Change the default card for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeDefaultCard(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            // Find the specified card
            $card = Card::findOrFail($request->input('default_card'));

            // Authorize the action for the card owner
            $this->authorize('owner', $card);

            // Get the authenticated user
            $user = auth()->user();

            // Update the default card for the user
            $user->default_card = $request->input('default_card');
            $user->save();

            // Redirect back with an info message
            return redirect()->back()->with('info', __('notifications.card_switched'));
        } catch (ModelNotFoundException $exception) {
            // Handle case where card is not found
            return redirect()->back()->with('error', __('notifications.card_not_found'));
        }
    }

}
