<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationToolModel extends Model
{
    protected $table            = 'application_tools';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id', 'license_application_id', 'name', 'serial_number', 'capacity'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
