<?php

namespace App\Modules\MetrologicalSupervision\Models;

use CodeIgniter\Model;

class PressureLogModel extends Model
{
    protected $table            = 'metro_pressureLogs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['vesselId', 'voyageId', 'productId', 'logDate', 'logTime', 'pressure', 'rate'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';
}
