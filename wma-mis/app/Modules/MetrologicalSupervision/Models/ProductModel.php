<?php

namespace App\Modules\MetrologicalSupervision\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'metro_products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $allowedFields    = ['productName'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';
}
