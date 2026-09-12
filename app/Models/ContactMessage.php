<?php

namespace App\Models;

use CodeIgniter\Model;

class ContactMessage extends Model
{
    protected $table = 'contact_messages';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['name', 'email', 'subject', 'message', 'is_read', 'read_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $validationRules = [
        'name' => ['label' => 'App.name', 'rules' => 'required|min_length[2]|max_length[150]'],
        'email' => ['label' => 'App.email', 'rules' => 'required|valid_email|max_length[190]'],
        'subject' => ['label' => 'App.subject', 'rules' => 'required|min_length[3]|max_length[190]'],
        'message' => ['label' => 'App.message', 'rules' => 'required|min_length[10]|max_length[5000]'],
        'is_read' => ['label' => 'App.status', 'rules' => 'permit_empty|in_list[0,1]'],
    ];
}
