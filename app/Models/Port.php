<?php

namespace App\Models;

use CodeIgniter\Model;

class Port extends Model
{
    protected $table = 'ports';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = ['place_id', 'name', 'country', 'city', 'latitude', 'longitude'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $validationRules = [
        'place_id' => ['label' => 'App.source_location', 'rules' => 'permit_empty|is_natural_no_zero'],
        'name' => ['label' => 'App.name', 'rules' => 'required|min_length[2]|max_length[255]'],
        'country' => ['label' => 'App.country', 'rules' => 'required|min_length[2]|max_length[100]'],
        'city' => ['label' => 'App.city', 'rules' => 'required|min_length[2]|max_length[100]'],
        'latitude' => ['label' => 'App.latitude', 'rules' => 'required|numeric|greater_than_equal_to[-90]|less_than_equal_to[90]'],
        'longitude' => ['label' => 'App.longitude', 'rules' => 'required|numeric|greater_than_equal_to[-180]|less_than_equal_to[180]'],
    ];
}
