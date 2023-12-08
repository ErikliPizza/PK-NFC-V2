<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Information extends Model
{
    use HasFactory;

    // Specify the correct table name
    protected $table = 'informations';

    // Fillable attributes for mass assignment
    protected $fillable = ['title', 'value', 'card_id', 'app_id', 'order', 'extra'];


    /**
     * Get the card associated with the information.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function card()
    {
        return $this->belongsTo(Card::class);
    }


    /**
     * Get the app associated with the information.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function app()
    {
        return $this->belongsTo(App::class);
    }
}
