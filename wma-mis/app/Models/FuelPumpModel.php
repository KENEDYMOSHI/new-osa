<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class FuelPumpModel extends Model
{
  public $db;
  public $dataTable;
  public function __construct()
  {
    $this->db = \Config\Database::connect();
    $this->dataTable = $this->db->table('fuelpumps');
  }

  public function savePumpData($data)
  {

   return $this->dataTable->insert($data);
   
  }


  public function getRegisteredPumps($id)
  {
    return $this->dataTable
    ->select()
    ->select('customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number')
    // ->where(['unique_id' => $id])

      ->join('customers', 'customers.hash = fuelpumps.customer_hash')
      ->get()
      ->getResult();
  }
  // ================Get all fuel pumps based on the location==============
  public function getAllPumps($region)
  {
    return $this->dataTable
    ->select()
    ->select('customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number')
    ->where(['region' => $region])

      ->join('customers', 'customers.hash = fuelpumps.customer_hash')
      ->get()
    ->getResult();

    // return $this->dataTable
    //   ->where(['region' => $region])
    //   ->get()
    //   ->getResult();
  }
  // ================Get all details in all regions==============
  public function getAllInRegion($location)
  {
    return $this->dataTable
    ->select('amount,payment')
    ->where(['customers.region' => $location])
    ->join('customers', 'customers.hash = fuelpumps.customer_hash')
    ->get()
    ->getResultArray();
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
    ->select('customers.name,customers.gender')
      ->where(['id' => $id])
      ->join('customers', 'customers.hash = fuelpumps.customer_hash')
      ->get()
      ->getRow();
  }

  public function updatePumpData($data, $id)
  {


    return $this->dataTable
      ->set($data)
      ->where(['id' => $id])
      ->update();
  }
  public function fuelPumpDetails($location)
  {

    return $this->dataTable
    ->select('amount,payment')
    ->where(['customers.region' => $location])
    ->join('customers', 'customers.hash = fuelpumps.customer_hash')
    ->get()
    ->getResultArray();

    // return $this->dataTable
    //   ->where(['region' => $location])
    //   ->select(' amount,payment')
    //   ->get()
    //   ->getResultArray();
  }
  // ================Full details on  activity==============
  public function activityFullDetails()
  {
    return $this->dataTable
      // ->where(['region' => $location])
      ->select('amount,payment')
      ->get()
      ->getResultArray();
  }

  // ================Data for Api==============
  public function getData($region)
  {
    return $this->dataTable
      ->select('customers.region,fuelpumps.date,fuelpumps.amount,fuelpumps.payment')
      ->where(['customers.region' => $region])
      ->join('customers', 'customers.hash = fuelpumps.customer_hash')
      ->get()
      ->getResultArray();
  }
  // ================Data for Api==============
  public function getFullData()
  {
    return $this->dataTable
      ->select('customers.date,amount,payment')
      // ->where(['region' => $region])
      ->join('customers', 'customers.hash = fuelpumps.customer_hash')
      ->get()
      ->getResult();
  }

  public function search($query)
  {
    return $this->dataTable
      ->havingLike('first_name', $query)
      ->orHavingLike('last_name', $query)
      ->get()
      ->getResultArray();
  }
}