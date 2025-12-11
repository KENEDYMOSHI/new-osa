<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class ScaleModel extends Model
{
  public $db;
  public $dataTable;
  public $customerScale;
  public function __construct()
  {
    $this->db = \Config\Database::connect();
    $this->dataTable = $this->db->table('scaling');
    $this->customerScale = $this->db->table('customer_scale');
  }

  public function customerScales($data)
  {
   return  $this->customerScale->insert($data);
   
  }

  //=================fetch specific customer scales====================
  public function getCustomerScales($hash){

    return $this->customerScale
    ->select()
    ->where(['customer_hash'=>$hash])
    ->get()
    ->getResultArray();

  }



  public function saveScaleData($data)
  {

   return  $this->dataTable->insert($data);
   
  }


  public function getRegisteredScales($uniqueId)
  {
    return $this->dataTable
    ->select()
      ->select('customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number,customers.date')
      // ->where(['customers.unique_id' => $uniqueId])
      ->join('customers', 'customers.hash = scaling.customer_hash')
      ->join('customer_scale', 'customers.hash = customer_scale.customer_hash')
      ->get()
      ->getResultArray();
  }

  // ================fetch all scales in a particular region==============
  public function getAllScales($region)
  {
    return $this->dataTable
    ->select()
    ->select('customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number')
    ->where(['region' => $region])
    ->join('customers', 'customers.hash = scaling.customer_hash')
    ->get()
      ->getResult();
  }



  public function deleteRecord($hash)
  {
    $this->dataTable
      ->where(['hash' => $hash])
      ->delete();
  }
  public function editRecord($hash)
  {
    return $this->dataTable
      ->where(['hash' => $hash])
      ->get()
      ->getRow();
  }

  public function updateScaleData($data, $hash)
  {

    return $this->dataTable
      ->set($data)
      ->where(['hash' => $hash])
      ->update();
  }



  public function getGroupAndOfficers()
  {

    return $this->officerTable
      ->select('tasks.activity,tasks.description,tasks.the_group,tasks.region,tasks.district,tasks.ward,tasks.created_at,first_name,last_name,email,avatar')
      // ->where(['users.unique_id' => '7530d45f1b8e519ff4828e528f4c2a37'])
      ->join('users', 'users.unique_id = officers_group.officer_id')
      ->join('tasks', 'tasks.the_group = officers_group.group_name')
      ->get()
      ->getResultArray();
  }

  public function scalesDetails($region)
  {
    return $this->dataTable
      ->select('scaling.amount,scaling.payment')
      ->where(['customers.region' => $region])
      ->join('customers', 'customers.hash = scaling.customer_hash')
      ->get()
      ->getResultArray();
    // return $this->dataTable
    //   ->where(['region' => $region])
    //   ->select('amount,payment')
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
  // ================Get all details in all regions==============
  public function getAllInRegion($location)
  {

    return $this->dataTable
    ->select('scaling.amount,scaling.payment')
    ->where(['customers.region' => $location])
    ->join('customers', 'customers.hash = scaling.customer_hash')
    ->get()
    ->getResultArray();
   
  }

  // ================Api data==============
  public function getData($region)
  {
    return $this->dataTable
    ->select('scaling.amount,scaling.payment,scaling.created_at')
    ->where(['customers.region' => $region])
    ->join('customers', 'customers.hash = scaling.customer_hash')
    ->get()
    ->getResultArray();

    // return $this->dataTable
    //   ->select('region,date,amount,payment')
    //   ->where(['region' => $region])
    //   ->get()
    //   ->getResult();
  }


  // ================Data for Api==============
  public function getFullData()
  {
    return $this->dataTable
       
      ->select('customers.region,customers.date,amount,payment')
      // ->where(['region' => $region])
       ->join('customers', 'customers.hash = scaling.customer_hash')
      ->get()
      ->getResult();
  }
}