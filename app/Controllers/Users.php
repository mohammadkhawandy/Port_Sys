<?php

namespace App\Controllers;

use App\Libraries\ActivityLogger;
use App\Models\Notification;
use App\Models\User;
use CodeIgniter\Exceptions\PageNotFoundException;

class Users extends BaseController
{
    public function index()
    {
        $filters = [
            'q' => trim((string) $this->request->getGet('q')),
            'role' => trim((string) $this->request->getGet('role')),
            'status' => trim((string) $this->request->getGet('status')),
        ];

        $model = new User();
        $builder = $model;

        if ($filters['q'] !== '') {
            $builder->groupStart()
                ->like('full_name', $filters['q'])
                ->orLike('email', $filters['q'])
                ->groupEnd();
        }
        if (in_array($filters['role'], ['admin', 'user'], true)) {
            $builder->where('role', $filters['role']);
        }
        if (in_array($filters['status'], ['active', 'inactive'], true)) {
            $builder->where('status', $filters['status']);
        }

        return view('users/index', [
            'users' => $builder->orderBy('created_at', 'DESC')->paginate(20),
            'pager' => $model->pager,
            'filters' => $filters,
            'stats' => [
                'total' => (new User())->countAllResults(),
                'admins' => (new User())->where('role', 'admin')->countAllResults(),
                'active' => (new User())->where('status', 'active')->countAllResults(),
                'inactive' => (new User())->where('status', 'inactive')->countAllResults(),
            ],
        ]);
    }

    public function updateRole(int $id)
    {
        $role = trim((string) $this->request->getPost('role'));
        if (! in_array($role, ['admin', 'user'], true)) {
            return redirect()->to(site_url('users'))->with('error', lang('App.msg_invalid_user_role'));
        }

        $model = new User();
        $user = $model->find($id);
        if (! $user) {
            throw PageNotFoundException::forPageNotFound(lang('App.msg_user_not_found'));
        }

        $currentUserId = (int) session()->get('userId');
        if ($id === $currentUserId && $role !== 'admin') {
            return redirect()->to(site_url('users'))->with('error', lang('App.msg_cannot_demote_self'));
        }

        if (($user['role'] ?? 'user') === 'admin' && ($user['status'] ?? 'active') === 'active' && $role !== 'admin' && $this->activeAdminCount() <= 1) {
            return redirect()->to(site_url('users'))->with('error', lang('App.msg_last_admin_protected'));
        }

        if (($user['role'] ?? 'user') === $role) {
            return redirect()->to(site_url('users'))->with('success', lang('App.msg_no_changes'));
        }

        if (! $model->update($id, ['role' => $role])) {
            return redirect()->to(site_url('users'))->with('errors', $model->errors());
        }

        $label = $role === 'admin' ? lang('App.role_admin') : lang('App.role_user');
        (new ActivityLogger())->log('user.role_updated', sprintf(lang('App.activity_user_role_updated'), (string) ($user['email'] ?? ''), $label), 'user', $id);
        $this->notifyUser($id, lang('App.notification_role_changed_title'), sprintf(lang('App.notification_role_changed_body'), $label));

        return redirect()->to(site_url('users'))->with('success', lang('App.msg_user_role_updated'));
    }

    public function updateStatus(int $id)
    {
        $status = trim((string) $this->request->getPost('status'));
        if (! in_array($status, ['active', 'inactive'], true)) {
            return redirect()->to(site_url('users'))->with('error', lang('App.msg_invalid_user_status'));
        }

        $model = new User();
        $user = $model->find($id);
        if (! $user) {
            throw PageNotFoundException::forPageNotFound(lang('App.msg_user_not_found'));
        }

        $currentUserId = (int) session()->get('userId');
        if ($id === $currentUserId && $status !== 'active') {
            return redirect()->to(site_url('users'))->with('error', lang('App.msg_cannot_disable_self'));
        }

        if (($user['status'] ?? 'active') === $status) {
            return redirect()->to(site_url('users'))->with('success', lang('App.msg_no_changes'));
        }

        if (($user['role'] ?? 'user') === 'admin' && ($user['status'] ?? 'active') === 'active' && $status === 'inactive' && $this->activeAdminCount() <= 1) {
            return redirect()->to(site_url('users'))->with('error', lang('App.msg_last_admin_protected'));
        }

        if (! $model->update($id, ['status' => $status])) {
            return redirect()->to(site_url('users'))->with('errors', $model->errors());
        }

        $label = $status === 'active' ? lang('App.user_status_active') : lang('App.user_status_inactive');
        (new ActivityLogger())->log('user.status_updated', sprintf(lang('App.activity_user_status_updated'), (string) ($user['email'] ?? ''), $label), 'user', $id);
        if ($status === 'active') {
            $this->notifyUser($id, lang('App.notification_account_status_title'), sprintf(lang('App.notification_account_status_body'), $label));
        }

        return redirect()->to(site_url('users'))->with('success', lang('App.msg_user_status_updated'));
    }

    private function activeAdminCount(): int
    {
        return (new User())
            ->where('role', 'admin')
            ->where('status', 'active')
            ->countAllResults();
    }

    private function notifyUser(int $userId, string $title, string $message): void
    {
        try {
            $notification = new Notification();
            $inserted = $notification->insert([
                'user_id' => $userId,
                'type' => 'security',
                'title' => $title,
                'message' => $message,
                'related_type' => 'user',
                'related_id' => $userId,
                'is_read' => 0,
            ]);

            if ($inserted === false) {
                log_message('warning', 'User notification validation failed: {errors}', [
                    'errors' => json_encode($notification->errors(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]);
            }
        } catch (\Throwable $e) {
            log_message('debug', 'User notification could not be created: {message}', ['message' => $e->getMessage()]);
        }
    }
}
