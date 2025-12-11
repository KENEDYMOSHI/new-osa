<?php

namespace App\Models;

use CodeIgniter\Model;

class Test extends Model
{
    public $db;
    public $usersTable;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->usersTable = $this->db->table('x1');
    }

    public function addData($data)
    {

        return $this->usersTable->ignore()->insert($data);

    }

    public function readUsers()
    {
        // return $this->usersTable->select()->get()->getResult();
        return $this->usersTable
            ->select()
            ->orderBy('id', 'DESC')
            ->get()
            ->getResult();



    }

    
}