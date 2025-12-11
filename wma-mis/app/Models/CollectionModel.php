<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\ArrayLibrary;

class CollectionModel extends Model
{
    protected $billTable;
    protected $billItems;
    protected $db;
    protected $gepg;
    protected $ack;

    protected $billPayment;
    protected $gfsCode;
    protected $collectionCenter;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->billTable = $this->db->table('wma_bill');
        $this->billItems = $this->db->table('bill_items');


        $this->billPayment = $this->db->table('bill_payment');

        $this->gfsCode = $this->db->table('gfscode');
        $this->collectionCenter = $this->db->table('collectioncenter');


        // $this->db->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'"); 
    }


    public function getPaymentCollection($params)
    {
        // Separate 'Activity' from $params if it exists


        return  $this->billPayment
            ->select('
                bill_payment.BillId,
               
                PaymentStatus,
              
                clearedAmount as amount,
                
                CollectionCenter,
                CenterName,
              
                bill_payment.CreatedAt
            ')
            ->where($params)
            //  ->limit(10)
            ->join('wma_bill ', 'wma_bill.BillId = bill_payment.BillId', 'right')
            ->join('collectioncenter', 'collectioncenter.CenterNumber = wma_bill.CollectionCenter')

            ->get()->getResult();
    }

    public function getBillItems($billIds)
    {
        // $billIds === [] ? [microtime()] : $billIds;
      //  $x =[5467,45678];
        return $this->billItems->select(

            '
            bill_items.BillId ,
            ItemName,
            PyrName,
            ItemQuantity,
            "Paid" as PaymentStatus,
            PyrCellNum,
            PayCntrNum,
            GfsCode as Activity,
            bill_items.Task,
            BillItemRef,
            BillPayOpt,
            BillItemAmt as amount,
            paidAmount,
            BillAmt as BilledAmount,
            wma_bill.CreatedAt'
        )
            ->join('wma_bill', 'bill_items.BillId = wma_bill.BillId')
           
            ->whereIn('bill_items.BillId', $billIds)
            //   ->limit(5)
            ->get()->getResult();
    }

    public function getReportData($params)
    {



        $query = $this->billTable
            ->select('
                wma_bill.BillId,
                GfsCode as Activity,
                BillItemRef,
                bill_items.Task,
                PaymentStatus,
                PyrName,
                PyrCellNum,
                ItemName,
                BillPayOpt,
                ItemQuantity,
                BillItemAmt as amount,
                PaidAmount,
                BillAmt as BilledAmount,
                PayCntrNum,
               
                CollectionCenter,
                CenterName,
             
                wma_bill.CreatedAt
            ')
            // ->where('PayCntrNum !=','')
            ->where($params);
        //->limit(3);



        $query->join('collectioncenter', 'collectioncenter.CenterNumber = wma_bill.CollectionCenter');
        // $query->groupBy('PayCntrNum');
        $query->join('bill_items', 'wma_bill.BillId = bill_items.BillId', 'left');

        return $query->get()->getResult();
    }
}
