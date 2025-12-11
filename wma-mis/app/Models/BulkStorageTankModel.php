<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class BulkStorageTankModel extends Model
{
  public $db;
  public $dataTable;
  public function __construct()
  {
    $this->db = \Config\Database::connect();
    $this->dataTable = $this->db->table('bulkStorageTank');
  }

  public function saveBulkStorageTankData($data)
  {

    return $this->dataTable->insert($data);
   
  }


  public function getRegisteredBulkStorageTank($id)
  {
    return $this->dataTable
    ->select()
     ->select('customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number')
      // ->where(['unique_id' => $id])
       ->join('customers', 'customers.hash = bulkStorageTank.customer_hash')
      ->get()
      ->getResult();
  }
  public function getAllBulkStorageTank($region)
  {
    return $this->dataTable
    ->select()
     ->select('customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number')
      ->where(['region' => $region])
       ->join('customers', 'customers.hash = bulkStorageTank.customer_hash')
      ->get()
      ->getResult();
  }
  // ================Get all details in all regions==============
  public function getAllInRegion($location)
  {
    return $this->dataTable
    ->select('amount,payment')
    ->where(['customers.region' => $location])
     ->join('customers', 'customers.hash = bulkStorageTank.customer_hash')
    ->get()
    ->getResultArray();
  }

  public function deleteRecord($hash)
  {
    $this->dataTable
      ->where(['hash' => $hash])
      ->delete();
  }
  public function editRecord($id)
  {
    return $this->dataTable
    ->select()
     ->select('customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number')
      ->where(['id' => $id])
       ->join('customers', 'customers.hash = bulkStorageTank.customer_hash')
      ->get()
      ->getRow();
  }

  public function updateBulkStorageTankData($data, $id)
  {


    return $this->dataTable
      ->set($data)
      ->where(['id' => $id])
      ->update();
  }

  public function bstDetails($location)
  {
    return $this->dataTable
    ->select('amount,payment')
      ->where(['customers.region' => $location])
       ->join('customers', 'customers.hash = bulkStorageTank.customer_hash')
      ->get()
      ->getResultArray();
  }
  // ================Full details on  activity==============
  public function activityFullDetails()
  {
    return $this->dataTable
      // ->where(['city' => $location])
      ->select('amount,payment')
      ->get()
      ->getResultArray();
  }
  // ================Api==============
  public function getData($region)
  {
    return $this->dataTable
      ->select('customers.region,bulkStorageTank.date,bulkStorageTank.amount,bulkStorageTank.payment')
       ->where(['customers.region' => $region])
        ->join('customers', 'customers.hash = bulkStorageTank.customer_hash')
      ->get()
      ->getResult();
  }
  // ================Data for Api==============
  public function getFullData()
  {
    return $this->dataTable
      ->select('customers.date,amount,payment')
      ->join('customers', 'customers.hash = bulkStorageTank.customer_hash')
      ->get()
      ->getResult();
  }
}