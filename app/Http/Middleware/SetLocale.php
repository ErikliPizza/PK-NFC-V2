<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
class SetLocale
{
    /**
     * Handle an incoming request.
     *
     */
    public function handle(Request $request, Closure $next)
    {
        $userPreferredLanguage = Cache::remember('user_language_' . $request->ip(), now()->addDay(), function () use ($request) {
            return $request->getPreferredLanguage(
                Config::get('app.available_locales')
            );
        });

        App::setLocale($userPreferredLanguage);

        return $next($request);
    }

}
