<?php

namespace App\Models;

use CodeIgniter\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = ['full_name', 'email', 'password', 'role', 'status', 'last_login_at'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'full_name' => ['label' => 'App.full_name', 'rules' => 'permit_empty|min_length[2]|max_length[150]'],
        'email' => ['label' => 'App.email', 'rules' => 'required|valid_email|max_length[255]'],
        'password' => ['label' => 'App.auth_password', 'rules' => 'permit_empty|min_length[8]'],
        'role' => ['label' => 'App.role', 'rules' => 'permit_empty|in_list[admin,user]'],
        'status' => ['label' => 'App.status', 'rules' => 'permit_empty|in_list[active,inactive]'],
    ];

    protected $allowCallbacks = true;
    protected $beforeInsert = ['hashPassword', 'normalizeEmail'];
    protected $beforeUpdate = ['hashPassword', 'normalizeEmail'];

    protected function hashPassword(array $data): array
    {
        if (! isset($data['data']['password']) || $data['data']['password'] === '') {
            unset($data['data']['password']);
            return $data;
        }

        $password = (string) $data['data']['password'];
        $info = password_get_info($password);
        if (($info['algoName'] ?? 'unknown') === 'unknown') {
            $data['data']['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        return $data;
    }

    protected function normalizeEmail(array $data): array
    {
        if (isset($data['data']['email'])) {
            $data['data']['email'] = mb_strtolower(trim((string) $data['data']['email']));
        }

        return $data;
    }
}
