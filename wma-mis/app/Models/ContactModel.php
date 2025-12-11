<?php

namespace App\Models;

use CodeIgniter\Model;

class ContactModel extends Model
{
    public $db;
    public $usersTable;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->usersTable = $this->db->table('contact_form');
    }

    public function saveData($data)
    {
     return  $this->usersTable->insert($data);
    }
    public function getData()
    {
     return  $this->usersTable->select()
     ->orderBy('name','ASC')
     ->get()
     ->getResult();
    }
    public function getRecord($id)
    {
     return  $this->usersTable->select()
     ->where('id',$id)
     ->get()
     ->getRow();
    }
    public function updateData($hash,$data)
    {
     return  $this->usersTable
     ->set($data)
     ->where('hash',$hash)
     ->update();
    }


    
}
