<?php

namespace App\Services;

use App\Models\Card;
use Illuminate\Support\Facades\Validator;
use App\Rules\UniqueValueForCard;

class CardImportService
{
    /**
     * Import information from one card to another, ensuring uniqueness based on app ID.
     *
     * @param  \App\Models\Card  $currentCard
     * @param  \App\Models\Card  $otherCard
     * @return array
     */
    public function importInformation(Card $currentCard, Card $otherCard)
    {
        $skipped = 0;
        $created = 0;

        $otherInformations = $otherCard->informations;

        foreach ($otherInformations as $otherInformation) {
            // Validate if the value is unique for the current card and app
            $validator = Validator::make(
                ['value' => $otherInformation->value],
                ['value' => [new UniqueValueForCard($currentCard->id, $otherInformation->app_id)]]
            );

            if (!$validator->fails()) {
                // Create a new information record in the current card
                $currentCard->informations()->create([
                    'app_id' => $otherInformation->app_id,
                    'value' => $otherInformation->value,
                    'title' => $otherInformation->title,
                    'card_id' => $currentCard->id,
                    'order' => $otherInformation->order
                ]);
                $created++;
            } else {
                // Skip if the value is not unique
                $skipped++;
            }
        }

        return [$skipped, $created];
    }
}
