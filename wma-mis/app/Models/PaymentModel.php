<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $PaymentTable;
    protected $db;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->PaymentTable = $this->db->table('Payment');
    }


    //creating new customer
    public function createPayment($data)
    {
        return $this->PaymentTable->insertBatch($data);
    }
    //update customer
    public function updatePayment($hash, $data)
    {
        return $this->PaymentTable
            ->where(['hash' => $hash])
            ->update($data);
    }
    //searching existing customer
    public function searchPayment($params, $name, $activity)
    {

        $tableToJoin = '';
        $id = '';

        $items = '';



        $builder = $this->db->table('transactions');

        switch ($activity) {
            case 'prepackage':
                $id .= 'product_id';
                $tableToJoin .= 'prepackage';
                $items .= 'commodity,quantity,unit';
                $builder->join('product_details', "product_details.id = transactions.instrument_id");
                break;

            case 'vtv':
                $tableToJoin .= 'calibrated_tanks';
                $id .= 'id';
                // $items .= "vehicle_brand,' ',trailer_plate_number,' ',capacity";
                $items .= "vehicle_brand,trailer_plate_number,CONCAT(capacity, ' ','Liters')";
                break;


            case 'sbl':
                $tableToJoin .= 'sandy_lorries_records';
                $id .= 'id';
                // $items .= "vehicle_brand,' ',trailer_plate_number,' ',capacity";
                $items .= "vehicle_brand,trailer_plate_number,CONCAT(capacity, ' ','m<sup>3</sup>')";
                break;
            case 'waterMeters':
                $tableToJoin .= 'water_meters';
                $id .= 'batch_id';
                $items .= "brand,CONCAT(flow_rate, ' ','m<sup>3</sup>/h'),CONCAT(quantity,' ','Meters')";
                break;

            default:
                # code...
                break;
        }
        $builder->join($tableToJoin, "$tableToJoin.$id = transactions.instrument_id");
        $builder->join('customers', "customers.hash = transactions.customer_hash");
        if ($name != '') $builder->like(['customers.name' => $name]);
        // $builder->like(['customers.name' => $name]);

        if (count($params) > 0) $builder->where($params);

        $builder->select(
            "$tableToJoin.hash,
      transactions.id,
      transactions.amount as total,
      name,phone_number,
      control_number,
      payment,
      $tableToJoin.amount,
      transactions.created_on,
      CONCAT_WS(' ',$items) as item
      "
        );
        // $builder->select();
        $builder->orderBy('transactions.id', 'DESC');
       // $builder->groupBy('control_number');


        return $builder->get()->getResult();
        // return $builder->getCompiledSelect();
    }






    //get single Payment 
    public function selectPayment($controlNumber, $activity)
    {
        $tableToJoin = '';
        $id = '';

        $items = '';
        $amount = '';



        $builder = $this->db->table('transactions');

        switch ($activity) {
            case 'prepackage':
                $id .= 'product_id';
                $amount .= 'amount';
                $tableToJoin .= 'prepackage';
                $items .= 'commodity,quantity,unit';
                $builder->join('product_details', "product_details.id = transactions.instrument_id");
                break;

            case 'vtv':
                $tableToJoin .= 'calibrated_tanks';
                $id .= 'id';
                $amount .= 'amount';
                $items .= "vehicle_brand,trailer_plate_number,CONCAT(capacity, ' ','Liters')";
                break;
            case 'sbl':
                $tableToJoin .= 'sandy_lorries_records';
                $id .= 'id';
                $amount .= 'amount';
                $items .= "vehicle_brand,trailer_plate_number,CONCAT(capacity, ' ','m<sup>3</sup>')";
                break;
            case 'waterMeters':
                $tableToJoin .= 'water_meters';
                $id .= 'batch_id';
                $amount .= 'total_amount';
                $items .= "brand,CONCAT(flow_rate, ' ','m<sup>3</sup>/h'),CONCAT(quantity,' ','Meters')";
                $builder->groupBy('batch_id');

                break;

            default:
                # code...
                break;
        }
        $builder->join($tableToJoin, "$tableToJoin.$id = transactions.instrument_id");
        $builder->join('customers', "customers.hash = transactions.customer_hash");
        $builder->join('users', "users.unique_id = transactions.unique_id");

        $builder->where(['control_number' => $controlNumber]);
        // $builder->where(['payment' => 'pending']);

        $builder->select(
            "$tableToJoin.hash,
      transactions.id,
      transactions.amount as total,
      transactions.paid_amount as paid,
      name,
      customers.phone_number,
      control_number,
      payment,
      $tableToJoin.$amount,
      transactions.created_on,
      transactions.updated_at as paymentDate,
      CONCAT_WS(' ',$items) as item,
      CONCAT(users.first_name,' ',users.last_name) as creator
      "
        );
        // $builder->select();


        return $builder->get()->getResult();
    }



    //get latest inserted customer hash 
    public function lastHash()
    {

        return $this->PaymentTable
            ->select('id,hash')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getResult();
    }
}
