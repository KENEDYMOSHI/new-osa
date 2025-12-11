<?php

namespace App\Modules\MetrologicalSupervision\Models;

use CodeIgniter\Model;

class DocumentModel extends Model
{
    protected $table = 'metro_documents';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useTimestamps = true;
    protected $allowedFields = ['documentName'];
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';
}
