<?php
//SELECT officers_group.officer_id,users.first_name,users.email, officers_group.group_name FROM officers_group INNER JOIN users ON officers_group.officer_id=users.unique_id
namespace App\Models;

use App\Libraries\ArrayLibrary;
use CodeIgniter\Model;
use Config\Database;

class DashboardModel extends Model
{
  protected $db;
  protected $taskGroup;
  protected $tasks;
  protected $officerGroup;
  protected $vtv;
  protected $sbl;
  protected $waterMeters;
  protected $billTable;
  protected $payments;
  protected $user;
  public function __construct()
  {
    $this->db = \Config\Database::connect();
    $this->taskGroup = $this->db->table('task_group');
    $this->tasks = $this->db->table('tasks');
    $this->officerGroup = $this->db->table('officers_group');
    $this->billTable = $this->db->table('wma_bill');
    $this->vtv = $this->db->table('calibrated_tanks');
    $this->sbl = $this->db->table('verified_lorries');
    $this->waterMeters = $this->db->table('water_meters');
    $this->payments = $this->db->table('bill_payment');
    $this->user = auth()->user();
   
  }


  public function sqlMode()
  {
      $this->db->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
  }

  public function vtv($params)
  {

      return $this->vtv
          ->select(
              " 
              wma_bill.BillId,
              PyrName,
              BillItemAmt as amount,
              PaymentStatus,
              CollectionCenter,
              IsCancelled,
              wma_bill.CreatedAt,
              trailer_plate_number,
              GfsCode,
              region,
              "
          )
          
          ->where(['PayCntrNum !=' => ''])
          ->where($params)
          ->join('bill_items', 'bill_items.BillItemRef = calibrated_tanks.id')
          ->join('wma_bill', 'wma_bill.PayCntrNum = bill_items.controlNumber')
          ->get()
          ->getResult();
  }
  public function sbl($params)
  {

    $data = $this->sbl
          ->select(
              " 
              PaymentStatus,
              BillItemAmt as amount,
              controlNumber,
               wma_bill.CreatedAt,
              "
          )
          ->where($params)
       //  ->where('MONTH(bill_items.CreatedAt)','08')
          // ->where('PaymentStatus','Paid')

          ->where(['PayCntrNum !=' => ''])
          ->join('bill_items', 'bill_items.BillItemRef = verified_lorries.id')
          ->join('wma_bill', 'wma_bill.PayCntrNum = bill_items.controlNumber','left')
          ->get()
          ->getResult();

          return $data;

        //   $amt = (new ArrayLibrary($data))
        //  ->filter(fn($v)=>$v->PaymentStatus == 'Paid')
        //   ->reduce(fn($a,$b)=>$a + $b->amount)->get();

        //   return [
        //     'params'=> $params,
        //     'amount' => number_format($amt)
        //   ];
  }


  public function waterMeters($params)
    {
        $this->sqlMode();
        return $this->waterMeters
            ->select(
                '
                water_meters.id,
                PyrName,
                
                PaymentStatus,
                CollectionCenter,
                quantity,
                PaymentStatus,
                wma_bill.Task,
                PyrCellNum,
                PyrName,
                BillItemAmt as amount,
               '
            )
            ->where($params)
            ->where(['PayCntrNum !=' => ''])
            ->groupBy('batch_id')
            ->join('bill_items', 'bill_items.BillItemRef = water_meters.batch_id')
            ->join('wma_bill', 'wma_bill.PayCntrNum = bill_items.controlNumber')
            ->get()
            ->getResult();
    }



    public function ppg($params)
    {
        // Separate 'Activity' from $params if it exists
        

        $query = $this->billTable
            ->select('
                wma_bill.BillId,
               
                PaymentStatus,
                PyrName,
                PyrCellNum,
                ItemName,
                BillItemAmt as amount,
                GfsCode,
                wma_bill.CreatedAt
            ')
            ->where(['GfsCode'=>setting('Gfs.prePackages')])
            ->where($params);

       

       // $query->join('collectioncenter', 'collectioncenter.CenterNumber = wma_bill.CollectionCenter', 'LEFT');
        $query->join('bill_items', 'wma_bill.PayCntrNum = bill_items.controlNumber');

        return $query->get()->getResult();
    }
   

    public function others($params)
    {
      // Separate 'Activity' from $params if it exists
      // $except = [
      //   setting('Gfs.prePackage'),
      //   setting('Gfs.vtv'),
      //   setting('Gfs.sbl'),
      //   setting('Gfs.waterMeter'),
      // ];
  
      $query = $this->billTable
        ->select('
                  wma_bill.BillId,
                 
                  PaymentStatus,
                  PyrName,
                  PyrCellNum,
                  ItemName,
                  BillItemAmt as amount,
                  GfsCode,
                  wma_bill.CreatedAt
              ')
             ->where($params)
        ->whereNotIn('GfsCode' ,['142101210007','142101210003','142101210013','142101210035']);
        
  
      // $query->join('collectioncenter', 'collectioncenter.CenterNumber = wma_bill.CollectionCenter', 'LEFT');
      $query->join('bill_items', 'wma_bill.PayCntrNum = bill_items.controlNumber');
  
      return $query->get()->getResult();
    }

    public function othersPaidOnly(){



      $queryParams = [

        'DATE(bill_payment.CreatedAt)>=' => $this->user->inGroup('manager', 'officer') ? '' : financialYear()->startDate,
        'DATE(bill_payment.CreatedAt) <=' => $this->user->inGroup('manager', 'officer') ? '' : financialYear()->endDate,
        "MONTH(bill_payment.CreatedAt)" => $this->user->inGroup('manager', 'officer') ?  date('m') : '',
        'CenterNumber' => $this->user->inGroup('officer', 'manager') ? $this->user->collection_center : '',
       // 'IsCancelled' => 'No',
        // 'PaymentStatus' => 'Paid',
];

      $params = array_filter($queryParams, fn ($param) => $param !== '' || $param != null);
      $query = $this->payments
      ->select('
                bill_payment.BillId,
               
                "Paid" as PaymentStatus,
                PyrName,
                PyrCellNum,
                ItemName,
                BillItemAmt as amount,
                GfsCode,
                bill_payment.CreatedAt
            ')
           ->where($params)
      ->whereNotIn('GfsCode' ,['142101210007','142101210003','142101210013','142101210035']);
      

    // $query->join('collectioncenter', 'collectioncenter.CenterNumber = wma_bill.CollectionCenter', 'LEFT');
    $query->join('bill_items', 'bill_payment.PayCtrNum = bill_items.controlNumber');

    return $query->get()->getResult();
    }





  




  // ==============================
}