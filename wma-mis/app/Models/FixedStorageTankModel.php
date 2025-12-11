<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class FixedStorageTankModel extends Model
{
  public $db;
  public $dataTable;
  public function __construct()
  {
    $this->db = \Config\Database::connect();
    $this->dataTable = $this->db->table('fixedstoragetank');
  }

  public function saveFixedStorageTankData($data)
  {

   return  $this->dataTable->insert($data);
   
  }


  public function getRegisteredFixedStorageTank($id)
  {
    return $this->dataTable
    ->select()
     ->select('customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number')
      // ->where(['unique_id' => $id])
       ->join('customers', 'customers.hash = fixedstoragetank.customer_hash')
      ->get()
      ->getResult();
  }
  public function getAllFixedStorageTank($region)
  {
    return $this->dataTable
    ->select()
    ->select('customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number')
     ->where(['region' => $region])
      ->join('customers', 'customers.hash = fixedstoragetank.customer_hash')
     ->get()
      ->getResult();
  }

  public function deleteRecord($id)
  {
    $this->dataTable
      ->where(['id' => $id])
      ->delete();
  }
  public function editRecord($id)
  {
    return $this->dataTable
    ->select()
     ->select('customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number')
      ->where(['id' => $id])
       ->join('customers', 'customers.hash = fixedstoragetank.customer_hash')
      ->get()
      ->getRow();
  }

  public function updateFixedStorageTankData($data, $id)
  {


    return $this->dataTable
      ->set($data)
      ->where(['id' => $id])
      ->update();
  }
  public function fstDetails($location)
  {
    return $this->dataTable
    ->select('amount,payment')
      ->where(['customers.region' => $location])
       ->join('customers', 'customers.hash = fixedstoragetank.customer_hash')
      ->get()
      ->getResultArray();
  }
   // ================Get all details in all regions==============
   public function getAllInRegion($location)
   {
     return $this->dataTable
     ->select('amount,payment')
     ->where(['customers.region' => $location])
      ->join('customers', 'customers.hash = fixedstoragetank.customer_hash')
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
  public function getData($city)
  {
    return $this->dataTable
      ->select('customers.region,fixedstoragetank.date,fixedstoragetank.amount,fixedstoragetank.payment')
      ->where(['customers.region' => $city])
       ->join('customers', 'customers.hash = fixedstoragetank.customer_hash')
      ->get()
      ->getResult();
  }
    // ================Data for Api==============
    public function getFullData()
    {
      return $this->dataTable
        ->select('customers.date,amount,payment')
        // ->where(['city' => $city])
        ->join('customers', 'customers.hash = fixedstoragetank.customer_hash')
        ->get()
        ->getResult();
    }
}