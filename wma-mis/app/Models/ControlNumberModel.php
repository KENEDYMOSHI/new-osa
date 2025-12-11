<?php

namespace App\Models;

use CodeIgniter\Model;

class ControlNumberModel extends Model
{
    public $db;
   
    public $transactionTable;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        
        $this->transactionTable = $this->db->table('transactions');
    }

    public function getControlNumbers(){
        return $this->transactionTable
          ->select('users.first_name as fName,users.last_name as lName')
            ->select('customers.first_name,customers.last_name,customers.phone_number,sandy_lorries_records.vehicle_brand,sandy_lorries_records.plate_number,sandy_lorries_records.capacity,sandy_lorries_records.amount as vehicle_amount')
            ->select('instrument_id,transactions.payment,sandy_lorries_records.amount,control_number,transactions.created_on')

         
            ->orderBy('created_on', 'ASC')
            ->join('customers', 'customers.hash = transactions.customer_hash')
            ->join('users', 'users.unique_id = transactions.unique_id')
            ->join('sandy_lorries_records', 'sandy_lorries_records.id = transactions.instrument_id')
            ->get()
            ->getResult();
    }
}
