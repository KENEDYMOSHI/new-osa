<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class PrePackageModel extends Model
{
  protected $db;
  protected $prePackageTable;
  protected $measurementTable;
  protected $customersTable;
  protected $productDetailsTable;
  protected $productTest;
  protected $billedItems;
  protected $imported;

  public function __construct()
  {
    $this->db = \Config\Database::connect();
    $this->prePackageTable = $this->db->table('prepackage');
    $this->billedItems = $this->db->table('bill_items');
    $this->measurementTable = $this->db->table('measurement_sheet');
    $this->productDetailsTable = $this->db->table('product_details');
    $this->productTest = $this->db->table('producttest');
    $this->customersTable = $this->db->table('customers');
    $this->imported = $this->db->table('ppg_imported');
    // $this->db->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'"); 
  }
  public function prePackageColumns()
  {
    return '
        customers.hash,
        name,
        location,
        physical_address,
        postal_address,
        customers.region,
        customers.phone_number,
        ref_number,
        batch_number,
        BillItemRef,
        bill_items.BillItemAmt as billAmount,
        product_details.hash,
        commodity,
        quantity,
        unit,
        lot,
        sample_size,
        gross_quantity,
        prepackage.product_id,
        prepackage.amount,
        users.first_name as fName,users.last_name as lName,
        prepackage.hash,
        prepackage.created_at,
        PaymentStatus,
        PayCntrNum
      
      ';
  }





  public function saveBilledProducts($data)
  {
    return $this->prePackageTable->insertBatch($data);
  }
  public function createPrePackageBill($data)
  {
    return $this->billedItems->insertBatch($data);
  }


  public function getUnpaidProducts($hash, $ids)
  {
    return $this->productDetailsTable
      ->select()
      ->where(['hash' => $hash])
      ->whereNotIn('id', $ids)
      ->get()
      ->getResult();
  }
  public function getUnverifiedProducts($ids, $hash)
  {
    return $this->productDetailsTable
      ->select()
      ->where(['verified' => 0, 'hash' => $hash])
      //->whereNotIn('id', $ids)
      ->get()
      ->getResult();
  }
  public function getPaidProducts($hash)
  {
    return $this->prePackageTable
      ->select()
      ->where(['prepackage.hash' => $hash])
      ->join('product_details', 'product_details.id = prepackage.product_id')
      ->get()
      ->getResult();
  }

  public function getBilledProducts($hash)
  {
    return $this->billedItems
      ->select('BillItemRef')
      ->where(['PayerId' => $hash])
      ->get()
      ->getResult();
  }
  public function getTheProducts($hash)
  {
    return $this->prePackageTable
      // ->select('
      //  product_details.id,
      //  product_details.hash,
      //  product_details.commodity,
      //  product_details.quantity,
      //  product_details.unit,
      //  product_details.analysis_category,
      //  product_details.method,
      //  product_details.packing_declaration,
      //  product_details.measurement_unit,
      //  product_details.sampling,
      //  product_details.measurement_nature,
      //  product_details.tare,
      //  product_details.product_nature,
      //  product_details.density,
      //  product_details.gross_quantity,
      //  product_details.sample_size,
      //  product_details.unique_id,
      //  product_details.created_at,

      // ')

      ->select()
      ->where(['prepackage.hash' => $hash])
      ->join('product_details', 'product_details.id = prepackage.product_id ')
      ->get()
      ->getResult();
  }
  public function getRegionalPrepackedData($params)
  {
    return $this->customersTable
      ->select($this->prePackageColumns())
      ->where($params)
      ->join('prepackage', 'prepackage.hash = customers.hash')
      ->join('product_details', 'product_details.id = prepackage.product_id')
      ->join('bill_items', 'bill_items.BillItemRef = prepackage.product_id')
      ->get()
      ->getResult();
  }

  public function prePackageData($id)
  {
    return $this->prePackageTable

      ->select($this->prePackageColumns())
      ->where(['prepackage.unique_id' => $id])
      ->orderBy('bill_items.CreatedAt', 'DESC')
      ->join('users', 'users.unique_id = prepackage.unique_id')
      ->join('customers', 'customers.hash = prepackage.hash')
      ->join('product_details', 'product_details.id = prepackage.product_id')
      ->join('bill_items', 'bill_items.BillItemRef = prepackage.product_id')
      ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
      ->get()
      ->getResult();
  }
  public function getPrePackageData($params)
  {
    return $this->billedItems
      ->select('
    wma_bill.BillId,,
    BillItemRef,
    ItemName,
    PayCntrNum,
    PyrName,
    BillAmt as amount,
    BillGenBy,
    PaymentStatus,
    wma_bill.CreatedAt,
    CenterNumber,
    CenterName
    ')
      ->where($params)
      ->where(['wma_bill.Activity' => 'prepackage'])
      ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
      ->join('prepackage', 'prepackage.product_id = bill_items.BillItemRef')
      ->join('collectioncenter', 'collectioncenter.CenterNumber = wma_bill.CollectionCenter')

      ->get()
      ->getResult();
  }
  public function prePackageDataRegion($region)
  {
    return $this->prePackageTable

      ->select($this->prePackageColumns())
      ->where(['customers.region' => $region])
      ->orderBy('bill_items.CreatedAt', 'DESC')
      ->join('users', 'users.unique_id = prepackage.unique_id')
      ->join('customers', 'customers.hash = prepackage.hash')
      ->join('product_details', 'product_details.id = prepackage.product_id')
      ->join('bill_items', 'bill_items.BillItemRef = prepackage.product_id')
      ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
      ->get()
      ->getResult();
  }



  //QUARTER REPORT
  public function prePackageQuarterReport($params, $monthFrom, $monthTo)
  {
    return $this->prePackageTable

      ->select($this->prePackageColumns())
      ->where($params)
      ->where('MONTH(prepackage.created_at) BETWEEN ' . $monthFrom . ' AND ' . $monthTo . '')
      ->orderBy('prepackage.created_at', 'ASC')
      ->join('users', 'users.unique_id = prepackage.unique_id')
      ->join('customers', 'customers.hash = prepackage.hash')
      ->join('product_details', 'product_details.id = prepackage.product_id')
      ->join('bill_items', 'bill_items.BillItemRef = prepackage.product_id')
      ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
      ->get()
      ->getResult();
  }
  //MONTHLY REPORT
  public function dataReport($params)
  {
    return $this->prePackageTable

      ->select($this->prePackageColumns())
      ->where($params)
      ->orderBy('bill_items.CreatedAt', 'ASC')
      ->join('users', 'users.unique_id = prepackage.unique_id')
      ->join('customers', 'customers.hash = prepackage.hash')
      ->join('product_details', 'product_details.id = prepackage.product_id')
      ->join('bill_items', 'bill_items.BillItemRef = prepackage.product_id')
      ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
      ->get()
      ->getResult();
  }
  //DATE RANGE REPORT
  public function dateRangeReport($params, $dateFrom, $dateTo)
  {
    return $this->prePackageTable

      ->select($this->prePackageColumns())
      ->where($params)
      ->where(['bill_items.CreatedAt >=' => $dateFrom])
      ->where(['bill_items.CreatedAt <=' => $dateTo])
      ->orderBy('bill_items.CreatedAt', 'ASC')
      ->join('users', 'users.unique_id = prepackage.unique_id')
      ->join('customers', 'customers.hash = prepackage.hash')
      ->join('product_details', 'product_details.id = prepackage.product_id')
      ->join('bill_items', 'bill_items.BillItemRef = prepackage.product_id')
      ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
      ->get()
      ->getResult();
  }

  public function addCustomer($data)
  {

    return $this->customersTable->insert($data);
  }

  public function addProductDetails($data)
  {

    return $this->productDetailsTable->insert($data);
  }
  public function getProducts($hash)
  {

    return $this->productDetailsTable->select()->where(['hash' => $hash])->orderBy('id', 'DESC')->get()->getResult();
  }
  public function getImported($params)
  {

    return $this->billedItems->select('
    PyrName as customer,
    bill_items.BillId,
    wma_bill.BillId,
    PyrCellNum as phoneNumber,
    PayCntrNum as controlNumber,
    BillItemAmt as amount,
    PaymentStatus,
    tansardNumber,
    ItemName as product,
    fob,
    Status,
    center,
    date,
    CollectionCenter,
    bill_items.createdAt,
   ')
   ->where($params)
        ->join('wma_bill', 'wma_bill.BillId = bill_items.BillId')
       //->limit(1500)
        ->get()->getResult();
  }
  public function getMeasuredProducts($hash)
  {

    return $this->productDetailsTable
      ->select('product_details.*,quantity_id')
      ->where(['hash' => $hash])
      ->orderBy('id', 'DESC')
      ->join('measurement_sheet', 'measurement_sheet.product_id = product_details.id', 'left')
      ->get()
      ->getResult();
  }
  public function collectionSum($params)
  {
    return $this->billedItems
      ->selectSum('prepackage.amount')

      ->where($params)
      ->join('customers', 'customers.hash = bill_items.PayerId')
      ->join('prepackage', 'prepackage.id = bill_items.BillItemRef')
      ->get()
      ->getResult();
  }

  public function lastHash()
  {

    return $this->customersTable->select('id,hash')->orderBy('id', 'DESC')->limit(1)->get()->getResult();
  }

  public function searchCustomer($keyword)
  {

    return $this->customersTable->select()
      ->like(['name' => $keyword])
      ->orLike(['phone_number' => $keyword])
      ->get()
      ->getResult();
  }
  public function getCustomerInfo($hash)
  {

    return $this->customersTable->select()
      ->where(['hash' => $hash])
      ->get()
      ->getRow();
  }
  public function getCustomerProducts($params)
  {
    try {
      return $this->productDetailsTable->select(
        'id,
      hash,
      activity,
      task,
      commodity,
      quantity,
      unit,
      sample_size,
      lot,
      gross_quantity,
      created_at

      '


      )
        // ->where($params)
        ->where($params)
        ->get()
        ->getResult();
    } catch (\Exception $e) {
      echo ($e->getMessage());
    }
  }


  //=================Inserting measurement sheet data====================
  public function addMeasurementSheetData($data)
  {

    $builder = $this->db->table('measurement_sheet');


    return $builder->insertBatch($data);
  }
  public function checkQuantityId($id)
  {

    return $this->measurementTable
      ->select()
      ->where('quantity_id', $id)
      ->limit(1)
      ->get()
      ->getResult();
  }

  //=================grabbing measurement sheet data====================
  public function getProductsWithMeasurements($ids, $hash)
  {

    return $this->measurementTable
      ->select('product_details.id,task,measurement_sheet.product_id,product_details.hash,commodity,lot,quantity,unit,activity,product_details.gross_quantity,product_details.sample_size ,type,fob,date,tansard_number')
      ->where(['product_details.hash' => $hash])
      ->whereIn('measurement_sheet.product_id', $ids)
      // ->whereNotIn('prepackage.product_id', $ids)
      // ->join('prepackage', 'prepackage.product_id = measurement_sheet.product_id', 'right')
      ->join('product_details', 'product_details.id = measurement_sheet.product_id')
      ->get()
      ->getResult();
  }


  public function verifiedProducts($params)
  {
    $verifiedProducts = [];

    // Fetch all products matching the given parameters
    $ppg1 = $this->productDetailsTable->select('id')->where($params)->get()->getResult();
    $ppg2 = $this->prePackageTable->select('product_id as id')
      ->where(['product_details.hash' => $params['hash'], 'product_details.verified' => 0])
      ->join('product_details', 'product_details.id = prepackage.product_id')
      ->get()->getResult();

    $products = array_merge($ppg1, $ppg2);
    foreach ($products as $product) {
      // Check if there is at least one measurement record associated with the product
      $isVerified = $this->measurementTable->select('product_id')->where('product_id', $product->id)->limit(1)->get()->getResult();

      if ($isVerified) {
        // If at least one measurement record exists, add the product ID to the verified products array
        $verifiedProducts[] = $product->id;
      }
    }

    return array_unique($verifiedProducts);
  }





  public function grabProducts($ids, $hash)
  {

    return $this->prePackageTable
      ->select()
      ->where(['hash' => $hash])
      ->whereIn('product_id', $ids)
      ->get()
      ->getResult();
  }
  public function getAllProducts($hash)
  {

    return $this->productDetailsTable
      ->select()
      // ->where(['hash' => $hash])
      ->get()
      ->getResult();
  }
  public function fetchProducts($ids, $hash)
  {



    return $this->productDetailsTable
      ->select()
      ->where(['product_details.hash' => $hash])
      ->whereIn('product_details.id', $ids)

      ->get()
      ->getResult();
  }

  public function products($ids)
  {
    return $this->productDetailsTable
      ->select(
        "CONCAT(commodity, ', ', quantity, ', ', unit) AS product"
      )
      ->whereIn('product_details.id', $ids)
      ->get()
      ->getResult();
  }



  //=================grabbing measurement sheet data====================
  public function getMeasurementData($params)
  {


    $builder = $this->db->table('measurement_sheet');

    return $builder
      ->select()
      ->where($params)
      //->where(['quantity_id' => $measurementId])
      ->get()
      ->getResult();
  }
  public function selectProduct($id)
  {

    return $this->productDetailsTable->select()
      ->where(['id' => $id])
      ->get()
      ->getRow();
  }

  public function getprepackage($id)
  {

    return $this->prePackageTable
      ->select()
      ->select('customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number')
      ->where(['unique_id' => $id])
      ->join('customers', 'customers.hash = prepackage.hash')
      ->get()
      ->getResult();
  }
  public function getAllPrePackages($region)
  {

    return $this->prePackageTable
      ->select()
      ->select('customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number')
      ->where(['region' => $region])
      ->join('customers', 'customers.hash = prepackage.hash')
      ->get()
      ->getResult();
  }

  public function deleteRecord($id)
  {
    $this->prePackageTable
      ->where(['id' => $id])
      ->delete();
  }


  public function editRecord($id)
  {
    return $this->prePackageTable
      ->select()
      ->select('customers.name,customers.region,customers.district,customers.ward,customers.village,customers.postal_address,customers.phone_number')
      ->where(['id' => $id])
      ->join('customers', 'customers.hash = prepackage.hash')
      ->get()
      ->getRow();
  }

  public function updateIndustrialPackage($data, $id)
  {


    return $this->prePackageTable
      ->set($data)
      ->where(['id' => $id])
      ->update();
  }
  public function updateProducts($ids, $data)
  {


    return $this->productDetailsTable
      ->set($data)
      ->whereIn('id', $ids)
      ->update();
  }

  public function prePackageDetails($location)
  {
    return $this->prePackageTable
      ->select('bill_items.amount')
      ->where(['customers.region' => $location])
      ->join('customers', 'customers.hash = prepackage.hash')
      ->join('bill_items', 'bill_items.BillItemRef = prepackage.product_id')
      ->get()
      ->getResultArray();
  }
  // ================Get all details in all regions dts==============
  public function getAllInRegion($region)
  {
    return $this->prePackageTable

      ->select($this->prePackageColumns())
      ->where(['customers.region' => $region])
      ->orderBy('prepackage.created_at', 'ASC')
      ->join('users', 'users.unique_id = prepackage.unique_id')
      ->join('customers', 'customers.hash = prepackage.hash')
      ->join('product_details', 'product_details.id = prepackage.product_id')
      ->join('bill_items', 'bill_items.BillItemRef = prepackage.product_id')
      ->get()
      ->getResult();
  }
  // ================Full details on  activity==============
  public function activityFullDetails()
  {
    return $this->prePackageTable
      // ->where(['region' => $location])
      ->select('amount')
      ->get()
      ->getResultArray();
  }
  // ================Api==============
  public function getData($region)
  {
    return $this->prePackageTable
      ->select()
      ->where(['customers.region' => $region])
      ->join('customers', 'customers.hash = prepackage.hash')
      ->get()
      ->getResult();
  }

  // ================Data for Api==============
  public function getFullData()
  {
    return $this->prePackageTable
      ->select('customers.date,amount')
      // ->where(['region' => $region])
      ->join('customers', 'customers.hash = prepackage.hash')
      ->get()
      ->getResult();
  }

  public function nextVerification($currentDate, $verificationDate)
  {
    return $this->prePackageTable
      ->select('
    prepackage.id,
    name,
    phone_number as phoneNumber,
    gfCode as activity,
    "prepackage" as table,
    nextVerification
   ')
      ->join('customers', 'customers.hash = prepackage.hash')
      ->where('notified', 0)
      ->where('nextVerification >=', $currentDate)
      ->where('nextVerification <=', $verificationDate)
      ->groupBy('prepackage.hash')
      ->get()
      ->getResult();
  }
}
