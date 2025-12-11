<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\ArrayLibrary;

class ReportModel extends Model
{
    protected $billTable;
    protected $billItems;
    protected $db;


    protected $billPayment;

    protected $gfsCode;
    protected $collectionCenter;

    protected $partialReference;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->billTable = $this->db->table('wma_bill');
        $this->billItems = $this->db->table('bill_items');

        $this->billPayment = $this->db->table('bill_payment');

        $this->collectionCenter = $this->db->table('collectioncenter');
    }

    public function sqlMode()
    {
        $this->db->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
    }

    public function getPendingPartialPayments($params, $paymentStatus)
    {



        $status = ['Partial', 'Pending'];

        if ($paymentStatus == 'Partial') {
            $key = array_search('Pending', $status);
            if ($key !== false) {
                unset($status[$key]);
            }
        }
        return $this->billTable->select('PyrName as customer,controlNumber,CollectionCenter as region,GfsCode,ItemName,PaidAmount,BillAmt,BillItemAmt  as amount,wma_bill.CreatedAt as date,BillPayOpt, PaymentStatus,bill_items.Task,PyrCellNum,BillItemRef')
            ->where($params)
            ->where(['wma_bill.IsCancelled' => 'No'])
            ->whereIn('PaymentStatus', $status)
            ->limit(0)
            ->join('bill_items', 'wma_bill.PayCntrNum = bill_items.controlNumber')
            ->get()
            ->getResult();
    }
    public function getPendingAndPartial($params)
    {



        $status = ['Partial', 'Pending'];


        $query = $this->billTable->select('CollectionCenter,PaymentStatus ,PaidAmount,BillAmt,wma_bill.BillGenDt');

        if (!empty($params)) {
            $query->where($params);
        }
        $query->where(['wma_bill.IsCancelled' => 'No']);
        $query->whereIn('PaymentStatus', $status);
        //$query->limit(300);
        return $query->get()->getResult();
    }


    public function getPaidPayments($params)
    {
        return $this->billPayment->select('PyrName as customer,controlNumber,CenterNumber as region,GfsCode,ItemName,PaidAmt,clearedAmount,BillAmt,BillItemAmt  as amount,TrxDtTm as date,BillPayOpt,"Paid" as PaymentStatus,Task,PyrCellNum,BillItemRef,PayRefId')
            ->where($params)
            //  ->limit(22000)
            ->join('bill_items', 'bill_payment.PayCtrNum = bill_items.controlNumber')
            ->get()
            ->getResult();
    }


    public function getInstrumentsCount($params)
    {
        return  $this->billItems->select('GfsCode,center,BillItemAmt,ItemQuantity,Status,CreatedAt')
            //->where(['GfsCode' => $gfsCode,])
            //    ->limit(320)
            ->where('deletedAt', NULL)
            ->where($params)
            ->get()
            ->getResult();
    }

    public function getCollectionCenters()
    {
        return $this->collectionCenter->select()
            ->get()
            ->getResult();
    }
}
