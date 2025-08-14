<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetUserLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($userLocale = $request->user()?->locale) {
            App::setLocale($userLocale);

            return $next($request);
        }

        if ($sessionLocale = $request->session()->get('locale')) {
            if (in_array($sessionLocale, $this->getSupportedLocales())) {
                App::setLocale($sessionLocale);

                return $next($request);
            }
        }

        $this->setLocaleFromBrowser($request);

        return $next($request);
    }

    /**
     * Set locale based on browser language preferences
     */
    private function setLocaleFromBrowser(Request $request): void
    {
        $browserLocales = $request->getLanguages();
        $supportedLocales = $this->getSupportedLocales();

        foreach ($browserLocales as $browserLocale) {
            $normalizedLocale = str_replace('_', '-', strtolower($browserLocale));

            if (in_array($normalizedLocale, $supportedLocales)) {
                App::setLocale($normalizedLocale);

                return;
            }

            $languageCode = strtolower(substr($browserLocale, 0, 2));

            if (in_array($languageCode, $supportedLocales)) {
                App::setLocale($languageCode);

                return;
            }

            foreach ($supportedLocales as $supportedLocale) {
                if (str_starts_with($supportedLocale, $languageCode.'-')) {
                    App::setLocale($supportedLocale);

                    return;
                }
            }
        }

        App::setLocale(config('app.locale'));
    }

    /**
     * Get supported locales from config
     */
    private function getSupportedLocales(): array
    {
        return config('app.available_locales');
    }
}
