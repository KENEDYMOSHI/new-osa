<?php

namespace App\Models;

use CodeIgniter\Model;

class BillModel extends Model
{
    protected $billTable;
    protected $billPayment;
    protected $billItems;
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->billTable = $this->db->table('wma_bill');
        $this->billPayment = $this->db->table('bill_payment');
        $this->billItems = $this->db->table('bill_items');
    }

    public function getPaymentCollection($params)
    {
        // $params comes from WMA-MIS, keys might need adjustment if table aliases differ
        // WMA-MIS uses 'bill_payment' table name in keys.
        
        $query = $this->billPayment
            ->select('
                bill_payment.BillId,
                Activity,
                Task,
                PaymentStatus,
                PaidAmt as amount,
                CollectionCenter,
                bill_payment.CreatedAt
            ')
            ->where($params)
            ->join('wma_bill ', 'wma_bill.PayCntrNum = bill_payment.PayCtrNum', 'left');

        return $query->get()->getResult();
    }
    
    public function getReportData($params)
    {
          // Separate 'Activity' from $params if it exists
        $activityLike = null;
        if (isset($params['Activity'])) {
            $activityLike = $params['Activity'];
            unset($params['Activity']);
        }

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
                BillItemAmt as amount,
                PaidAmount,
                BillAmt as BilledAmount,
                PayCntrNum,
                BillGenBy,
                BillExprDt,
                CollectionCenter,
                CenterName,
                Ccy,
                wma_bill.CreatedAt
            ')
            ->where($params);

        if ($activityLike !== null) {
            $query->like('Activity', $activityLike);
        }

        $query->join('collectioncenter', 'collectioncenter.CenterNumber = wma_bill.CollectionCenter');
        $query->join('bill_items', 'wma_bill.BillId = bill_items.BillId', 'left');

        return $query->get()->getResult();
    }
}
