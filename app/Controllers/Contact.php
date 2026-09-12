<?php

namespace App\Controllers;

use App\Libraries\ActivityLogger;
use App\Models\ContactMessage;
use App\Models\Notification;
use App\Models\User;
use CodeIgniter\Exceptions\PageNotFoundException;

class Contact extends BaseController
{
    public function create()
    {
        return view('pages/contact');
    }

    public function store()
    {
        // Hidden field for automated bots. Return a generic success response so the trap is not disclosed.
        if (trim((string) $this->request->getPost('website')) !== '') {
            return redirect()->to(site_url('contact'))->with('success', lang('App.contact_spam_success'));
        }

        $lastSentAt = (int) session()->get('contact_last_sent_at');
        if ($lastSentAt > 0 && (time() - $lastSentAt) < 30) {
            return redirect()->back()->withInput()->with('error', lang('App.contact_rate_limited'));
        }

        $model = new ContactMessage();
        $data = [
            'name' => trim((string) $this->request->getPost('name')),
            'email' => mb_strtolower(trim((string) $this->request->getPost('email'))),
            'subject' => trim((string) $this->request->getPost('subject')),
            'message' => trim((string) $this->request->getPost('message')),
            'is_read' => 0,
        ];

        if (! $model->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        $messageId = (int) $model->getInsertID();
        session()->set('contact_last_sent_at', time());

        try {
            $admins = (new User())->select('id')->where('role', 'admin')->where('status', 'active')->findAll();
            $notificationModel = new Notification();
            foreach ($admins as $admin) {
                if (! $notificationModel->insert([
                    'user_id' => (int) $admin['id'],
                    'type' => 'contact_message',
                    'title' => lang('App.notification_contact_message_title'),
                    'message' => sprintf(lang('App.notification_contact_message_body'), $data['name']),
                    'related_type' => 'contact_message',
                    'related_id' => $messageId,
                    'is_read' => 0,
                ])) {
                    log_message('warning', 'Contact notification could not be created for message {id}.', ['id' => $messageId]);
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Contact notifications unavailable: {message}', ['message' => $e->getMessage()]);
        }

        (new ActivityLogger())->log('message.received', lang('App.activity_message_received'), 'contact_message', $messageId);
        return redirect()->to(site_url('contact'))->with('success', lang('App.msg_contact_sent'));
    }

    public function index()
    {
        $status = trim((string) $this->request->getGet('status'));
        $q = trim((string) $this->request->getGet('q'));
        $model = new ContactMessage();
        $builder = $model;

        if ($status === 'unread') {
            $builder->where('is_read', 0);
        } elseif ($status === 'read') {
            $builder->where('is_read', 1);
        }

        if ($q !== '') {
            $builder->groupStart()
                ->like('name', $q)
                ->orLike('email', $q)
                ->orLike('subject', $q)
                ->groupEnd();
        }

        $messages = $builder->orderBy('created_at', 'DESC')->paginate(20);

        return view('messages/index', [
            'messages' => $messages,
            'pager' => $model->pager,
            'filters' => ['status' => $status, 'q' => $q],
            'stats' => [
                'total' => (new ContactMessage())->countAllResults(),
                'unread' => (new ContactMessage())->where('is_read', 0)->countAllResults(),
            ],
        ]);
    }

    public function show(int $id)
    {
        $model = new ContactMessage();
        $message = $model->find($id);
        if (! $message) {
            throw PageNotFoundException::forPageNotFound(lang('App.msg_message_not_found'));
        }

        if ((int) ($message['is_read'] ?? 0) === 0) {
            $model->update($id, ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')]);
            $message['is_read'] = 1;
            $message['read_at'] = date('Y-m-d H:i:s');
        }

        return view('messages/show', ['messageItem' => $message]);
    }

    public function markRead(int $id)
    {
        $model = new ContactMessage();
        if (! $model->find($id)) {
            return redirect()->to(site_url('messages'))->with('error', lang('App.msg_message_not_found'));
        }

        if (! $model->update($id, ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')])) {
            return redirect()->back()->with('error', lang('App.msg_operation_failed'));
        }
        return redirect()->back()->with('success', lang('App.msg_message_marked_read'));
    }

    public function delete(int $id)
    {
        $model = new ContactMessage();
        if (! $model->find($id)) {
            return redirect()->to(site_url('messages'))->with('error', lang('App.msg_message_not_found'));
        }

        if (! $model->delete($id)) {
            return redirect()->back()->with('error', lang('App.msg_operation_failed'));
        }
        (new ActivityLogger())->log('message.deleted', lang('App.activity_message_deleted'), 'contact_message', $id);
        return redirect()->to(site_url('messages'))->with('success', lang('App.msg_message_deleted'));
    }
}
