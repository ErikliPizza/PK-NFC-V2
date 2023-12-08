<?php

namespace App\Http\Controllers;

use App\Http\Resources\CardResource;
use App\Models\Card;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Actions\CreateCardAction;
class NfcController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response|\Illuminate\Http\RedirectResponse|void
     * @param  string  $slug
     */
    public function show(Request $request, $slug)
    {
        // Attempt to find a card with the given slug
        $card = Card::where('slug', $slug)->first();

        // If a card with the slug is found, render it
        if ($card) {
            return $this->renderCard($card);
        }

        // If a signature is provided, handle card creation
        if ($request->input('signature')) {
            return $this->handleCardCreation($request, $slug);
        } else {
            // If neither a card nor a signature is found, abort with a 404
            abort(404);
        }
    }


    /**
     * Render the specified card.
     *
     * @param  \App\Models\Card  $card
     * @return \Inertia\Response
     */
    private function renderCard(Card $card)
    {
        return Inertia::render('Nfc/Show', [
            'card' => CardResource::make($card->load([
                'informations' => function ($query) {
                    $query->orderBy('order', 'asc');
                },
                'informations.app',
                'informations.app.category'
            ])),
        ]);
    }

    /**
     * Handle the creation of a card.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\RedirectResponse
     */
    private function handleCardCreation(Request $request, $slug)
    {
        $params = ['slug' => $slug];
        $signature = $request->input('signature');

        // Validate the provided signature
        if ($signature !== hash_hmac('sha256', http_build_query($params), env('SIGNATURE_KEY'))) {
            abort(403);
        }

        $user = auth()->user();

        if (!$user) {
            // If user is not authenticated, store slug and signature in session and redirect to login page
            session()->put(['slug' => $slug, 'signature' => $signature]);
            return redirect()->route('login')->with('info', __('notifications.card_redirected'));
        }

        // User is authenticated, proceed with card creation
        $createCardAction = new CreateCardAction();
        $card = $createCardAction->execute($user, __('custom.enter_username'), $slug);

        // Update the user's default_card column
        $user->default_card = $card->id;
        $user->save();

        return redirect('/card')->with('success', __('notifications.card_created', ['title' => $card->title]));
    }
}
