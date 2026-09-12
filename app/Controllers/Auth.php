<?php

namespace App\Controllers;

use App\Libraries\ActivityLogger;
use App\Models\User;

class Auth extends BaseController
{
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCK_SECONDS = 300;

    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(site_url('dashboard'));
        }

        return view('auth/login', [
            'lockRemaining' => $this->loginLockRemaining(),
        ]);
    }

    public function register()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(site_url('dashboard'));
        }

        return view('auth/register');
    }

    public function registerUser()
    {
        $rules = [
            'full_name' => ['label' => 'App.full_name', 'rules' => 'required|min_length[2]|max_length[150]'],
            'email' => ['label' => 'App.email', 'rules' => 'required|valid_email|max_length[255]|is_unique[users.email]'],
            'password' => ['label' => 'App.auth_password', 'rules' => 'required|min_length[8]'],
            'confirm_password' => ['label' => 'App.auth_confirm_password', 'rules' => 'required|matches[password]'],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new User();
        $userId = $userModel->insert([
            'full_name' => trim((string) $this->request->getPost('full_name')),
            'email' => mb_strtolower(trim((string) $this->request->getPost('email'))),
            'password' => (string) $this->request->getPost('password'),
            'role' => 'user',
            'status' => 'active',
        ], true);

        if (! $userId) {
            return redirect()->back()->withInput()->with('errors', $userModel->errors());
        }

        (new ActivityLogger())->log('auth.registered', lang('App.activity_account_created'), 'user', (int) $userId);
        return redirect()->to(site_url('login'))->with('success', lang('App.msg_account_created'));
    }

    public function authenticate()
    {
        $remaining = $this->loginLockRemaining();
        if ($remaining > 0) {
            return redirect()->back()->withInput()->with('error', sprintf(lang('App.msg_login_locked'), (int) ceil($remaining / 60)));
        }

        $rules = [
            'email' => ['label' => 'App.email', 'rules' => 'required|valid_email'],
            'password' => ['label' => 'App.auth_password', 'rules' => 'required'],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = mb_strtolower(trim((string) $this->request->getPost('email')));
        $userModel = new User();
        $user = $userModel->where('email', $email)->first();

        if (! $user || ! password_verify((string) $this->request->getPost('password'), (string) $user['password'])) {
            $this->recordFailedLogin();
            return redirect()->back()->withInput()->with('error', lang('App.msg_invalid_credentials'));
        }

        if (($user['status'] ?? 'active') !== 'active') {
            return redirect()->back()->withInput()->with('error', lang('App.msg_account_inactive'));
        }

        if (password_needs_rehash((string) $user['password'], PASSWORD_DEFAULT)) {
            try {
                $userModel->update((int) $user['id'], ['password' => (string) $this->request->getPost('password')]);
            } catch (\Throwable $e) {
                log_message('debug', 'Could not rehash password: {message}', ['message' => $e->getMessage()]);
            }
        }

        session()->regenerate(true);
        session()->remove(['login_attempts', 'login_lock_until']);
        session()->set([
            'isLoggedIn' => true,
            'userId' => (int) $user['id'],
            'userName' => (string) ($user['full_name'] ?? ''),
            'userEmail' => (string) $user['email'],
            'userRole' => (string) ($user['role'] ?? 'user'),
        ]);

        try {
            $userModel->update((int) $user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
        } catch (\Throwable $e) {
            log_message('debug', 'Could not update last login: {message}', ['message' => $e->getMessage()]);
        }

        (new ActivityLogger())->log('auth.login', lang('App.activity_login'), 'user', (int) $user['id']);
        return redirect()->to(site_url('dashboard'));
    }

    public function logout()
    {
        if (session()->get('isLoggedIn')) {
            (new ActivityLogger())->log('auth.logout', lang('App.activity_logout'), 'user', (int) session()->get('userId'));
        }

        $locale = (string) (session()->get('locale') ?: config('App')->defaultLocale);
        session()->remove([
            'isLoggedIn', 'userId', 'userName', 'userEmail', 'userRole',
            'login_attempts', 'login_lock_until',
        ]);
        session()->regenerate(true);
        session()->set('locale', $locale);

        return redirect()->to(site_url('login'));
    }

    private function recordFailedLogin(): void
    {
        $attempts = (int) session()->get('login_attempts') + 1;
        session()->set('login_attempts', $attempts);

        if ($attempts >= self::MAX_LOGIN_ATTEMPTS) {
            session()->set([
                'login_attempts' => 0,
                'login_lock_until' => time() + self::LOCK_SECONDS,
            ]);
        }
    }

    private function loginLockRemaining(): int
    {
        $until = (int) session()->get('login_lock_until');
        if ($until <= time()) {
            session()->remove('login_lock_until');
            return 0;
        }

        return $until - time();
    }
}
