<?php

namespace App\Modules\MetrologicalSupervision\Models;

use CodeIgniter\Model;

class VesselTankModel extends Model
{
    protected $table            = 'metro_vesselTanks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $allowedFields    = ['vesselId', 'tankName'];
    protected $useTimestamps    = true;
    protected $createdField     = 'createdAt';
    protected $updatedField     = 'updatedAt';
}
