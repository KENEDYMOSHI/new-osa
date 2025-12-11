<?php

namespace App\Modules\MetrologicalSupervision\Models;

use CodeIgniter\Model;

class TerminalModel extends Model
{
    protected $table = 'metro_terminals';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useTimestamps = true;
    protected $allowedFields    = ['terminalName', 'postalAddress', 'phoneNumber', 'telephone', 'email', 'physicalAddress'];
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';
}
