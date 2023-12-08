<?php
namespace App\Actions;

use App\Models\User;
use App\Models\Card;

class CreateCardAction
{
    /**
     * Execute the action to create a card for the specified user.
     *
     * @param  \App\Models\User  $user
     * @param  string  $title
     * @param  string  $slug
     * @return \App\Models\Card
     */
    public function execute(User $user, $title, $slug)
    {
        // Create a new card for the specified user
        $card = Card::create([
            'user_id' => $user->id,
            'title' => $title,
            'slug' => $slug
        ]);

        // Update the user's default card
        $user->default_card = $card->id;
        $user->save();

        // Return the newly created card
        return $card;
    }

}
