<?php

namespace App\Controllers;

use App\Libraries\ActivityLogger;
use App\Models\User;
use CodeIgniter\Exceptions\PageNotFoundException;

class Profile extends BaseController
{
    public function index()
    {
        $user = (new User())->find((int) session()->get('userId'));
        if (! $user) {
            throw PageNotFoundException::forPageNotFound(lang('App.msg_user_not_found'));
        }

        return view('profile/index', ['user' => $user]);
    }

    public function update()
    {
        $userId = (int) session()->get('userId');
        $userModel = new User();
        $user = $userModel->find($userId);
        if (! $user) {
            return redirect()->to(site_url('profile'))->with('error', lang('App.msg_user_not_found'));
        }

        $rules = [
            'full_name' => ['label' => 'App.full_name', 'rules' => 'required|min_length[2]|max_length[150]'],
            'email' => ['label' => 'App.email', 'rules' => "required|valid_email|max_length[255]|is_unique[users.email,id,{$userId}]"],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'full_name' => trim((string) $this->request->getPost('full_name')),
            'email' => mb_strtolower(trim((string) $this->request->getPost('email'))),
        ];

        if (! $userModel->update($userId, $data)) {
            return redirect()->back()->withInput()->with('errors', $userModel->errors());
        }

        session()->set([
            'userName' => $data['full_name'],
            'userEmail' => $data['email'],
        ]);

        (new ActivityLogger())->log('profile.updated', lang('App.activity_profile_updated'), 'user', $userId);
        return redirect()->to(site_url('profile'))->with('success', lang('App.msg_profile_updated'));
    }

    public function changePassword()
    {
        $userId = (int) session()->get('userId');
        $userModel = new User();
        $user = $userModel->find($userId);
        if (! $user) {
            return redirect()->to(site_url('profile'))->with('error', lang('App.msg_user_not_found'));
        }

        $rules = [
            'current_password' => ['label' => 'App.current_password', 'rules' => 'required'],
            'password' => ['label' => 'App.new_password', 'rules' => 'required|min_length[8]'],
            'confirm_password' => ['label' => 'App.auth_confirm_password', 'rules' => 'required|matches[password]'],
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('profile') . '#password-section')->with('errors', $this->validator->getErrors());
        }

        $currentPassword = (string) $this->request->getPost('current_password');
        $newPassword = (string) $this->request->getPost('password');

        if (! password_verify($currentPassword, (string) $user['password'])) {
            return redirect()->to(site_url('profile') . '#password-section')->with('error', lang('App.msg_current_password_invalid'));
        }
        if (password_verify($newPassword, (string) $user['password'])) {
            return redirect()->to(site_url('profile') . '#password-section')->with('error', lang('App.msg_same_password'));
        }

        if (! $userModel->update($userId, ['password' => $newPassword])) {
            return redirect()->to(site_url('profile') . '#password-section')->with('errors', $userModel->errors());
        }

        session()->regenerate(true);
        (new ActivityLogger())->log('profile.password_changed', lang('App.activity_password_changed'), 'user', $userId);
        return redirect()->to(site_url('profile'))->with('success', lang('App.msg_password_changed'));
    }
}
