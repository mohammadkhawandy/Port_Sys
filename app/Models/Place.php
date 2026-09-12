<?php

namespace App\Models;

use CodeIgniter\Model;

class Place extends Model
{
    protected $table = 'places';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = [
        'code', 'name', 'name_en', 'name_ar', 'country', 'country_code',
        'country_en', 'country_ar', 'city_en', 'city_ar', 'latitude', 'longitude',
        'type', 'timezone', 'status', 'is_seeded',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $validationRules = [
        'code' => ['label' => 'App.location_code', 'rules' => 'permit_empty|max_length[12]'],
        'name' => ['label' => 'App.name', 'rules' => 'required|min_length[2]|max_length[255]'],
        'name_en' => ['label' => 'App.name_en', 'rules' => 'permit_empty|min_length[2]|max_length[255]'],
        'name_ar' => ['label' => 'App.name_ar', 'rules' => 'permit_empty|min_length[2]|max_length[255]'],
        'country' => ['label' => 'App.country', 'rules' => 'required|min_length[2]|max_length[100]'],
        'country_code' => ['label' => 'App.country_code', 'rules' => 'permit_empty|exact_length[2]|alpha'],
        'country_en' => ['label' => 'App.country_en', 'rules' => 'permit_empty|min_length[2]|max_length[100]'],
        'country_ar' => ['label' => 'App.country_ar', 'rules' => 'permit_empty|min_length[2]|max_length[100]'],
        'city_en' => ['label' => 'App.city_en', 'rules' => 'permit_empty|max_length[100]'],
        'city_ar' => ['label' => 'App.city_ar', 'rules' => 'permit_empty|max_length[100]'],
        'latitude' => ['label' => 'App.latitude', 'rules' => 'required|numeric|greater_than_equal_to[-90]|less_than_equal_to[90]'],
        'longitude' => ['label' => 'App.longitude', 'rules' => 'required|numeric|greater_than_equal_to[-180]|less_than_equal_to[180]'],
        'type' => ['label' => 'App.location_type', 'rules' => 'required|in_list[port,terminal,anchorage,city,other]'],
        'timezone' => ['label' => 'App.timezone', 'rules' => 'permit_empty|max_length[64]'],
        'status' => ['label' => 'App.status', 'rules' => 'required|in_list[active,inactive]'],
    ];
}
