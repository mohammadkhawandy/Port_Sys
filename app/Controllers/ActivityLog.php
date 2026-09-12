<?php

namespace App\Controllers;

use App\Models\ActivityLog as ActivityLogModel;

class ActivityLog extends BaseController
{
    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $action = trim((string) $this->request->getGet('action'));

        $model = new ActivityLogModel();
        $builder = $model
            ->select('activity_logs.*, users.email AS user_email, users.full_name AS user_name')
            ->join('users', 'users.id = activity_logs.user_id', 'left');

        if ($q !== '') {
            $builder->groupStart()
                ->like('activity_logs.description', $q)
                ->orLike('users.email', $q)
                ->orLike('users.full_name', $q)
                ->groupEnd();
        }
        if ($action !== '') {
            $builder->where('activity_logs.action', $action);
        }

        $logs = $builder->orderBy('activity_logs.created_at', 'DESC')->paginate(25);
        $actions = (new ActivityLogModel())->select('action')->groupBy('action')->orderBy('action')->findAll();

        return view('activity_log/index', [
            'logs' => $logs,
            'pager' => $model->pager,
            'filters' => ['q' => $q, 'action' => $action],
            'actions' => array_column($actions, 'action'),
        ]);
    }
}
