<?php

namespace App\Events;

use App\Actions\CreateCardAction;
use App\Models\Card;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Logged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    /**
     * Create a new event instance.
     */
    public function __construct(User $user)    {
        // Store the authenticated user
        $this->user = $user;

        // Retrieve and remove slug and signature from session
        $slug = session()->pull('slug');
        $signature = session()->pull('signature');

        // Check if both slug and signature are present
        if ($slug && $signature) {
            // Validate the signature
            $params = ['slug' => $slug];
            $isValidSignature = $signature === hash_hmac('sha256', http_build_query($params), env('SIGNATURE_KEY'));

            if ($isValidSignature) {
                // Check if a card with the given slug exists
                $card = Card::where('slug', $slug)->first();

                if (!$card) {
                    // If card doesn't exist, create a new one
                    $createCardAction = new CreateCardAction();
                    $card = $createCardAction->execute($user, __('custom.enter_username'), $slug);
                    $user->default_card = $card->id;
                    $user->save();
                    session()->put('card', $card->title);
                }
            }
        }
    }
}
