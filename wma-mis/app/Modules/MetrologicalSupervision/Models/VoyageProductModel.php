<?php

namespace App\Modules\MetrologicalSupervision\Models;

use CodeIgniter\Model;

class VoyageProductModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'metro_voyageProducts';
    protected $primaryKey       = 'voyageProductId'; // VARCHAR PK
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'voyageProductId',
        'voyageId',
        'productId',
        'loadPortDensityAtFifteen',
        'loadPortWCFTAtFifteen',
        'loadPortDensityAtTwenty',
        'loadPortWCFTAtTwenty',
        'tbsDensityAtFifteen',
        'tbsWCFTAtFifteen',
        'tbsDensityAtTwenty',
        'tbsWCFTAtTwenty',
        'primaryLine',
        'secondaryLine',
        'billOfLading',
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
