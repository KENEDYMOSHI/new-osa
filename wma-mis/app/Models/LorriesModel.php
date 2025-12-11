<?php

namespace App\Models;

use Config\Database;
use CodeIgniter\Model;
use App\Libraries\ArrayLibrary;

class LorriesModel extends Model
{
    protected $db;
    protected $dataTable;
    protected $lorriesTable;
    protected $verifiedLorries;
    protected $billedItems;
    protected $billTable;
    protected $GfsCode;
    protected $trailers;
    public function __construct()
    {
        $this->GfsCode = setting('Gfs.sbl');
        $this->db = \Config\Database::connect();
        $this->trailers = $this->db->table('lorry_trailers');
        $this->dataTable = $this->db->table('lorries');
        $this->lorriesTable = $this->db->table('lorries');
        $this->verifiedLorries = $this->db->table('verified_lorries');
        $this->billedItems = $this->db->table('bill_items');
        $this->billTable = $this->db->table('wma_bill');
        // $this->db->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'"); 
    }

    //=================check Last id====================

    public function checkLastId()
    {
        return $this->lorriesTable
            ->select('id')
            ->orderBy('data_id', 'DESC')
            ->limit(1)
            ->get()
            ->getResult();
    }
    public function findVehicle($id)
    {
        return $this->lorriesTable
            ->select()
            ->where(['id' => $id])
            ->get()
            ->getRow();
    }
    public function checkLastIdByPlate($plateNumber)
    {
        return $this->lorriesTable
            ->select('id')
            ->where(['plate_number' => $plateNumber])
            ->orderBy('data_id', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();
    }

    public function checkLastVehicleId()
    {
        return $this->verifiedLorries
            ->select('id')
            ->orderBy('data_id', 'DESC')
            ->limit(1)
            ->get()
            ->getResult();
    }

    public function getVehicle($id)
    {
        return $this->lorriesTable
            ->select()
            ->where(['id' => $id])
            ->get()
            ->getRow();
    }

    public function grabTheLastVehicle()
    {
        return $this->lorriesTable
            ->select()
            ->orderBy('data_id', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();
    }
    //=================searching  a plate number====================
    public function findMatch($hash, $plateNumber)
    {
        return $this->lorriesTable
            ->select()
            ->where(['hash' => $hash])
            ->where(['plate_number' => $plateNumber])
            ->get()
            ->getRow();
    }
    public function registerVerifiedLorry($data)
    {

        return $this->verifiedLorries->insertBatch($data);
    }
    public function registerTrailers($data)
    {

        return  $this->trailers->insertBatch($data);
    }

    public function getTrailers($vehicleId)
    {
        return  $this->trailers->select()->where(['vehicleId' => $vehicleId])->get()->getResult();
    }

    public function saveLorryData($data)
    {

        return $this->dataTable->insert($data);
    }
    //=================Add sandyLorries====================
    public function registerLorry($data)
    {
        return $this->lorriesTable->insert($data);
    }
    //=================delete sandyLorries====================
    public function deleteLorry($vehicleId)
    {
        return $this->lorriesTable->where(['id' => $vehicleId])->delete();
    }

    //=================check all paid Lorries if exist====================
    public function filterCustomersPaidLorries($params)
    {
        return $this->verifiedLorries
            ->select('original_id')
            ->where($params)
            ->get()
            ->getResult();
    }

    public function getAllUnpaidLorries($params, $taskName)
    {
        $ids = (new AppModel())->getItemIds($params);
        return $this->lorriesTable
            ->select()
            ->where('task', $taskName)
            ->whereIn('id', $ids)
            //->join('bill_items','lorries.id = bill_items.BillItemRef')
            ->get()
            ->getResult();
    }

    //=================Publish Lorry ni transaction====================
    public function publishLorryTransaction($data)
    {

        return $this->billedItems->insertBatch($data);
    }
    public function verifiedSbl($params)
    {
        return $this->verifiedLorries
            ->select(
                "
                verified_lorries.id,
                wma_bill.BillId,
                PyrName,
                 amount,
                PaymentStatus,
                CollectionCenter,
                PyrId,
                PayCntrNum,
                PaymentStatus,
                wma_bill.Task,
                PyrCellNum,
                wma_bill.CreatedAt,
                CONCAT(first_name,' ',last_name) as officer,
                verified_lorries.id as vehicleId,
                registration_date,
                next_calibration,
                driver_name,vehicle_brand,
                 sticker_number,
                 stickers.stickerNumber,
                plate_number,
                width,
                height,
                model,
                type,
                depth,
                users.unique_id,
                driver_name,
                physical_address,
                ward,
                customers.region,
                customers.phone_number,
                capacity,
                postal_address,postal_code,tin_number,
                driver_license ,  
                IF(DATEDIFF(CURRENT_DATE(), next_calibration) < 0, 'Valid', 'Not Valid') AS calibrationStatus
               
"
            )
            //FIXME Check the calibration status logic
            ->where($params)
            ->where(['PayCntrNum !=' => ''])
            ->join('users', 'users.unique_id = verified_lorries.unique_id')
            ->join('bill_items', 'bill_items.BillItemRef = verified_lorries.id')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->join('customers', 'customers.hash = verified_lorries.hash')
            ->join('stickers','stickers.instrumentId = verified_lorries.id')
            ->get()
            ->getResult();
    }
    public function getAllLorries($region)
    {
        return $this->billedItems
            ->select()
            ->select('users.phone_number as phoneNumber ,PaymentStatus,users.first_name as firstName ,users.last_name as lastName,customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number,lorries.vehicle_brand')
            ->where(['customers.region' => $region])
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('lorries', 'lorries.id = bill_items.BillItemRef')
            ->join('users', 'users.unique_id = bill_items.PayerId')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->get()
            ->getResult();
    }
    public function getAllLorriesTz()
    {
        return $this->billedItems
            ->select()
            ->select('users.phone_number as phoneNumber ,PaymentStatus,users.first_name as firstName ,users.last_name as lastName,customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number,lorries.vehicle_brand')
            // ->where(['customers.region' => $region])
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('lorries', 'lorries.id = bill_items.BillItemRef')
            ->join('users', 'users.unique_id = bill_items.PayerId')
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
            ->join('customers', 'customers.hash = lorries.customer_hash')
            ->get()
            ->getRow();
    }

    public function updateLorry($data, $id)
    {

        return $this->lorriesTable
            ->set($data)
            ->where(['id' => $id])
            ->update();
    }
    public function updateVerifiedLorry($id ,$data)
    {

        return $this->verifiedLorries
            ->where(['id' => $id])
            ->set($data)
            ->update();
    }
    public function updateTrailers($trailers)
    {

        return $this->trailers->updateBatch($trailers, 'id');
    }

    public function sblDetails($location)
    {
        return $this->billedItems
            ->select('lorries.amount')
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('lorries', 'lorries.id = bill_items.BillItemRef')
            ->where(['customers.region' => $location])
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->get()
            ->getResult();
    }
    // ================Get all details in all regions==============
    public function getAllInRegion($location)
    {
        return $this->billedItems
            ->select('bill_items.id,lorries.amount,customers.region')
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('lorries', 'lorries.id = bill_items.BillItemRef')
            ->where(['customers.region' => $location])
            ->get()
            ->getResult();
    }
    // ================Full details on  activity==============

    // ================Api Data for a specific region==============
    public function getData($city)
    {
        return $this->billedItems
            ->select('customers.region,lorries.registration_date,lorries.amount')
            ->where(['customers.region' => $city])
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('lorries', 'lorries.id = bill_items.BillItemRef')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->get()
            ->getResult();
    }

    // ================Data for Api for entire country (DIRECTOR) ==============
    public function getFullDataForDirector()
    {
        return $this->billedItems
            ->select('lorries.amount,lorries.registration_date')
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('lorries', 'lorries.id = bill_items.BillItemRef')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
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
    public function sblQuarterReport($params, $monthFrom, $monthTo)
    {
        return $this->billedItems
            ->select('PaymentStatus,users.first_name as fName,users.last_name as lName')
            ->select('customers.name,customers.phone_number,lorries.vehicle_brand,lorries.plate_number,lorries.capacity,lorries.amount as vehicle_amount')
            ->select('BillItemRef,lorries.amount,PayCntrNum,bill_items.CreatedAt')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->where($params)
            ->where('MONTH(bill_items.CreatedAt) BETWEEN ' . $monthFrom . ' AND ' . $monthTo . '')


            ->orderBy('bill_items.CreatedAt', 'ASC')
            ->join('users', 'users.unique_id = bill_items.PayerId')
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('lorries', 'lorries.id = bill_items.BillItemRef')
            ->get()
            ->getResult();
    }


    //=================%% report for managers %%====================
    public function sblQuarterReportManager($region, $monthFrom, $monthTo, $year)
    {
        return $this->billedItems
            ->select('PaymentStatus,users.first_name as fName,users.last_name as lName')
            ->select('customers.name,customers.phone_number,lorries.vehicle_brand,lorries.plate_number,lorries.capacity,lorries.amount as vehicle_amount')
            ->select('BillItemRef,lorries.amount,PayCntrNum,bill_items.CreatedAt')
            ->where(['customers.region' => $region])
            ->where('MONTH(bill_items.CreatedAt) BETWEEN ' . $monthFrom . ' AND ' . $monthTo . '')
            ->where('YEAR(bill_items.CreatedAt) = ' . $year . '')

            ->orderBy('bill_items.CreatedAt', 'ASC')
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('users', 'users.unique_id = bill_items.PayerId')
            ->join('lorries', 'lorries.id = bill_items.BillItemRef')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->get()
            ->getResult();
    }



    ########################################################################
    ############################# MONTHLY ONLY #############################
    #######################################################################
    public function dataReport($params)
    {
        return $this->billedItems
            ->select('PaymentStatus,users.first_name as fName,users.last_name as lName')
            ->select('customers.name,customers.phone_number,lorries.vehicle_brand,lorries.plate_number,lorries.capacity,lorries.amount as vehicle_amount')
            ->select('BillItemRef,lorries.amount,PayCntrNum,bill_items.CreatedAt')
            ->where($params)
            ->orderBy('bill_items.CreatedAt', 'ASC')
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('users', 'users.unique_id = bill_items.PayerId')
            ->join('lorries', 'lorries.id = bill_items.BillItemRef')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->get()
            ->getResult();
    }

    public function collectionSum($params)
    {
        return $this->billedItems
            ->selectSum('lorries.amount')

            ->where($params)
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('lorries', 'lorries.id = bill_items.BillItemRef')
            ->get()
            ->getResult();
    }



    public function dateRangeReport($params, $dateFrom, $dateTo)
    {
        return $this->billedItems
            ->select('customers.name,customers.phone_number,lorries.vehicle_brand,lorries.plate_number,lorries.capacity,lorries.amount as vehicle_amount')
            ->select('BillItemRef,lorries.amount,PayCntrNum,bill_items.CreatedAt')
            ->select('PaymentStatus,users.first_name as fName,users.last_name as lName')
            ->where($params)
            ->where(['bill_items.CreatedAt >=' => $dateFrom])
            ->where(['bill_items.CreatedAt <=' => $dateTo])
            ->orderBy('bill_items.CreatedAt', 'ASC')
            ->join('customers', 'customers.hash = bill_items.PayerId')
            ->join('lorries', 'lorries.id = bill_items.BillItemRef')
            ->join('users', 'users.unique_id = bill_items.PayerId')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->get()
            ->getResult();
    }



    public function searchingSbl()
    {
        return $this->verifiedLorries
            ->select(
                '
          lorries.id,
         lorries.activity,
         lorries.registration_date,
         lorries.next_calibration,
         lorries.tin_number,
         lorries.supervisor,
         lorries.supervisor_phone,
         lorries.driver_name,
         lorries.driver_license,
         lorries.vehicle_brand,
         lorries.plate_number,
         lorries.capacity,
         lorries.PaymentStatus,
         lorries.sticker_number,
         lorries.amount,
         lorries.other_charges,
         lorries.remark,
         PayCntrNum,
         wma_bill.PaymentStatus,


         PaymentStatus,users.first_name as officerFirstName,
         users.last_name as officerLastName

          '
            )
            ->select('customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number,lorries.vehicle_brand')
            ->join('bill_items', 'lorries.id = bill_items.BillItemRef')
            ->join('customers', 'customers.hash = lorries.hash')
            ->join('users', 'users.unique_id = lorries.unique_id')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->get()
            ->getResultArray();
    }

    //=================get vehicle after searching====================
    public function vehicleMatch($id)
    {
        return $this->verifiedLorries
            ->select(
                '


         lorries.activity,
         lorries.registration_date,
         lorries.next_calibration,
         lorries.tin_number,
         lorries.supervisor,
         lorries.supervisor_phone,
         lorries.driver_name,
         lorries.driver_license,
         lorries.vehicle_brand,
         lorries.plate_number,
         lorries.capacity,
         lorries.PaymentStatus,
         lorries.sticker_number,
         lorries.amount,
         lorries.other_charges,
         lorries.remark,
         bill_items.PayCntrNum,
         wma_bill.PaymentStatus,


         PaymentStatus,users.first_name as officerFirstName,
         users.last_name as officerLastName

          '
            )
            ->select('customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number,lorries.vehicle_brand')
            ->where(['lorries.id' => $id])
            ->join('bill_items', 'lorries.id = bill_items.BillItemRef')
            ->join('customers', 'customers.hash = lorries.hash')
            ->join('users', 'users.unique_id = lorries.unique_id')
            ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
            ->get()
            ->getRow();
    }

    public function sblCount($params)
    {
        $builder = $this->verifiedLorries->select('amount,status,vehicle_brand,verified_lorries.created_at')
            ->where(['deletedAt' => null])
            ->where($params)
            ->get()
            ->getResult();

            // return $builder;

            if (!empty($builder)) {
                $quantity = count($builder);
                $amount = (new ArrayLibrary($builder))->map(fn ($item) => (int)$item->amount ?? 1)->reduce(fn ($x, $y) => $x + $y)->get();
            } else {
                $quantity = 0;
                $amount = 0;
            }
    
            return (object) [
                'quantity' => $quantity,
                'amount' => $amount,
            ];
    }

    public function sblInspection($params)
    {
        return $this->lorriesTable->select('collection_center,task,visualInspection,testing,lorries.created_at')
            ->join('users', 'users.unique_id = lorries.unique_id')

            ->where($params)

            // ->countAllResults();
            ->get()->getResult();
    }

    public function nextVerification($currentDate,$verificationDate){
     return $this->verifiedLorries
     ->select('
        verified_lorries.id,    
        name,
        phone_number as phoneNumber,
        gfCode as activity,
       
        "verified_lorries" as table,
        CONCAT("Gari", ," ",vehicle_brand ," ", plate_number) as item,
        next_calibration as nextVerification
       ')
            ->join('customers', 'customers.hash = verified_lorries.hash')
            ->where('notified', 0)
            ->where('next_calibration >=', $currentDate)
            ->where('next_calibration <=', $verificationDate)
            ->get()->getResult();
    }
}
