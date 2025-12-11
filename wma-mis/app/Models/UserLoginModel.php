<?php

namespace App\Models;

use CodeIgniter\Model;



class UserLoginModel extends Model
{
  public $db;
  public $dataTable;
  public function __construct()
  {
    $this->db = \Config\Database::connect();
    $this->dataTable = $this->db->table('users');
  }

  public function verifyEmail($email)
  {
    $builder = $this->dataTable;
    $builder->select();
    $builder->where('email', $email);
    $result =  $builder->get();

    if (count($result->getResultArray()) == 1) {
      return $result->getRowArray();
    }
  }

  public function getLoggedUser($id)
  {
    return $this->dataTable->select()->where(['unique_id' => $id])->get()->getRow();
  }
}
