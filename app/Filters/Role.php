<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Role implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $requiredRole = (string) ($arguments[0] ?? 'admin');

        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        $currentRole = (string) (session()->get('userRole') ?? 'user');

        if ($requiredRole !== '' && $currentRole !== $requiredRole) {
            return redirect()->to(site_url('dashboard'))->with('error', lang('App.msg_admin_only'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}