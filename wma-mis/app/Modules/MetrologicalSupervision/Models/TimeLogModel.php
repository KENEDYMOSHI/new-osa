<?php

namespace App\Modules\MetrologicalSupervision\Models;

use CodeIgniter\Model;

class TimeLogModel extends Model
{
    protected $table            = 'metro_timeLogs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['vesselId', 'voyageId', 'logDate', 'logTime', 'eventDescription'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';
}
