<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Cache;
class LanguageController extends Controller
{
    /**
     * Change the language and update user preferences.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeLanguage(Request $request)
    {
        $locale = $request->input('language');
        if (in_array($locale, config('app.available_locales'))) {
            App::setLocale($locale);

            // Update the cached user language preference
            Cache::put('user_language_' . $request->ip(), $locale, now()->addDay());

            // Redirect back
            return Redirect::back();
        }

        // Redirect back with an error message if the selected language is not supported
        return Redirect::back()->with('error', 'Selected language is not supported.');
    }
}
