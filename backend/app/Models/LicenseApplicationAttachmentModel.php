<?php

namespace App\Models;

use CodeIgniter\Model;

class LicenseApplicationAttachmentModel extends Model
{
    protected $table            = 'license_application_attachments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'user_id', 'application_id', 'document_type', 'file_path', 'original_name', 'file_content', 'mime_type', 'status', 'rejection_reason'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
}
