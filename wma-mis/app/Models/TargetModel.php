<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class TargetModel extends Model
{
    public $db;

    public $targetTable;


    public function __construct()
    {
        $this->db = \Config\Database::connect();

        $this->targetTable = $this->db->table('activity_targets');
    }

    public function addTarget($data)
    {

        return $this->targetTable->insert($data);
    }


    public function getTargets($params)
    {  
        //switching array keys to match table column
        if(isset($params['users.collection_center'])){
            $params['region'] = $params['users.collection_center']; 
            unset($params['users.collection_center']);

        }
        return $this->targetTable->select()
        ->where($params)
        ->get()
        ->getResult();
    }





}
