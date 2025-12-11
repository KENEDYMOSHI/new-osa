<?php

namespace App\Models;

use CodeIgniter\Model;

class SearchModel extends Model
{
    protected $billTable;
    protected $db;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->billTable = $this->db->table('bill');
    }


    //creating new customer
    public function createBill($data)
    {
        return $this->billTable->insertBatch($data);
    }
    //update customer
    public function updateBill($hash, $data)
    {
        return $this->billTable
            ->where(['hash' => $hash])
            ->update($data);
    }
    public function selectItem($identifier, $activity)
    {
        $theTable = '';
        $id = '';

        $items = '';
        $columns = '';



        switch ($activity) {
          

            case setting('Gfs.vtv'):
                $builder = $this->db->table('calibrated_tanks');
                $theTable .= 'calibrated_tanks';

                $items .= "vehicle_brand,hose_plate_number,trailer_plate_number,CONCAT(capacity, ' ','Liters')";
                $columns .= 'sticker_number as stickerNumber,DATE_FORMAT(registration_date,"%d %M %Y") as calibratedOn,DATE_FORMAT(next_calibration,"%d %M %Y") as nextCalibration,';
                $builder->join('bill_items', "bill_items.BillItemRef = calibrated_tanks.id");
                $builder->join('collectioncenter', "collectioncenter.centerNumber = calibrated_tanks.region");
                break;
            case setting('Gfs.sbl'):
                $theTable .= 'verified_lorries';
                $builder = $this->db->table($theTable);
                $id .= 'id';
                $items .= "vehicle_brand,plate_number,CONCAT(capacity, ' ','m<sup>3</sup>')";
                $columns .= 'sticker_number as stickerNumber,DATE_FORMAT(registration_date,"%d %M %Y") as calibratedOn,DATE_FORMAT(next_calibration,"%d %M %Y") as nextCalibration,';
                $builder->join('bill_items', "bill_items.BillItemRef = verified_lorries.id");
                $builder->join('collectioncenter', "collectioncenter.centerNumber = verified_lorries.region");
                break;
            case '142101210026':
                $theTable .= 'water_meters';
                $builder = $this->db->table($theTable);
                $id .= 'id';
                $items .= "brand,CONCAT(flow_rate, ' ','m<sup>3</sup>/h'),CONCAT(quantity,' ','Meters')";
                break;

            default:
              return [];
                break;
        }

        $builder->join('customers', "customers.hash = $theTable.hash",'left');
        // $builder->join('bill_items', "bill_items.BillItemRef = $theTable.id");
        $builder->join('wma_bill', 'wma_bill.BillId = bill_items.BillId');
        $builder->join('users', "users.unique_id = $theTable.unique_id",'left');
        // $builder->join('collectioncenter', "collectioncenter.centerNumber = users.collection_center");

        $builder->where(["$theTable.id" => $identifier]);
        // $builder->where(['PaymentStatus' => 'pending']);

        $builder->select(
            "$theTable.hash,
     wma_bill.BillAmt as total,
      wma_bill.PaidAmount as paid,
      name,
      customers.phone_number as phoneNumber,
      customers.region,
      PayCntrNum as controlNumber,
      PaymentStatus as paymentStatus,
      $columns
      $theTable.amount,
       IF(DATEDIFF(CURRENT_DATE(), next_calibration) < 0, 'Valid', 'Not Valid') AS calibrationStatus,
      CONCAT_WS(' ',$items) as item,
      CONCAT(users.first_name,' ',users.last_name) as verifiedBy,
      centerName as region
      "
        );
        // $builder->select();


        return $builder->get()->getRow();
    }






    //get single Item 
    public function searchItem($keyword, $activity)
    {
        $theTable = '';
        $id = '';

        $items = '';
        $columns = '';


        switch ($activity) {
          

            case setting('Gfs.vtv'):
                $builder = $this->db->table('calibrated_tanks');
                $theTable .= 'calibrated_tanks';

                $items .= "vehicle_brand,hose_plate_number,trailer_plate_number,CONCAT(capacity, ' ','Liters')";
                $columns .= 'sticker_number as stickerNumber,DATE_FORMAT(registration_date,"%d %M %Y") as calibratedOn,DATE_FORMAT(next_calibration,"%d %M %Y") as nextCalibration,';
                $builder->join('bill_items', "bill_items.BillItemRef = calibrated_tanks.id",'left');
                break;
            case setting('Gfs.sbl'):
                $theTable .= 'verified_lorries';
                $builder = $this->db->table($theTable);
                $id .= 'id';
                $items .= "vehicle_brand,plate_number,CONCAT(capacity, ' ','m<sup>3</sup>')";
                $columns .= 'sticker_number as stickerNumber,DATE_FORMAT(registration_date,"%d %M %Y") as calibratedOn,DATE_FORMAT(next_calibration,"%d %M %Y") as nextCalibration,';
                $builder->join('bill_items', "bill_items.BillItemRef = verified_lorries.id",'left');
                break;
            case '142101210026':
                $theTable .= 'water_meters';
                $builder = $this->db->table($theTable);
                $id .= 'id';
                $items .= "brand,CONCAT(flow_rate, ' ','m<sup>3</sup>/h'),CONCAT(quantity,' ','Meters')";
                break;

            default:
              return [];
                break;
                exit;
        }

        $builder->where(["$theTable.testing" => 'Pass']);
        $builder->where(["$theTable.visualInspection" => 'Pass']);
        $builder->join('wma_bill', "wma_bill.BillId = bill_items.BillId",'left');
        $builder->join('users', "users.unique_id = $theTable.unique_id",'left');
        $builder->join('collectioncenter', "collectioncenter.centerNumber = users.collection_center");

        $builder->join('customers', "customers.hash = $theTable.hash",'left');

        $builder->like(['customers.name' => $keyword]);
        $builder->orLike(['customers.phone_number' => $keyword]);
        if ($activity == setting('Gfs.vtv')) {
            $builder->orLike(['hose_plate_number' => $keyword])->orLike(['trailer_plate_number' => $keyword])->orLike(['sticker_number' => $keyword]);
        } else if ($activity == setting('Gfs.sbl')) {
            $builder->orLike(['plate_number' => $keyword])->orLike(['sticker_number' => $keyword]);
        }

       
        // $builder->where(['PaymentStatus' => 'pending']);

        $builder->select(
            "$theTable.hash,
            $theTable.id,
      wma_bill.BillAmt as total,
      wma_bill.PaidAmount as paid,
      name,
      customers.phone_number as phoneNumber,
      
      customers.region,
      PayCntrNum as controlNumber,
      PaymentStatus as paymentStatus,
      $columns
      $theTable.amount,
       IF(DATEDIFF(CURRENT_DATE(), next_calibration) < 0, 'Valid', 'Not Valid') AS calibrationStatus,
      CONCAT_WS(' ',$items) as item,
      CONCAT(users.first_name,' ',users.last_name) as verifiedBy,
      centerName as region
      "
        );
        // $builder->select();


        return $builder->distinct()->get()->getResult();
    }



    //get latest inserted customer hash 
    public function lastHash()
    {

        return $this->billTable
            ->select('id,hash')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getResult();
    }
}
