<?php

namespace App\Models;

use CodeIgniter\Model;

class TripRequest extends Model
{
    protected $table            = 'trip_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'trip_id', 'request_type', 'people_count', 'contact_name', 'contact_phone', 'contact_email', 'organization', 'message', 'status'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [
        'user_id' => ['label' => 'App.user', 'rules' => 'required|integer|is_not_unique[users.id]'],
        'trip_id' => ['label' => 'App.trip', 'rules' => 'required|integer|is_not_unique[trips.id]'],
        'request_type' => ['label' => 'App.request_type', 'rules' => 'required|in_list[booking,participation]'],
        'people_count' => ['label' => 'App.people_count', 'rules' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[9999]'],
        'contact_name' => ['label' => 'App.request_contact_name', 'rules' => 'required|min_length[2]|max_length[150]'],
        'contact_phone' => ['label' => 'App.request_contact_phone', 'rules' => 'required|max_length[50]|regex_match[/^[0-9+\\-\\s().]{6,50}$/]'],
        'contact_email' => ['label' => 'App.request_contact_email', 'rules' => 'required|valid_email|max_length[255]'],
        'organization' => ['label' => 'App.organization', 'rules' => 'permit_empty|max_length[150]'],
        'message' => ['label' => 'App.message', 'rules' => 'permit_empty|max_length[1000]'],
        'status' => ['label' => 'App.status', 'rules' => 'permit_empty|in_list[pending,approved,rejected,cancelled]'],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
