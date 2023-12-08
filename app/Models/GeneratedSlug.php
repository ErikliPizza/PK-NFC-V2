<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * This class target the API
 * See: routes/api.php <> Route::post('/check-slug')
 */
class GeneratedSlug extends Model
{
    use HasFactory;
    protected $table = 'generated_slugs'; // Specify the correct table name
    protected $fillable = ['slug'];
}
