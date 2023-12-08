<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class BillingInformation extends Model
{
    use HasFactory;
    protected $table = 'billing_information';
    protected $fillable = ['card_id', 't_ü', 'a', 't_s_n', 'v_d', 'v_n', 'm_n', 'email', 'k_a'];


    public function card()
    {
        return $this->belongsTo(Card::class);
    }
}
