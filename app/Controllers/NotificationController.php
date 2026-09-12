<?php

namespace App\Controllers;

use App\Models\Notification;

class NotificationController extends BaseController
{
    public function index()
    {
        $userId = (int) session()->get('userId');
        $status = trim((string) $this->request->getGet('status'));
        $model = new Notification();
        $builder = $model->where('user_id', $userId);

        if ($status === 'unread') {
            $builder->where('is_read', 0);
        } elseif ($status === 'read') {
            $builder->where('is_read', 1);
        }

        try {
            $notifications = $builder->orderBy('created_at', 'DESC')->paginate(20);
            $unreadCount = (new Notification())->where('user_id', $userId)->where('is_read', 0)->countAllResults();
            $pager = $model->pager;
        } catch (\Throwable $e) {
            log_message('error', 'Notifications page unavailable: {message}', ['message' => $e->getMessage()]);
            $notifications = [];
            $unreadCount = 0;
            $pager = null;
        }

        return view('notifications/index', [
            'notifications' => $notifications,
            'pager' => $pager,
            'filterStatus' => $status,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function feed()
    {
        $userId = (int) session()->get('userId');

        try {
            $items = (new Notification())
                ->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->findAll(8);

            $unread = (new Notification())
                ->where('user_id', $userId)
                ->where('is_read', 0)
                ->countAllResults();
        } catch (\Throwable $e) {
            log_message('error', 'Notification feed unavailable: {message}', ['message' => $e->getMessage()]);
            $items = [];
            $unread = 0;
        }

        return $this->response
            ->setHeader('Cache-Control', 'no-store, private')
            ->setJSON([
                'unread' => $unread,
                'items' => array_map(static function (array $item): array {
                    return [
                        'id' => (int) ($item['id'] ?? 0),
                        'type' => (string) ($item['type'] ?? 'general'),
                        'title' => (string) ($item['title'] ?? ''),
                        'message' => (string) ($item['message'] ?? ''),
                        'is_read' => (int) ($item['is_read'] ?? 0),
                        'time' => human_time($item['created_at'] ?? null),
                        'url' => site_url('notifications/read/' . (int) ($item['id'] ?? 0)),
                        'icon' => notification_icon($item['type'] ?? null),
                    ];
                }, $items),
            ]);
    }

    public function markRead(int $id)
    {
        $model = new Notification();
        $userId = (int) session()->get('userId');
        $notification = $model->where('id', $id)->where('user_id', $userId)->first();

        if (! $notification) {
            return redirect()->to(site_url('notifications'))->with('error', lang('App.msg_notification_not_found'));
        }

        if ((int) ($notification['is_read'] ?? 0) === 0 && ! $model->update($id, ['is_read' => 1])) {
            return redirect()->to(site_url('notifications'))->with('error', lang('App.msg_operation_failed'));
        }

        return redirect()->to(notification_target_url($notification));
    }

    public function markAllRead()
    {
        $userId = (int) session()->get('userId');
        $updated = (new Notification())
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->set(['is_read' => 1, 'updated_at' => date('Y-m-d H:i:s')])
            ->update();

        if (! $updated) {
            return redirect()->back()->with('error', lang('App.msg_operation_failed'));
        }

        return redirect()->back()->with('success', lang('App.msg_notifications_read'));
    }

    public function clearRead()
    {
        $userId = (int) session()->get('userId');
        $deleted = (new Notification())
            ->where('user_id', $userId)
            ->where('is_read', 1)
            ->delete();

        if (! $deleted) {
            return redirect()->to(site_url('notifications'))->with('error', lang('App.msg_operation_failed'));
        }

        return redirect()->to(site_url('notifications'))->with('success', lang('App.msg_notifications_cleared'));
    }
}
