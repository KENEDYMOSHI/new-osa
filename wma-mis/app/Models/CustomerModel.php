<?php

namespace App\Models;

use DateTime;
use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $customersTable;
    protected $db;
    protected $billTable;
    protected $tinTable;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->customersTable = $this->db->table('customers');
        $this->billTable = $this->db->table('wma_bill');
        $this->tinTable = $this->db->table('tin_info');
    }

    public function sqlMode()
    {
        $this->db->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
    }

    public function storeTinData($data)
    {
        return $this->tinTable->insert($data);
    }
    public function getTinData($tin)
    {
        return $this->tinTable->select()->where('tin', $tin)->get()->getRow();
    }


    //creating new customer
    public function createCustomer($data)
    {
        return $this->customersTable->insert($data);
    }
    //update customer
    public function updateCustomer($hash, $data)
    {
        return $this->customersTable
            ->where(['hash' => $hash])
            ->update($data);
    }
    //searching existing customer
    public function searchCustomer($keyword)
    {

        return $this->customersTable->select()
            ->like(['name' => $keyword])
            ->orLike(['phone_number' => $keyword])
            ->get()
            ->getResult();
    }
    //get single Customer customer
    public function selectCustomer($hash)
    {
        return $this->customersTable
            ->select()
            ->where(['hash' => $hash])
            ->get()
            ->getRow();
    }
    //get all Customer customer
    public function selectAllCustomers()
    {
        return $this->customersTable
            ->select()
            ->get()
            ->getResult();
    }
    //get all Customer customer
    public function getCustomers($activities, $params)
    {
        return $this->billTable
            ->select('customers.*,centerName,Activity')
            ->where($params)
            // ->whereIn('Activity', $activities)
            ->join('customers', 'customers.hash = wma_bill.PyrId', 'right')
            ->join('collectioncenter', 'collectioncenter.centerNumber = wma_bill.collectioncenter')
            // ->groupBy('hash')
            ->get()
            ->getResult();
    }




    // public function getPrepackageCustomers($activities, $params)
    // {
    //     return $this->billTable
    //         ->select('customers.*,centerName,commodity,BillGenDt as verificationDate')
    //         ->where($params)
    //         ->join('customers', 'customers.hash = wma_bill.PyrId','right')
    //         ->join('collectioncenter', 'collectioncenter.centerNumber = wma_bill.collectioncenter')
    //         ->join('product_details', 'product_details.hash = customers.hash')
    //         ->groupBy('customers.hash')
    //         ->orderBy('customers.name','ASC')
    //         ->get()
    //         ->getResult();
    // }

    public function getPrepackageCustomers($params, $name = '')
    {
        $this->sqlMode();
        $customers = $this->billTable
            ->select('customers.*, centerName, centerNumber, BillGenDt as verificationDate')
            ->join('customers', 'customers.hash = wma_bill.PyrId', 'right')
            ->join('collectioncenter', 'collectioncenter.centerNumber = wma_bill.CollectionCenter')
            ->join('product_details', 'product_details.hash = customers.hash')
            ->where($params);


        if ($name !== '') {
            $customers->like('customers.name', $name);
        }

        $customers = $customers
            ->orderBy('customers.name', 'ASC')
            ->groupBy('product_details.hash', 'ASC')
            ->get()
            ->getResult();

        $now = new DateTime();

        foreach ($customers as $customer) {
            $customer->Activity = 'Pre Package';
            $verificationDate = new DateTime($customer->verificationDate);
            $nextVerification = $verificationDate->modify('+1 year');
            $customer->nextVerification = $nextVerification->format('Y-m-d');

            if ($now < $nextVerification) {
                $customer->verificationStatus = 'Valid';
            } else {
                $customer->verificationStatus = 'Not Valid';
            }
        }

        return $customers;
    }

    public function getVtvCustomers($params, $name = '')
    {
        $this->sqlMode();
        $customers = $this->billTable
            ->select('customers.*, centerName,centerNumber, registration_date as verificationDate, next_calibration as nextVerification')
            ->join('customers', 'customers.hash = wma_bill.PyrId', 'right')
            ->join('collectioncenter', 'collectioncenter.centerNumber = wma_bill.CollectionCenter')
            ->join(' calibrated_tanks ', ' calibrated_tanks.hash= customers.hash')
            ->where($params);
        if ($name !== '') {
            $customers->like('customers.name', $name);
        }

        $customers = $customers
            ->orderBy('customers.name', 'ASC')
            ->groupBy('calibrated_tanks.hash', 'ASC')
            ->get()
            ->getResult();


        $now = new DateTime();

        foreach ($customers as $customer) {
            $customer->Activity = 'Vehicle Tank Verification';

            if ($now->format('Y-m-d') <  $customer->nextVerification) {
                $customer->verificationStatus = 'Valid';
            } else {
                $customer->verificationStatus = 'Not Valid';
            }
        }


        return $customers;
    }
    public function getSblCustomers($params, $name = '')
    {
        $this->sqlMode();
        $customers = $this->billTable
            ->select('customers.*, centerName,centerNumber, registration_date as verificationDate, next_calibration as nextVerification')
            ->join('customers', 'customers.hash = wma_bill.PyrId', 'right')
            ->join('collectioncenter', 'collectioncenter.centerNumber = wma_bill.CollectionCenter')
            ->join(' verified_lorries ', ' verified_lorries.hash= customers.hash')
            ->where($params);
        if ($name !== '') {
            $customers->like('customers.name', $name);
        }

        $customers = $customers
            ->orderBy('customers.name', 'ASC')
            ->groupBy('verified_lorries.hash', 'ASC')
            ->get()
            ->getResult();


        $now = new DateTime();

        foreach ($customers as $customer) {
            $customer->Activity = 'Sandy & Ballast Lorries';

            if ($now->format('Y-m-d') <  $customer->nextVerification) {
                $customer->verificationStatus = 'Valid';
            } else {
                $customer->verificationStatus = 'Not Valid';
            }
        }


        return $customers;
    }

    public function getWaterMeterCustomers($params, $name = '')
    {
        $this->sqlMode();
        $customers = $this->billTable
            ->select('customers.*, centerName,centerNumber, BillGenDt as verificationDate')
            ->join('customers', 'customers.hash = wma_bill.PyrId', 'right')
            ->join('collectioncenter', 'collectioncenter.centerNumber = wma_bill.CollectionCenter')
            ->join('water_meters', 'water_meters.hash= customers.hash')
            ->where($params);
        if ($name !== '') {
            $customers->like('customers.name', $name);
        }

        $customers = $customers
            ->orderBy('customers.name', 'ASC')
            ->groupBy('water_meters.hash', 'ASC')
            ->get()
            ->getResult();


        $now = new DateTime();

        foreach ($customers as $customer) {
            $customer->Activity = 'Water Meter';
            $verificationDate = new DateTime($customer->verificationDate);
            $nextVerification = $verificationDate->modify('+1 year');
            $customer->nextVerification = $nextVerification->format('Y-m-d');

            if ($now < $nextVerification) {
                $customer->verificationStatus = 'Valid';
            } else {
                $customer->verificationStatus = 'Not Valid';
            }
        }


        return $customers;
    }


    public function filterCustomerLocation($params, $name)
    {
        $builder = $this->billTable;
        $builder->select('customers.*,centerName,Activity');
        if ($name != '') $builder->like('customers.name', $name);
        $builder->where($params);
        $builder->where(['customers.latitude!=' => '']);
        $builder->where(['customers.longitude!=' => '']);
        $builder->join('customers', 'customers.hash = wma_bill.PyrId', 'left');
        $builder->join('collectioncenter', 'collectioncenter.centerNumber = wma_bill.collectioncenter');
        // $builder->groupBy('hash');
        return $builder->get()->getResult();

        // return $builder;
    }

    //get latest inserted customer hash 
    public function lastHash()
    {

        return $this->customersTable
            ->select('id,hash')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getResult();
    }
    public function getData($params)
    {

        return $this->customersTable
            ->select('name,region,created_at')
            ->where($params)
            ->get()
            ->getResult();
    }
}
