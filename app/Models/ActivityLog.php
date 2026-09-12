<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id', 'action', 'entity_type', 'entity_id', 'description',
        'metadata', 'ip_address', 'created_at',
    ];
    protected $useTimestamps = false;
    protected $validationRules = [
        'action' => ['label' => 'App.actions', 'rules' => 'required|max_length[60]'],
        'description' => ['label' => 'App.activity_description', 'rules' => 'required|max_length[500]'],
    ];
}
