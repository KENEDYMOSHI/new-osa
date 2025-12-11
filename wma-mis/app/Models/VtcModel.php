<?php

namespace App\Models;

use Config\Database;
use CodeIgniter\Model;
use App\Libraries\ArrayLibrary;

class VtcModel extends Model
{
    public $db;
    public $dataTable;
    public $calibratedTanks;
    public $billedItems;
    public $compartments;
    protected $vehicle;
    protected $billTable;
    protected $chartTable;
    protected $GfsCode;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->dataTable = $this->db->table('vtc');
        $this->vehicle = $this->db->table('vehicle_tanks');
        $this->calibratedTanks = $this->db->table('calibrated_tanks');
        $this->billedItems = $this->db->table('bill_items');
        $this->billTable = $this->db->table('wma_bill');
        $this->compartments = $this->db->table('compartments');
        $this->chartTable = $this->db->table('chart_number');
        $this->GfsCode = '142101210024';
    }
    //=================find any vehicle match====================
    public function findMatch($hash, $plateNumber)
    {
        return $this->vehicle
            ->select()
            ->where(['hash' => $hash])
            ->like(['trailer_plate_number' => $plateNumber])
            ->orLike(['hose_plate_number' => $plateNumber])
            ->get()
            ->getRow();
    }
    public function findVehicleTank($params)
    {
        return $this->vehicle
            ->select('vehicle_tanks.*,name,customers.region as city,customers.phone_number,postal_address ')
            ->where($params)
            ->join('customers', 'customers.hash = vehicle_tanks.hash')
            ->get()
            ->getRow();
    }

    public function getVehicle($id)
    {
        return $this->vehicle
            ->select()
            ->where(['id' => $id])
            ->get()
            ->getRow();
    }
    public function getVehicleBySticker($sticker)
    {
        return $this->calibratedTanks
            ->select()
            ->where(['sticker_number' => $sticker])
            ->get()
            ->getRow();
    }
    public function getCalibratedVehicle($params)
    {
        return $this->calibratedTanks
            ->select()
            ->where($params)
            ->join('bill_items', 'bill_items.BillItemRef = calibrated_tanks.id')
            ->get()
            ->getRow();
    }
    public function updateTank($id, $data)
    {
        return $this->calibratedTanks
            ->where(['id' => $id])
            ->set($data)
            ->update();
    }

    public function getClientVehicles($hash)
    {
        return $this->vehicle
            ->select()
            //    ->select("SUM(compartments.litres) AS ltr")
            ->where(['vehicle_tanks.hash' => $hash])
            //    ->join('compartments', 'compartments.vehicle_id = vehicle_tanks.id','right')
            // ->groupBy('data_id')
            ->get()
            ->getResult();
    }
    public function getCompartments($vehicleId)
    {
        return $this->compartments
            ->select()
            ->where(['vehicle_id' => $vehicleId])
            ->get()
            ->getResult();
    }

    public function deleteCompartmentData($id)
    {
        return $this->compartments->where(['id' => $id])->delete();
    }
    public function getCompartmentData($compartmentNumber, $vehicleId)
    {
        return $this->compartments
            ->select()
            ->where(['compartment_number' => $compartmentNumber])
            ->where(['vehicle_id' => $vehicleId])
            ->get()
            ->getResult();
    }
    public function findVehicle($id)
    {
        return $this->vehicle
            ->select()
            ->where(['id' => $id])
            ->get()
            ->getRow();
    }

    //=================Register vehicle tanks====================
    public function registerVehicleTank($data)
    {

        return $this->vehicle->insert($data);
    }
    public function registerVehicleTankRecord($data)
    {

        return $this->calibratedTanks->insertBatch($data);
    }
    //=================updating vehicle====================
    public function updateVehicleTank($data, $id)
    {

        return $this->vehicle
            ->set($data)
            ->where(['id' => $id])
            ->update();
    }
    //=================Publish vtc ni transaction====================
    public function publishVtcTransaction($data)
    {

        return $this->billedItems->insertBatch($data);
    }

    public function grabTheLastVehicle()
    {
        return $this->vehicle
            ->select()
            ->orderBy('data_id', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();
    }

    //=================check Last id====================

    public function checkLastId()
    {
        return $this->vehicle
            ->select('id')
            ->orderBy('data_id', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();
    }
    public function checkLastIdByPlate($plateNumber)
    {
        return $this->vehicle
            ->select('id')
            ->where(['trailer_plate_number' => $plateNumber])
            ->orderBy('data_id', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();
    }
    public function checkLastVehicleId()
    {
        return $this->calibratedTanks
            ->select('id')
            ->orderBy('data_id', 'DESC')
            ->limit(1)
            ->get()
            ->getResult();
    }

    //=================check all paid vehicles if exist====================
    public function filterCustomersPaidVehicles($params)
    {
        return $this->calibratedTanks
            ->select('original_id')
            ->where($params)
            ->get()
            ->getResult();
    }

    public function getAllUnpaidVehiclesTanks($params, $task)
    {
        $ids = (new AppModel())->getItemIds($params);
        return $this->vehicle
            ->select()
            ->where(['task' => $task])
            ->whereIn('id', $ids)
            ->get()
            ->getResult();
    }
    public function getAllUnpaidVehicles($params)
    {
        $ids = (new AppModel())->getItemIds($params);

        $vehicleIds = [];

        foreach ($ids as $id) {
            $hasCompartment = $this->compartments->select()->where('vehicle_id', $id)->get()->getRow();
            if (!$hasCompartment) {
                $vehicleIds[] = $id;
            }
        }

        return $this->vehicle
            ->select()
            ->whereIn('id', $vehicleIds)
            ->get()
            ->getResult();
    }
    public function getVehicleDetails($params)
    {
        return $this->vehicle
            ->select()
            ->where($params)
            ->orderBy('data_id', 'DESC')
            ->limit(1)
            ->join('chart_number', 'chart_number.vehicleId = vehicle_tanks.id', 'left')
            ->get()
            ->getRow();
    }

    //=================addCompartmentData====================
    public function addCompartmentData($data)
    {

        return $this->compartments->insertBatch($data);
    }
    //=================addCompartmentData====================
    public function updateCompartmentData($data)
    {

        return $this->compartments->updateBatch($data, 'id');
    }

    public function checkChartNumber()
    {

        return  $this->chartTable->select()->limit(1)->orderBy('idNo', 'DESC')->get()->getRow();
    }
    public function createChartNumber($data)
    {

        return  $this->chartTable->insert($data);
    }
    public function updateChartIfo($chartNumber, $data)
    {

        return  $this->chartTable->where(['number' => $chartNumber])->set($data)->update();
    }
    public function updateMultipleChartIfo($data)
    {

        return  $this->chartTable->updateBatch($data, ['idNo', 'number']);
    }
    public function getChartIfo($vehicleId)
    {

        return  $this->chartTable->where(['vehicleId' => $vehicleId])->get()->getRow();
    }


    public function getRegisteredVtc($params)
    {
        return $this->billTable
            ->select('
        wma_bill.BillId,
       
        PayCntrNum,
        PyrName,
        BillAmt as amount,
        BillGenBy,
        PaymentStatus,
        CONCAT(first_name," ", last_name) as Officer,
        wma_bill.CreatedAt
        ')
            ->where($params)
            ->where(['wma_bill.Activity' => $this->GfsCode])
            ->where(['PayCntrNum !=' => ''])
            ->join('users', 'users.unique_id = wma_bill.UserId', 'left')
            ->get()
            ->getResult();
    }

    public function verifiedVtvX($params)
    {
        return $this->calibratedTanks
            ->select(
                " 
                BillId,
                PyrName,
                amount,
                PaymentStatus,
                CollectionCenter,
                PyrId,
                PayCntrNum,
                PaymentStatus,
                wma_bill.Task,
                PyrCellNum,
                UserId,
                wma_bill.CreatedAt,
                username as officer,
                calibrated_tanks.id as vehicleId,
                registration_date,
                next_calibration,
                driver_name,vehicle_brand,
                hose_plate_number,
                trailer_plate_number,
                users.unique_id,
                driver_name,
                physical_address,
                ward,
                customers.region,
                calibrated_tanks.status,
                sticker_number ,
                capacity,
                postal_address,postal_code,tin_number,
                driver_license,
                IF(DATEDIFF(CURRENT_DATE(), next_calibration) < 0, 'Valid', 'Not Valid') AS calibrationStatus
                "


            )
            ->where($params)
            // ->where(['wma_bill.Activity' =>$this->GfsCode])
            ->where(['PayCntrNum !=' => ''])
            ->join('users', 'users.unique_id = calibrated_tanks.unique_id')
            ->join('wma_bill', 'wma_bill.PyrId = calibrated_tanks.hash', 'left')
            // ->join('calibrated_tanks', 'calibrated_tanks.hash = wma_bill.PyrId')
            ->join('customers', 'customers.hash = calibrated_tanks.hash', 'left')
            ->get()
            ->getResult();
    }
    public function verifiedVtv($params)
    {

        return $this->calibratedTanks
            ->select(
                " 
                wma_bill.BillId,
                PyrName,
                BillItemAmt as amount,
                PaymentStatus,
                CollectionCenter,
                PyrId,
                PayCntrNum,
                PaymentStatus,
                wma_bill.Task,
                PyrCellNum,
                wma_bill.CreatedAt,
                '' as officer,
                calibrated_tanks.id as vehicleId,
                registration_date,
                next_calibration,
                driver_name,vehicle_brand,
                hose_plate_number,
                trailer_plate_number,
                driver_name,
                physical_address,
                ward,
                customers.region,
                calibrated_tanks.status,
                sticker_number ,
                capacity,
                postal_address,postal_code,tin_number,
                driver_license,
                IF(DATEDIFF(CURRENT_DATE(), next_calibration) < 0, 'Valid', 'Not Valid') AS calibrationStatus,
                calibrated_tanks.unique_id as officerId
                "
            )
            ->where($params)
            ->where(['PayCntrNum !=' => ''])
            //->join('users', 'users.unique_id = calibrated_tanks.unique_id')
            ->join('bill_items', 'bill_items.BillItemRef = calibrated_tanks.id')
            //    ->join('bill_items', 'SUBSTRING_INDEX(bill_items.BillItemRef, "_", 2)= SUBSTRING_INDEX(calibrated_tanks.id, "_", 2) ')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->join('customers', 'customers.hash = calibrated_tanks.hash', 'left')
            ->get()
            ->getResult();
    }
    public function getVtc()
    {
        return $this->billedItems
            ->select()
            ->select('users.phone_number as phoneNumber ,PaymentStatus,users.first_name as firstName ,users.last_name as lastName,customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number,calibrated_tanks.vehicle_brand,calibrated_tanks.amount,calibrated_tanks.id as trailer_id,')
            //  ->where(['customers.region' => $region])
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('calibrated_tanks', 'calibrated_tanks.id = bill_items.BillItemRef')
            ->join('users', 'users.unique_id = bill_items.UserId')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')

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
            ->join('customers', 'customers.hash = vtc.customer_hash')
            ->get()
            ->getRow();
    }

    // ================Get all details in all regions==============
    public function getAllInRegion($location)
    {

        return $this->billedItems
            ->select('bill_items.id,calibrated_tanks.amount,customers.region')
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('calibrated_tanks', 'calibrated_tanks.id = bill_items.BillItemRef')
            ->where(['customers.region' => $location])
            ->get()
            ->getResult();
    }

    // ================Api for a specific region==============

    public function getData($region)
    {
        return $this->billedItems
            ->select('customers.region,calibrated_tanks.registration_date,calibrated_tanks.amount')
            ->where(['customers.region' => $region])
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('calibrated_tanks', 'calibrated_tanks.id = bill_items.BillItemRef')
            ->get()
            ->getResult();
    }

    // ================Data for Api for entire country (DIRECTOR) ==============
    public function getFullDataForDirector()
    {
        return $this->billedItems
            ->select('calibrated_tanks.amount,calibrated_tanks.registration_date,PaymentStatus')
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('calibrated_tanks', 'calibrated_tanks.id = bill_items.BillItemRef')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->get()
            ->getResult();
    }

    public function vtcDetails($location)
    {
        return $this->billedItems
            ->select('calibrated_tanks.amount,calibrated_tanks.registration_date,calibrated_tanks.id,customers.region')
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('calibrated_tanks', 'calibrated_tanks.id = bill_items.BillItemRef')

            ->where(['customers.region' => $location])
            ->get()
            ->getResult();
    }

    //=================DATA FOR THE REPORTS====================




    #--------------------------------------------------------
    #
    # ONLY WITHIN A QUARTER
    #
    #--------------------------------------------------------------


    //=================%% quarter report %%====================
    public function vtcQuarterReport($params, $monthFrom, $monthTo)
    {
        return $this->billedItems
            ->select('PaymentStatus,users.first_name as fName,users.last_name as lName')
            ->select('customers.name,customers.region,customers.phone_number,calibrated_tanks.vehicle_brand,calibrated_tanks.trailer_plate_number,calibrated_tanks.capacity,calibrated_tanks.amount as vehicle_amount')
            ->select('BillItemRef,calibrated_tanks.amount,PayCntrNum,bill_items.CreatedAt')
            ->where($params)
            ->where('MONTH(wma_bill.CreatedAt) BETWEEN ' . $monthFrom . ' AND ' . $monthTo . '')


            ->orderBy('wma_bill.CreatedAt', 'ASC')
            ->join('users', 'users.unique_id = bill_items.UserId')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('calibrated_tanks', 'calibrated_tanks.id = bill_items.BillItemRef')
            ->get()
            ->getResult();
    }


    ####################################################################
    ##########################WITHIN A MONTH ##########################
    ##################################################################
    public function dataReport($params)
    {
        return $this->billedItems
            ->select('PaymentStatus,users.first_name as fName,users.last_name as lName')
            ->select('customers.name,customers.phone_number,calibrated_tanks.vehicle_brand,calibrated_tanks.trailer_plate_number,calibrated_tanks.capacity,calibrated_tanks.amount as vehicle_amount')
            ->select('BillItemRef,calibrated_tanks.amount,PayCntrNum,bill_items.CreatedAt')
            ->where($params)
            ->orderBy('wma_bill.CreatedAt', 'ASC')
            ->join('users', 'users.unique_id = bill_items.UserId')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('calibrated_tanks', 'calibrated_tanks.id = bill_items.BillItemRef')
            ->get()
            ->getResult();
    }

    public function collectionSum($params)
    {
        return $this->billedItems
            ->selectSum('calibrated_tanks.amount')

            ->where($params)
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('calibrated_tanks', 'calibrated_tanks.id = bill_items.BillItemRef')
            ->get()
            ->getResult();
    }

    public function dateRangeReport($params, $dateFrom, $dateTo)
    {
        return $this->billedItems
            ->select('PaymentStatus,users.first_name as fName,users.last_name as lName')
            ->select('customers.name,customers.phone_number,calibrated_tanks.vehicle_brand,calibrated_tanks.trailer_plate_number,calibrated_tanks.capacity,calibrated_tanks.amount as vehicle_amount')
            ->select('BillItemRef,calibrated_tanks.amount,PayCntrNum,bill_items.CreatedAt')
            ->where($params)
            ->where(['wma_bill.CreatedAt >=' => $dateFrom])
            ->where(['wma_bill.CreatedAt <=' => $dateTo])
            ->orderBy('wma_bill.CreatedAt', 'ASC')
            ->join('users', 'users.unique_id = bill_items.UserId')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('calibrated_tanks', 'calibrated_tanks.id = bill_items.BillItemRef')
            ->get()
            ->getResult();
    }





    //=================SEARCHING FUNCTION====================
    public function searchingVtc()
    {
        return $this->billedItems

            ->select('
       calibrated_tanks.id,
       calibrated_tanks.activity,
       calibrated_tanks.region,
       calibrated_tanks.date_created,
       calibrated_tanks.next_calibration,
       calibrated_tanks.tin_number,
      
       calibrated_tanks.driver_name,
       calibrated_tanks.driver_license,
       calibrated_tanks.vehicle_brand,
       calibrated_tanks.trailer_plate_number,
       calibrated_tanks.capacity,
       calibrated_tanks.PaymentStatus,
       calibrated_tanks.sticker_number,
       calibrated_tanks.amount,
       calibrated_tanks.other_charges,
       calibrated_tanks.remark,


       PaymentStatus,users.first_name as officerFirstName,
       users.last_name as officerLastName



       ')

            ->select('customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number,calibrated_tanks.vehicle_brand')
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('calibrated_tanks', 'calibrated_tanks.id = bill_items.BillItemRef', 'right')
            ->join('users', 'users.unique_id = bill_items.UserId')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->get()
            ->getResultArray();
    }

    //=================get vehicle after searching====================
    public function vehicleMatch($id)
    {
        return $this->calibratedTanks
            ->select(
                '

       calibrated_tanks.activity,
       calibrated_tanks.region,
       calibrated_tanks.registration_date,
       calibrated_tanks.next_calibration,
       calibrated_tanks.tin_number,
      
       calibrated_tanks.driver_name,
       calibrated_tanks.driver_license,
       calibrated_tanks.vehicle_brand,
       calibrated_tanks.trailer_plate_number,
       calibrated_tanks.capacity,
       calibrated_tanks.PaymentStatus,
       calibrated_tanks.sticker_number,
       calibrated_tanks.amount,
       calibrated_tanks.other_charges,
       calibrated_tanks.remark,
       bill_items.PayCntrNum,
       


       PaymentStatus,users.first_name as officerFirstName,
       users.last_name as officerLastName

        '
            )
            ->select('customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number,calibrated_tanks.vehicle_brand')
            ->where(['calibrated_tanks.id' => $id])
            ->join('bill_items', 'calibrated_tanks.id = bill_items.BillItemRef')
            ->join('customers', 'customers.hash = calibrated_tanks.hash')
            ->join('users', 'users.unique_id = calibrated_tanks.unique_id')
            ->get()
            ->getRow();
    }


    public function vtvCount($params)
    {
        $builder = $this->calibratedTanks->select('vehicle_brand,amount,created_at')

            // ->where([
            //     'calibrated_tanks.created_at >=' => financialYear()->startDate,
            //     'calibrated_tanks.created_at <=' => financialYear()->endDate
            // ])
            ->where(['deletedAt' => null, 'status' => 'Pass'])
            ->where($params)

            // ->countAllResults();
            ->get()->getResult();


        if (!empty($builder)) {
            $quantity = count($builder);
            $amount = (new ArrayLibrary($builder))->map(fn($item) => (int)$item->amount ?? 1)->reduce(fn($x, $y) => $x + $y)->get();
        } else {
            $quantity = 0;
            $amount = 0;
        }

        return (object) [
            'quantity' => $quantity,
            'amount' => $amount,
        ];
    }
    public function vtvInspection($params)
    {
        return $this->vehicle->select('collection_center,task,visualInspection,testing,vehicle_tanks.created_at')
            ->join('users', 'users.unique_id = vehicle_tanks.unique_id')

            ->where($params)

            // ->countAllResults();
            ->get()->getResult();
    }

    public function nextVerification($currentDate, $verificationDate)
    {
        return $this->calibratedTanks
            ->select('
        calibrated_tanks.id,    
        name,
        phone_number as phoneNumber,
        gfCode as activity,
        "calibrated_tanks" as table,
        CONCAT("Trailer" ," ", trailer_plate_number) as item,
        next_calibration as nextVerification
       ')
            ->join('customers', 'customers.hash = calibrated_tanks.hash')
            ->where('notified', 0)
            ->where('next_calibration >=', $currentDate)
            ->where('next_calibration <=', $verificationDate)
            ->get()->getResult();
    }

    public function checkPlateNumber($plateNumber)
    {
        return $this->calibratedTanks
            ->select()
            ->where(['trailer_plate_number' => $plateNumber])
            // ->orWhere(['hose_plate_number' => $plateNumber])
            ->get()
            ->getRow();
    }
}
