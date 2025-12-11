<?php

namespace App\Modules\MetrologicalSupervision\Models;

use CodeIgniter\Model;

class BerthModel extends Model
{
    protected $table = 'metro_berths';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useTimestamps = true;
    protected $allowedFields = ['berthName', 'portId'];
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';
}
