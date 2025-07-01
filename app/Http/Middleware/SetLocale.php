<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        $locale = $request->header('lang');
        if (in_array($locale, ['en', 'ar'])) { // Check if the locale is supported
            // Set the application locale
            app()->setLocale($locale);
        }
        return $next($request);
    }
}
