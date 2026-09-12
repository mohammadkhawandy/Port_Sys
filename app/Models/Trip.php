<?php

namespace App\Models;

use CodeIgniter\Model;

class Trip extends Model
{
    protected $table            = 'trips';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['ship_id', 'departure_port_id', 'arrival_port_id', 'departure_date', 'arrival_date'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'ship_id' => ['label' => 'App.ship', 'rules' => 'required|integer|is_not_unique[ships.id]'],
        'departure_port_id' => ['label' => 'App.departure_port', 'rules' => 'required|integer|is_not_unique[ports.id]'],
        'arrival_port_id' => ['label' => 'App.arrival_port', 'rules' => 'required|integer|is_not_unique[ports.id]|differs[departure_port_id]'],
        'departure_date' => ['label' => 'App.departure_date', 'rules' => 'required|valid_date'],
        'arrival_date' => ['label' => 'App.arrival_date', 'rules' => 'required|valid_date'],
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
