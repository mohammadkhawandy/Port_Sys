<?php

namespace App\Filters;

use App\Models\User;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        $userId = (int) session()->get('userId');
        if ($userId < 1) {
            session()->remove(['isLoggedIn', 'userId', 'userRole', 'userName', 'userEmail']);
            session()->regenerate(true);
            return redirect()->to(site_url('login'))->with('error', lang('App.msg_session_expired'));
        }

        try {
            $user = (new User())->find($userId);
        } catch (\Throwable $e) {
            log_message('error', 'Authentication state check failed: {message}', ['message' => $e->getMessage()]);
            session()->remove(['isLoggedIn', 'userId', 'userRole', 'userName', 'userEmail']);
            session()->regenerate(true);
            return redirect()->to(site_url('login'))->with('error', lang('App.msg_session_expired'));
        }

        if (! $user || ($user['status'] ?? 'active') !== 'active') {
            session()->remove(['isLoggedIn', 'userId', 'userRole', 'userName', 'userEmail']);
            session()->regenerate(true);
            return redirect()->to(site_url('login'))->with('error', lang('App.msg_account_inactive'));
        }

        // Keep permissions and profile information synchronized after an administrator changes the account.
        session()->set([
            'userRole' => (string) ($user['role'] ?? 'user'),
            'userName' => (string) ($user['full_name'] ?? ''),
            'userEmail' => (string) ($user['email'] ?? ''),
        ]);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No after-filter work is required.
    }
}
