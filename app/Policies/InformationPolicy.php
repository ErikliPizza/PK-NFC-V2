<?php

namespace App\Policies;

use App\Models\Information;
use App\Models\User;

class InformationPolicy
{
    /**
     * Determine whether the user own the model.
     */
    public function owner(User $user, Information $information): bool
    {
        return $user->id === $information->card->user_id;
    }
}
