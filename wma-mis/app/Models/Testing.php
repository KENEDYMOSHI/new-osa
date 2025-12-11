<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class Testing extends Model
{
  public $db;
  public $dataTable;
  public $lorriesTable;
  public $transactionTable;
  public $alpine;
  public function __construct()
  {
    $this->db = \Config\Database::connect();
    $this->dataTable = $this->db->table('drivers');
    $this->lorriesTable = $this->db->table('sandy_lorries');
    $this->transactionTable = $this->db->table('transactions');
    $this->alpine = $this->db->table('alpine');
  }

  public function saveData($data)
  {
    $this->alpine->insert($data);
  }

  public function getAll()
  {
    $activity = 'vtv';
    $tableToJoin = '';

    $items = '';
    $id = '';



    $builder = $this->db->table('transactions');

    switch ($activity) {
      case 'prepackage':
        $tableToJoin .= "prepackage";
        $id .= 'product_id';
        $items .= 'commodity,quantity,unit';
        $builder->join('product_details', "product_details.id = transactions.instrument_id");
        break;

      case 'vtv':
        $tableToJoin .= 'oilvehicles';
        $id .= 'id';
        // $items .= "vehicle_brand,' ',plate_number,' ',capacity";
        $items .= "vehicle_brand,plate_number,CONCAT(capacity, ' ','Liters')";
        break;

      default:
        # code...
        break;
    }

    // $d = date('Y-m-d');
    $builder->join($tableToJoin, "$tableToJoin.$id = transactions.instrument_id");
    $builder->join('customers', "customers.hash = transactions.customer_hash");
    // // $builder->where('payment','pending');
    $where = "DATE(transactions.created_on) = CURDATE()";

    // $builder->where(['DATE(created_on)'=> $d]);
    $builder->where($where);
    // $builder->orderBy('transactions.id','DESC');
    // $builder->select();
    $builder->select(
      "
      instrument_id,
      $tableToJoin.hash,
      transactions.amount as total,
      name,phone_number,
      control_number,
      payment,
      $tableToJoin.amount,
      transactions.created_on,
      CONCAT_WS(' ',$items) as item
      "
    );
    // $builder->select();
    // $builder->groupBy('control_number');


    return $builder->get()->getResult();
    // return $builder->getCompiledSelect();
  }












  public function searchBy()
  {
    $activity = 'prepackage';
    $keyword = 'GSM ';
    $phone = '';
    $controlNumber = '';
    $tableToJoin = '';
    $date = '2022-08-10';
    $id = '';

    $items = '';



    $builder = $this->db->table('transactions');

    switch ($activity) {
      case 'prepackage':
        $id .= 'product_id';
        $tableToJoin .= 'prepackage';
        $items .= 'commodity,quantity,unit';
        $builder->join('product_details', "product_details.id = transactions.instrument_id");
        break;

      case 'vtv':
        $tableToJoin .= 'oilvehicles';
        $id .= 'id';
        // $items .= "vehicle_brand,' ',plate_number,' ',capacity";
        $items .= "vehicle_brand,plate_number,CONCAT(capacity, ' ','Liters')";
        break;

      default:
        # code...
        break;
    }
    $builder->join($tableToJoin, "$tableToJoin.$id = transactions.instrument_id");
    $builder->join('customers', "customers.hash = transactions.customer_hash");
    $builder->like(['customers.name' => $keyword]);
    // $builder->where(['phone_number' => $phone]);
    $builder->where('payment', 'pending');
    $where = "DATE(transactions.created_on) = CURDATE()";

    $builder->where(['DATE(created_on)'=> $date]);
    // $builder->where($where);
    // $builder->orWhere(['control_number' => $controlNumber]);
    $builder->select(
      "$tableToJoin.hash,
      transactions.id,
      transactions.amount as total,
      name,phone_number,
      control_number,
      payment,
      $tableToJoin.amount,
      transactions.created_on,
      CONCAT_WS(' ',$items) as item
      "
    );
    // $builder->select();
    $builder->groupBy('control_number');


    return $builder->get()->getResult();
  }


  public function bill()
  {
    $activity = 'prepackage';
    $controlNumber = '951000000061';
    // $date = date('Y-m-d');




    $tableToJoin = '';
    $id = '';
    $items = '';



    $builder = $this->db->table('transactions');

    switch ($activity) {
      case 'prepackage':
        $id .= 'product_id';
        $tableToJoin .= 'prepackage';
        $items .= 'commodity,quantity,unit';
        $builder->join('product_details', "product_details.id = transactions.instrument_id");
        break;

      case 'vtv':
        $tableToJoin .= 'oilvehicles';
        $id .= 'id';
        $items .= "vehicle_brand,plate_number,CONCAT(capacity, ' ','Liters')";
        break;

      default:
        # code...
        break;
    }
    $builder->join($tableToJoin, "$tableToJoin.$id = transactions.instrument_id");
    $builder->join('customers', "customers.hash = transactions.customer_hash");

    $builder->where(['control_number' => $controlNumber]);


    $builder->select(
      "$tableToJoin.hash,
      transactions.id,
      transactions.amount as total,
      name,phone_number,
      control_number,
      payment,
      $tableToJoin.amount,
      transactions.created_on,
      CONCAT_WS(' ',$items) as item
      "
    );



    return $builder->get()->getResult();
  }





















  public function getMonth($from, $to, $year)
  {
    return $this->transactionTable
      ->select()
      ->where('MONTH(created_on) BETWEEN ' . $from . ' AND ' . $to . '')
      ->where('YEAR(created_on) = ' . $year . '')
      ->get()
      ->getResult();
  }
  public function customDateRange($dateFrom, $dateTo)
  {
    return $this->dataTable
      ->select()
      ->where(['work_date >=' => $dateFrom])
      ->where(['work_date <=' => $dateTo])
      ->orderBy('work_date', 'ASC')
      ->get()
      ->getResult();
  }
  public function quarterWithCustomDateRange($dateFrom, $dateTo, $monthFrom, $monthTo, $year)
  {
    return $this->dataTable
      ->select()
      ->where('MONTH(work_date) BETWEEN ' . $monthFrom . ' AND ' . $monthTo . '')
      ->where('YEAR(work_date) = ' . $year . '')
      ->where(['work_date >=' => $dateFrom])
      ->where(['work_date <=' => $dateTo])
      ->orderBy('work_date', 'ASC')
      ->get()
      ->getResult();
  }
  public function quarterNoCustomDate($monthFrom, $monthTo, $year)
  {
    return $this->dataTable
      ->select()
      ->where('MONTH(work_date) BETWEEN ' . $monthFrom . ' AND ' . $monthTo . '')
      ->where('YEAR(work_date) = ' . $year . '')
      ->orderBy('work_date', 'ASC')
      ->get()
      ->getResult();
  }
  public function monthlyData($month, $year)
  {
    return $this->dataTable
      ->select()
      ->where('MONTH(work_date) = ' . $month . '')
      ->where('YEAR(work_date) = ' . $year . '')
      ->orderBy('work_date', 'ASC')
      ->get()
      ->getResult();
  }

  function testRole()
  {
  }

  public function byRole($role, $payment)
  {
    if ($role == 1) {
      switch ($payment) {
        case 'paid':
          return $this->dataTable
            ->select()
            ->where(['salary >' => 873000])
            ->get()
            ->getResult();
          break;

        default:
          # code...
          break;
      }
      return $this->dataTable
        ->select()
        ->where(['salary >' => 873000])
        ->get()
        ->getResult();
    } else if ($role == 2) {
      return $this->dataTable
        ->select()
        ->where(['salary >' => 358428874])
        ->get()
        ->getResult();
    }
  }
}
