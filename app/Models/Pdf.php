<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pdf extends Model
{
    use HasFactory;
    protected $table = 'pdfs';
    protected $fillable = ['title', 'path', 'card_id'];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }
}
