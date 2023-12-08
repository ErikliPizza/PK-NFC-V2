<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = 'images';

    protected $fillable = [
        'card_id',
        'filename',
        'order'
    ];
    use HasFactory;
    public function card()
    {
        return $this->belongsTo(Card::class);
    }
}
