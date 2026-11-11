<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    private const ALLOWED_LOCALES = ['pt_PT', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = null;

        if ($request->hasHeader('X-Locale')) {
            $headerLocale = (string) $request->header('X-Locale');
            if (in_array($headerLocale, self::ALLOWED_LOCALES, true)) {
                $locale = $headerLocale;
            }
        }

        if ($locale === null && $request->hasSession()) {
            $sessionLocale = (string) $request->session()->get('locale');
            if (in_array($sessionLocale, self::ALLOWED_LOCALES, true)) {
                $locale = $sessionLocale;
            }
        }

        if ($locale === null) {
            $locale = (string) config('app.locale', 'pt_PT');
            if (!in_array($locale, self::ALLOWED_LOCALES, true)) {
                $locale = 'pt_PT';
            }
        }

        App::setLocale($locale);

        if ($request->hasSession()) {
            $request->session()->put('locale', $locale);
        }

        $response = $next($request);

        $response->headers->set('Content-Language', str_replace('_', '-', $locale));

        return $response;
    }
}