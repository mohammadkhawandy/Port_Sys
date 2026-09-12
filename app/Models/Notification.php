<?php

namespace App\Models;

use CodeIgniter\Model;

class Notification extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'type', 'title', 'message', 'related_type', 'related_id', 'is_read'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'is_read' => 'integer',
    ];
    protected array $castHandlers = [];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [
        'user_id' => ['label' => 'App.user', 'rules' => 'required|integer|is_not_unique[users.id]'],
        'type' => ['label' => 'App.type', 'rules' => 'permit_empty|max_length[40]'],
        'title' => ['label' => 'App.title', 'rules' => 'required|max_length[190]'],
        'message' => ['label' => 'App.message', 'rules' => 'permit_empty|max_length[2000]'],
        'related_type' => ['label' => 'App.type', 'rules' => 'permit_empty|max_length[60]'],
        'related_id' => ['label' => 'App.id', 'rules' => 'permit_empty|integer'],
        'is_read' => ['label' => 'App.status', 'rules' => 'permit_empty|in_list[0,1]'],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
