<?php

namespace App\Modules\MetrologicalSupervision\Models;

use CodeIgniter\Model;

class VesselModel extends Model
{
    protected $table = 'metro_vessels';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useTimestamps = true;
    protected $allowedFields = ['vesselName', 'imoNumber', 'country'];
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';
}
