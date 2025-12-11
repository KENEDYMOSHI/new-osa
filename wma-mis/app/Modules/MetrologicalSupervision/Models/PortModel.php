<?php

namespace App\Modules\MetrologicalSupervision\Models;

use CodeIgniter\Model;

class PortModel extends Model
{
    protected $table = 'metro_port';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useTimestamps = true;
    protected $allowedFields = ['portName'];
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';
}
