<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class PersonalDetailsModel extends Model
{
    public $db;
    public $customersTable;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->customersTable = $this->db->table('customers');;
    }

    public function registerCustomer($data)
    {


       return $this->customersTable->insert($data);
       
    }
    public function getLastCustomer()
    {


       return $this->customersTable->select()->orderBy('id','DESC')->limit(1)->get()->getRow();
       
    }

    public function findMatch()
    {

        return $this->customersTable
            ->select()
            ->get()
            ->getResultArray();
    }
    public function customerDetails($hash)
    {

        return $this->customersTable
            ->where(['hash' => $hash])
            ->get()
            ->getRow();
    }
    public function updateCustomer($hash,$customer)
    {

        return $this->customersTable
            ->set($customer)
            ->where(['hash' => $hash])
            ->update();

            // return $this->lorriesTable
            // ->set($data)
            // ->where(['id' => $id])
            // ->update();
    }
}