<?php

namespace App\Models;

use CodeIgniter\Model;

class Ship extends Model
{
    protected $table = 'ships';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = ['name', 'type', 'capacity'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $validationRules = [
        'name' => ['label' => 'App.name', 'rules' => 'required|min_length[2]|max_length[255]'],
        'type' => ['label' => 'App.type', 'rules' => 'required|min_length[2]|max_length[100]'],
        'capacity' => ['label' => 'App.capacity', 'rules' => 'required|integer|greater_than[0]|less_than_equal_to[999999999]'],
    ];
}
