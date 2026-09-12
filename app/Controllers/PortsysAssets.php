<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

class PortsysAssets extends Controller
{
    public function favicon(): ResponseInterface
    {
        return $this->show('favicon.ico');
    }

    /**
     * Serves the small set of assets bundled inside app/Resources.
     * This keeps the graduation project operational even when a CDN is unavailable.
     */
    public function show(string $name): ResponseInterface
    {
        $name = basename(rawurldecode($name));
        $path = APPPATH . 'Resources/portsys/' . $name;

        if ($name === '' || ! is_file($path)) {
            return $this->response->setStatusCode(404)->setBody('Asset not found');
        }

        // If a corrupted form action or a direct browser navigation reaches an
        // asset route as a page request, do not show the logo/image as the page.
        // Send the user back into the app instead. Normal <img>, <link>, CSS and
        // JS requests keep receiving the asset normally.
        if ($this->isDocumentNavigation()) {
            return redirect()->to(site_url(session()->get('isLoggedIn') ? 'dashboard' : 'login'));
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'webmanifest' => 'application/manifest+json; charset=UTF-8',
            'css' => 'text/css; charset=UTF-8',
            'js' => 'application/javascript; charset=UTF-8',
            'json' => 'application/json; charset=UTF-8',
            default => 'application/octet-stream',
        };

        $modified = (int) filemtime($path);
        $etag = '"' . sha1($name . ':' . $modified . ':' . filesize($path)) . '"';
        $requestEtag = trim((string) $this->request->getHeaderLine('If-None-Match'));

        if ($requestEtag !== '' && hash_equals($etag, $requestEtag)) {
            return $this->response
                ->setStatusCode(304)
                ->setHeader('ETag', $etag)
                ->setHeader('Cache-Control', 'public, max-age=2592000, immutable');
        }

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Length', (string) filesize($path))
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('ETag', $etag)
            ->setHeader('Last-Modified', gmdate('D, d M Y H:i:s', $modified) . ' GMT')
            ->setHeader('Cache-Control', 'public, max-age=2592000, immutable')
            ->setBody((string) file_get_contents($path));
    }

    public function misdirected(string $name = ''): ResponseInterface
    {
        return redirect()->to(site_url(session()->get('isLoggedIn') ? 'dashboard' : 'login'));
    }

    private function isDocumentNavigation(): bool
    {
        if (strtolower((string) $this->request->getMethod()) !== 'get') {
            return true;
        }

        $destination = strtolower((string) $this->request->getHeaderLine('Sec-Fetch-Dest'));
        if ($destination === 'document') {
            return true;
        }

        $accept = strtolower((string) $this->request->getHeaderLine('Accept'));

        return str_contains($accept, 'text/html');
    }

}
