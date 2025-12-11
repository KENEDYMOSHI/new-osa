<?php

namespace App\Models;

use CodeIgniter\Model;

class LicenseApplicationItemModel extends Model
{
    protected $table            = 'license_application_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'application_id', 'license_type', 'fee', 'application_type', 'status', 'approval_stage'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = false;
}
