<?php

namespace App\Models;

use CodeIgniter\Model;

class CertificateModel extends Model
{
  protected $conformityTable;
  protected $correctnessTable;
  protected $db;
  protected $users;
  protected $user;

  protected $signatureTable;


  public function __construct()
  {
    $this->db = \Config\Database::connect();
    $this->conformityTable = $this->db->table('conformity_certificate');
    $this->correctnessTable = $this->db->table('correctness_certificate');
    $this->users = $this->db->table('users');
    $this->signatureTable = $this->db->table('sign');
    $this->user = auth()->user();
  }


  public function addConformityCertificate($data)
  {
    return $this->conformityTable->insert($data);
  }
  public function addSignatureData($data)
  {
    return $this->signatureTable->insert($data);
  }
  public function getSignPath($userId)
  {
    return $this->signatureTable->select('file')->where('userId', $userId)->get()->getRow();
  }
  public function findUser($params)
  {
    return $this->users->select('users.id,username,collection_center,unique_id')->where($params)
      ->join('auth_groups_users', 'auth_groups_users.user_id = users.id')
      ->get()->getRow();
  }

  public function findConformityCertificates($params, $name,$limit, $offset)
  {
    $query = $this->conformityTable->select()->where($params)->limit($limit, $offset);
    if ($name) {
      $query->like('customer', $name);
    }
    return $query->get()->getResult();
  }



  public function fetchConformityCertificate($params)
  {
    $query = $this->conformityTable->select()->where($params);
    return $query->get()->getRow();
  }



  public function getLastConformityCertificate($params)
  {
    return $this->conformityTable
      ->select()
      ->where($params)
      ->orderBy('id', 'DESC')
      ->limit(1)
      ->get()
      ->getRow();
  }


  // ================CERTIFICATE OF CORRECTNESS==============

  public function addCorrectnessCertificate($data)
  {
    return $this->correctnessTable->insert($data);
  }


  public function findCorrectnessCertificates($params, $name, $limit, $offset)
  {
    $query = $this->correctnessTable->select()->where($params)->limit($limit, $offset);
    if ($name) {
      $query->like('customer', $name);
    }
    return $query->get()->getResult();
  }

  public function countCorrectnessCertificates()
  {
    
    $query = $this->correctnessTable->select();
    if($this->user->inGroup('officer', 'manager', 'accountant')){
      $query->where(['region' => $this->user->collection_center]);

    }
    return $query->countAllResults();
  }
  public function countConformityCertificates()
  {
    
    $query = $this->conformityTable->select();
    if($this->user->inGroup('officer', 'manager', 'accountant')){
      $query->where(['region' => $this->user->collection_center]);

    }
    return $query->countAllResults();
  }




  public function fetchCorrectnessCertificate($params)
  {
    $query = $this->correctnessTable->select()->where($params);
    return $query->get()->getRow();
  }



  public function getLastCorrectnessCertificate($params)
  {
    return $this->correctnessTable
      ->select()
      ->where($params)
      ->orderBy('id', 'DESC')
      ->limit(1)
      ->get()
      ->getRow();
  }

  public function isPaid($params)
  {
    return   $this->db->table('bill_payment')->select('PayCtrNum')->where($params)->get()->getRow();
  }

  public function getItems($params)
  {
    return $this->db->table('bill_items')->select('ItemName,BillItemAmt as amount')->where($params)->get()->getResult();
  }

  public function region($centerNUmber)
  {
    return $this->db->table('collectioncenter')->select('centerName')->where('centerNumber', $centerNUmber)->get()->getRow();
  }

  public function officer($id)
  {
    return $this->db->table('users')->select('username')->where('unique_id', $id)->get()->getRow()->username;
  }
}
