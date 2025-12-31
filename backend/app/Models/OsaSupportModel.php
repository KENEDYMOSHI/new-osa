<?php

namespace App\Models;

use CodeIgniter\Model;

class OsaSupportModel extends Model
{
    protected $DBGroup          = 'osa'; // Use the osa connection group defined in Database.php
    protected $table            = 'osa_support_details';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'address', 
        'phone_label_1', 'phone_number_1',
        'phone_label_2', 'phone_number_2',
        'phone_label_3', 'phone_number_3',
        'email_general', 'email_tech', 'website'
    ];
}
