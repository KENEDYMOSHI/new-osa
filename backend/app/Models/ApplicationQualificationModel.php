<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationQualificationModel extends Model
{
    protected $table            = 'application_qualifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id', 'license_application_id', 'institution', 'award', 'year'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
