<?php

namespace App\Policies;

use App\Models\Card;
use App\Models\User;

class CardPolicy
{
    /**
     * Determine whether the user owner of this model.
     */
    public function owner(User $user, Card $card): bool
    {
        return $user->id === $card->user_id;
    }

}
