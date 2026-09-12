<?php

namespace App\Controllers;

class Language extends BaseController
{
    public function switch(string $locale = 'en')
    {
        $appConfig = config('App');
        $supported = (array) $appConfig->supportedLocales;
        if (! in_array($locale, $supported, true)) {
            $locale = (string) $appConfig->defaultLocale;
        }

        session()->set('locale', $locale);
        service('request')->setLocale($locale);
        service('language')->setLocale($locale);

        $fallback = session()->get('isLoggedIn') ? site_url('dashboard') : site_url('/');
        $return = trim((string) $this->request->getGet('return'));
        $target = $this->safeLocalTarget($return, $fallback);

        return redirect()->to($target)->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    private function safeLocalTarget(string $return, string $fallback): string
    {
        if ($return === '' || preg_match('/[\r\n]/', $return) === 1) {
            return $fallback;
        }

        $decoded = rawurldecode($return);
        if (preg_match('/[\r\n]/', $decoded) === 1) {
            return $fallback;
        }

        $parts = parse_url($decoded);
        if ($parts === false || isset($parts['scheme']) || isset($parts['host'])) {
            return $fallback;
        }

        $path = trim((string) ($parts['path'] ?? ''), '/');
        if (str_contains($path, '..')) {
            return $fallback;
        }

        $appConfig = config('App');
        $basePath = trim((string) parse_url(site_url('/'), PHP_URL_PATH), '/');
        if ($basePath !== '' && ($path === $basePath || str_starts_with($path, $basePath . '/'))) {
            $path = ltrim(substr($path, strlen($basePath)), '/');
        }

        $indexPage = trim((string) $appConfig->indexPage, '/');
        if ($indexPage !== '' && ($path === $indexPage || str_starts_with($path, $indexPage . '/'))) {
            $path = ltrim(substr($path, strlen($indexPage)), '/');
        }

        if ($path === 'lang/ar' || $path === 'lang/en' || str_starts_with($path, 'lang/')) {
            return $fallback;
        }

        $target = $path === '' ? site_url('/') : site_url($path);
        $query = trim((string) ($parts['query'] ?? ''));
        if ($query !== '') {
            $target .= '?' . $query;
        }

        return $target;
    }
}
