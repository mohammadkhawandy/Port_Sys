<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Locale implements FilterInterface
{
    private string $resolvedLocale = 'ar';

    public function before(RequestInterface $request, $arguments = null)
    {
        $appConfig = config('App');
        $supported = (array) $appConfig->supportedLocales;
        $defaultLocale = (string) $appConfig->defaultLocale;
        $sessionLocale = session()->get('locale');

        $locale = is_string($sessionLocale) && in_array($sessionLocale, $supported, true)
            ? $sessionLocale
            : $defaultLocale;

        $this->resolvedLocale = $locale;
        $request->setLocale($locale);
        service('language')->setLocale($locale);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // The selected locale is stored in the session. Prevent browser/proxy
        // caches from serving an Arabic response after switching to English,
        // or the opposite.
        $response->setHeader('Content-Language', $this->resolvedLocale);
        $response->setHeader('Vary', 'Cookie, Accept-Language');

        if (session()->get('isLoggedIn')) {
            $response->setHeader('Cache-Control', 'private, no-store, no-cache, must-revalidate');
            $response->setHeader('Pragma', 'no-cache');
        } else {
            $response->setHeader('Cache-Control', 'private, no-cache, must-revalidate');
        }
    }
}
