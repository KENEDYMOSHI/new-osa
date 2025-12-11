<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationReviewModel extends Model
{
    protected $table            = 'application_reviews';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id', 'application_id', 'application_type', 'approver_id', 
        'stage', 'status', 'comments'
    ];
    protected $useTimestamps = true; // Use created_at only
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // No updated_at needed for audit log usually, but migration has created_at only? No, migration has created_at only.
    
    // Migration only has created_at. So we should disable updatedField or not use timestamps fully?
    // CI4 expects both if useTimestamps is true unless configured.
    // Migration: 'created_at' => ['type' => 'DATETIME', 'null' => true]
    // Since I didn't add updated_at in migration, I should turn off updatedField.
}
