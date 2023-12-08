<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
/**
 * Custom validation rule to ensure unique value for a card and app combination.
 */
class UniqueValueForCard implements Rule
{
    /**
     * The card ID.
     *
     * @var int
     */
    protected $cardId;

    /**
     * The app ID.
     *
     * @var int
     */
    protected $appId;

    /**
     * The information ID (optional).
     *
     * @var int|null
     */
    protected $infoId;

    /**
     * Create a new rule instance.
     *
     * @param  int  $cardId
     * @param  int  $appId
     * @param  int|null  $infoId
     * @return void
     */
    public function __construct($cardId, $appId, $infoId = null)
    {
        $this->cardId = $cardId;
        $this->appId = $appId;
        $this->infoId = $infoId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $query = DB::table('informations')
            ->where('card_id', $this->cardId)
            ->where('app_id', $this->appId)
            ->where('value', $value);

        if ($this->infoId !== null) {
            $query->where('id', '<>', $this->infoId);
        }

        return !$query->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('notifications.information_exist');
    }
}
