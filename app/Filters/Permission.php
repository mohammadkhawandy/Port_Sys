<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Permission implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('permission');

        $requiredPermission = (string) ($arguments[0] ?? '');

        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        if ($requiredPermission !== '' && ! can($requiredPermission)) {
            return redirect()->to(site_url('dashboard'))->with('error', lang('App.msg_permission_denied'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
