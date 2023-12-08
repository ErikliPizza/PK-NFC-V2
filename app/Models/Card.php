<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Card extends Model
{
    use HasFactory;

    // Fillable attributes for mass assignment
    protected $fillable = ['title', 'slug', 'image', 'user_id', 'latitude', 'longitude', 'address', 'billing_info', 'bio'];

    /**
     * Get the user associated with the card.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the informations associated with the card.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function informations()
    {
        return $this->hasMany(Information::class);
    }
    public function images()
    {
        return $this->hasMany(Image::class)->orderBy('order');
    }
    public function pdfs()
    {
        return $this->hasMany(Pdf::class);
    }

    public function billingInformation()
    {
        return $this->hasOne(BillingInformation::class, 'card_id', 'id');    }

    /**
     * Boot function to handle events.
     */
    public static function boot()
    {
        parent::boot();

        // Event listener for when a card is being deleted
        static::deleting(function ($card) {
            // If the deleted card is the default card, update the user's default_card field
            if ($card->user->default_card === $card->id) {
                $newDefaultCard = $card->user->cards->where('id', '<>', $card->id)->first();
                if ($newDefaultCard) {
                    $card->user->default_card = $newDefaultCard->id;
                    $card->user->save();
                } else {
                    // If no other card is available, set default_card to NULL
                    $card->user->default_card = null;
                    $card->user->save();
                }
            }
        });
    }
}
