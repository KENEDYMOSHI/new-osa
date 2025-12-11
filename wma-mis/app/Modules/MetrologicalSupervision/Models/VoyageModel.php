<?php

namespace App\Modules\MetrologicalSupervision\Models;

use CodeIgniter\Model;

class VoyageModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'metro_voyage';
    protected $primaryKey       = 'voyageId'; // VARCHAR PK
    protected $useAutoIncrement = false;      // Important for VARCHAR PK
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'voyageId',
        'voyageNumber',
        'vesselId',
        'vesselExperienceFactor',
        'loadingPort',
        'arrivalPort',
        'arrivalBerth',
        'loadingDate',
        'arrivalDate',
        'createdAt',
        'updatedAt'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
