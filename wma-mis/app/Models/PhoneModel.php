<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class PhoneModel extends Model
{
    public $db;
    public $dataTable;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->dataTable = $this->db->table('phone');
    }

    public function saveData($data)
    {

       return $this->dataTable->insert($data);
        
    }
    public function getData()
    {

       return $this->dataTable->select()->get()->getResult();
        
    }
}