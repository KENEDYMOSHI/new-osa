<?php

namespace App\Models;

use Config\Database;
use CodeIgniter\Model;
use App\Libraries\ArrayLibrary;

class WaterMeterModel extends Model
{
    public $db;
    public $waterMetersTable;
    public $billedItems;
    public $billTable;
    public $GfsCode;


    public function __construct()
    {
        $this->GfsCode = setting('Gfs.waterMeter');
        $this->db = \Config\Database::connect();
        $this->waterMetersTable = $this->db->table('water_meters');
        $this->billedItems = $this->db->table('bill_items');
        $this->billTable = $this->db->table('wma_bill');
    }

    public function sqlMode()
    {
        $this->db->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
    }

    public function registerWaterMeter($data)
    {

        return $this->waterMetersTable->insertBatch($data);
    }
    //=================Publish water meter in transaction table====================
    public function publishWaterMeterData($data)
    {

        return $this->billedItems->insertBatch($data);
    }

    //=================check all paid water meter if exist====================
    public function filterCustomersPaidWaterMeters($hash)
    {
        return $this->billedItems
            ->select('BillItemRef')
            ->where(['PayerId' => $hash])
            ->get()
            ->getResult();
    }
    public function findMeter($params)
    {
        return $this->waterMetersTable
            ->select()
            ->where($params)
            ->get()
            ->getRow();
    }
    //=================get meter details====================
    public function getMeterDetails($id)
    {
        return $this->waterMetersTable
            ->select()
            ->where(['id' => $id])
            ->get()
            ->getRow();
    }
    //ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION

    public function getAllUnpaidWaterMeters($hash, $ids)
    {

        $this->sqlMode();
        return $this->waterMetersTable
            ->select()
            ->where(['hash' => $hash])
            ->whereIn('batch_id', $ids)
            ->groupBy('batch_id')
            ->get()
            ->getResult();
    }
    public function getMetersByBatchId($batchId)
    {
        return $this->waterMetersTable
            ->select()
            ->where(['batch_id' => $batchId])

            ->get()
            ->getResult();
    }
    public function getMetersByBatch($batchId)
    {
        return $this->waterMetersTable
            ->select('
            water_meters.*,
            first_name,
            last_name,
            username,
            users.unique_id,
            customers.hash,
            customers.phone_number,
            customers.district,
            customers.name,
            customers.postal_address
            
            ')
            ->where(['batch_id' => $batchId])
            ->join('customers', 'customers.hash = water_meters.hash')
            ->join('users', 'users.unique_id = water_meters.unique_id')
            ->get()
            ->getResult();
    }
    public function getVerifiedMetersByBatch($batchId)
    {
        $this->sqlMode();
        return $this->waterMetersTable
            ->select()
            ->where(['batch_id' => $batchId])
            ->groupBy('batch_id')
            ->get()
            ->getResult();
    }

    //=================check Last id====================

    public function checkLastId()
    {
        return $this->waterMetersTable
            ->select('id')
            ->orderBy('data_id', 'DESC')
            ->limit(1)
            ->get()
            ->getResult();
    }

    public function getRegisteredWaterMeters($params)
    {
        return $this->billedItems
            ->select('
            wma_bill.BillId,
            ItemName,
            PayCntrNum,
            PyrName,
            BillAmt as amount,
            quantity,
            BillGenBy,
            PaymentStatus,
            CONCAT(first_name," ", last_name) as Officer,
            wma_bill.CreatedAt
            ')
            ->where($params)
            ->where(['wma_bill.Activity' =>  $this->GfsCode])
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->join('water_meters', 'water_meters.batch_id = bill_items.BillItemRef')
            ->join('users', 'users.unique_id = bill_items.UserId')

            ->get()
            ->getResult();
    }


    public function verifiedWaterMeters($params)
    {
        $this->sqlMode();
        return $this->waterMetersTable
            ->select(
                '
                water_meters.id,
                PyrName,
                amount,
                PaymentStatus,
                CollectionCenter,
                PyrId,
                PayCntrNum,
                PaymentStatus,
                wma_bill.Task,
                PyrCellNum,
                water_meters.created_at,
                CONCAT(first_name," ",last_name) as officer,
                meter_size,
                brand,
                users.unique_id,
                quantity,
                ward,
                flow_rate,
                class,
                testing_method,
                lab,
                batch_id,
                customers.region,
                postal_address,postal_code,
'
            )
            ->where($params)
            ->where(['PayCntrNum !=' => ''])
            ->groupBy('batch_id')
            ->join('users', 'users.unique_id = water_meters.unique_id')
            ->join('bill_items', 'bill_items.BillItemRef = water_meters.batch_id')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->join('customers', 'customers.hash = water_meters.hash', 'left')
            ->get()
            ->getResult();
    }








    public function getAllWaterMeters($region)
    {
        return $this->billedItems
            ->select()
            //->select('customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number,water_meters.vehicle_brand')
            ->where(['customers.region' => $region])
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('water_meters', 'water_meters.batch_id = bill_items.BillItemRef')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            //  //->groupBy(['water_meters.id','batch_id'])
            ->get()
            ->getResult();
    }
    public function getAllWaterMetersTz()
    {
        $this->sqlMode();
        return $this->billedItems
            ->select()
            //->select('customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number,water_meters.vehicle_brand')
            // ->where(['customers.region' => $region])
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('water_meters', 'water_meters.batch_id = bill_items.BillItemRef')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            //->groupBy(['water_meters.id','batch_id'])
            ->groupBy('batch_id')
            ->get()
            ->getResult();
    }

    public function deleteRecord($id)
    {
        $this->waterMetersTable
            ->where(['id' => $id])
            ->delete();
    }
    public function editRecord($id)
    {
        return $this->waterMetersTable
            ->select()
            ->select('customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number')
            ->where(['id' => $id])
            ->join('customers', 'customers.hash = watermeter.PayerId')
            ->get()
            ->getRow();
    }

    public function updateMeterAmount($batchId,$data)
    {

        return $this->waterMetersTable
            ->set($data)
            ->where(['batch_id'=>$batchId])
            ->update();
    }
    public function updateWaterMeterData($data, $id)
    {

        return $this->waterMetersTable
            ->set($data)
            ->where(['id' => $id])
            ->update();
    }
    public function updatePayment($data, $id)
    {

        return $this->waterMetersTable
            ->set($data)
            ->where(['id' => $id])
            ->update();
    }
    public function updateVerifiedMeters($id, $data)
    {

        return $this->waterMetersTable
            ->set($data)
            ->where(['batch_id' => $id])
            ->update();
    }

    //=================get paid and pending amounts in water meter in specific region ====================
    public function waterMeterDetails($region)
    {
        return $this->billedItems
            ->select('water_meters.amount')
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('water_meters', 'water_meters.batch_id = bill_items.BillItemRef')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            //->groupBy(['water_meters.id','batch_id'])
            ->where(['customers.region' => $region])
            ->get()
            ->getResult();
    }
    // ================Get all details in all regions==============
    public function getAllInRegion($location)
    {
        $this->sqlMode();
        return $this->billedItems
            ->select('bill_items.id,water_meters.amount,customers.region,water_meters.quantity')
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('water_meters', 'water_meters.batch_id = bill_items.BillItemRef')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->groupBy('batch_id')
            ->where(['customers.region' => $location])
            ->get()
            ->getResult();
    }

    // ================Api data for specific region(MANAGER) ==============
    public function getData($city)
    {
        $this->sqlMode();

        return $this->billedItems
            ->select('water_meters.amount,customers.region,water_meters.created_at')
            ->where(['customers.region' => $city])
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('water_meters', 'water_meters.batch_id = bill_items.BillItemRef')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->groupBy('batch_id')
            ->get()
            ->getResult();
    }

    // ================Data for Api for entire country (DIRECTOR) ==============
    public function getFullDataForDirector()
    {
        return $this->billedItems
            ->select('water_meters.amount,water_meters.created_at,water_meters.quantity,PaymentStatus')
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('water_meters', 'water_meters.batch_id = bill_items.BillItemRef')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            //->groupBy(['water_meters.id','batch_id'])
            ->get()
            ->getResult();
    }

    //=================DATA FOR THE REPORTS====================
    //=================%% report for officers %%====================

    //=================%% report for managers %%====================

    //=================%% report for directors %%====================

    #--------------------------------------------------------
    #
    # ONLY WITHIN A QUARTER
    #
    #--------------------------------------------------------------

    //=================%% report for officers %%====================
    public function waterMeterQuarterReport($params, $monthFrom, $monthTo)
    {
        return $this->billedItems
            ->select('PaymentStatus,users.first_name as fName,users.last_name as lName')
            ->select('customers.name,customers.phone_number,water_meters.brand,water_meters.quantity,water_meters.flow_rate,water_meters.class,water_meters.amount,meter_size')

            ->select('BillItemRef,bill_items.CreatedAt')
            ->where($params)
            ->where('MONTH(bill_items.CreatedAt) BETWEEN ' . $monthFrom . ' AND ' . $monthTo . '')


            ->orderBy('bill_items.CreatedAt', 'ASC')
            ->join('users', 'users.unique_id = bill_items.UserId')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('water_meters', 'water_meters.batch_id = bill_items.BillItemRef')
            //->groupBy(['water_meters.id','batch_id'])

            ->get()
            ->getResult();
    }




    //=================%% report for directors %%====================




    #--------------------------------------------------------
    ###########################################################
    # ################ONLY WITHIN A MONTH #####################
    ###########################################################
    #--------------------------------------------------------------

    //=================%% report for officers %%====================
    public function dataReport($params)
    {
        return $this->billedItems
            ->select('customers.name,customers.phone_number,water_meters.brand,water_meters.quantity,water_meters.flow_rate,water_meters.class,water_meters.amount ,meter_size')
            ->select('BillItemRef,bill_items.CreatedAt')
            ->select('PaymentStatus,users.first_name as fName,users.last_name as lName')
            ->where($params)
            ->orderBy('bill_items.CreatedAt', 'ASC')
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('water_meters', 'water_meters.batch_id = bill_items.BillItemRef')
            //->groupBy(['water_meters.id','batch_id'])
            ->join('users', 'users.unique_id = bill_items.UserId')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->get()
            ->getResult();
    }

    public function collectionSum($params)
    {
        return $this->billedItems
            ->selectSum('water_meters.amount')

            ->where($params)
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('water_meters', 'water_meters.batch_id = bill_items.BillItemRef')
            //->groupBy(['water_meters.id','batch_id'])
            ->get()
            ->getResult();
    }

    public function dateRangeReport($params, $dateFrom, $dateTo)
    {
        return $this->billedItems
            ->select('customers.name,customers.phone_number,water_meters.brand,water_meters.quantity,water_meters.flow_rate,water_meters.class,water_meters.amount ,meter_size')
            ->select('BillItemRef,bill_items.CreatedAt')
            ->select('PaymentStatus,users.first_name as fName,users.last_name as lName')
            ->where($params)
            ->where(['bill_items.CreatedAt >=' => $dateFrom])
            ->where(['bill_items.CreatedAt <=' => $dateTo])
            ->orderBy('bill_items.CreatedAt', 'ASC')
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('water_meters', 'water_meters.batch_id = bill_items.BillItemRef')
            //->groupBy(['water_meters.id','batch_id'])
            ->join('users', 'users.unique_id = bill_items.UserId')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->get()
            ->getResult();
    }






    //=================searching water meters====================
    public function searchingWaterMeter()
    {
        return $this->waterMetersTable
            ->select(
                '
       water_meters.batch_id,
       water_meters.activity,
       water_meters.created_at,
       water_meters.meter_size,
       water_meters.brand,
       water_meters.quantity,
       water_meters.flow_rate,
       water_meters.class,
       water_meters.lab,
       water_meters.testing_method,
       water_meters.PaymentStatus,
       water_meters.PaymentStatus,
       water_meters.other_charges,
       water_meters.remark,
       water_meters.other_charges,

       
       


       PaymentStatus,users.first_name as officerFirstName,
       users.last_name as officerLastName

        '
            )
            ->select('customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number')
            ->join('bill_items', 'water_meters.batch_id = bill_items.BillItemRef')
            ->join('customers', 'customers.hash = water_meters.hash')
            ->join('users', 'users.unique_id = water_meters.unique_id')
            ->get()
            ->getResultArray();
    }

    //=================get water meter details after searching====================
    public function waterMeterMatch($id)
    {
        return $this->waterMetersTable
            ->select(
                '


        water_meters.activity,
        water_meters.created_at,
        water_meters.meter_size,
        water_meters.brand,
        water_meters.quantity,
        water_meters.flow_rate,
        water_meters.class,
        water_meters.lab,
        water_meters.testing_method,
        water_meters.PaymentStatus,
        water_meters.amount,
        water_meters.other_charges,
        water_meters.remark,
        water_meters.other_charges,

       
       


       PaymentStatus,users.first_name as officerFirstName,
       users.last_name as officerLastName

        '
            )
            ->select('customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number')
            ->where(['water_meters.batch_id' => $id])
            ->join('bill_items', 'water_meters.batch_id = bill_items.BillItemRef')
            ->join('customers', 'customers.hash = water_meters.hash')
            ->join('users', 'users.unique_id = water_meters.unique_id')
            ->get()
            ->getRow();
    }


    public function meterCount($params)
    {
        $this->sqlMode();
        $builder = $this->waterMetersTable->select('batch_id,quantity,deletedAt,created_at,amount,decision')
            ->where(['deletedAt' => null])
            ->where($params)
            ->where('decision', 'PASS')
           // ->limit(100)
            ->groupBy('batch_id')
            ->get()
            ->getResult();
    
        $res = null; // Initialize $res
    
        if (!empty($builder)) {
            $res = array_reduce($builder, function ($carry, $item) {
                $quantity = (int)$item->quantity;
                $amount = $item->amount;
    
                // Incrementing quantities and amounts
                $carry['quantity'] += $quantity;
                $carry['amount'] += $amount;
    
                return $carry;
            }, ['quantity' => 0, 'amount' => 0]); // Initialize carry with 0 values
        }else{
            return (object)['quantity' => 0, 'amount' => 0];
        }
    
        return (object)$res;
    }
    
    

    public function meterInspection($params)
    {
        return $this->waterMetersTable->select('collection_center,task,visualInspection,testing,water_meters.created_at')
            ->join('users', 'users.unique_id = water_meters.unique_id')

            ->where($params)

            // ->countAllResults();
            ->get()->getResult();
    }

    public function getVerifiedMeters($serialNumbers)
    {
        $this->sqlMode();
        return $this->waterMetersTable
            ->select('

            water_meters.id,
            water_meters.hash as customer_id,
            customers.name as customer_name,
            customers.phone_number,
            customers.postal_address,
            customers.region,
            district,
            ward,
            postal_address,
            water_meters.batch_id,
            brand,
            serial_number,
            meter_size,
            actual_volume,
            verifier,
            initial_reading,
            final_reading,
            indicated_volume,
            rate as flow_rate,
            class,
            lab as testing_lab,
            testing_method,
            error,
            decision,
            created_at as verification_date


            ')
            ->join('customers', 'customers.hash = water_meters.hash')
            ->limit(20)
            ->whereIn('serial_number', $serialNumbers)
            // ->orderBy('id','ASC')
            ->groupBy('batch_id') //FIXME Remove the group by 
            ->get()
            ->getResult();
    }
}
