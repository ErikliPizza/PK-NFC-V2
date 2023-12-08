<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\GeneratedSlug;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/check-slug', function (Request $request) {
    $requestedData = $request->json()->all();
    $slug = $requestedData['slug'];
    $signature = $requestedData['signature'];
    $params = ['slug' => $slug];
    if ($signature === hash_hmac('sha256', http_build_query($params), env('ADMIN_SIGNATURE_KEY'))) {
        $foundedSlug = GeneratedSlug::where('slug', $slug)->first();
        if ($foundedSlug) {
            return response()->json(['exists' => true]);
        } else {
            GeneratedSlug::create([
                'slug' => $slug
            ]);
            return response()->json(['exists' => false]);
        }
    }
    return response()->json(['error' => 'Invalid signature'], 401);
});
