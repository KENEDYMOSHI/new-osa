<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Sky extends BaseController
{
    public function index()
    {
        helper(['bill','text']);
        $db = \Config\Database::connect();

        $params = [
            'DATE(bill_payment.CreatedAt) >=' => financialYear()->startDate,
            'DATE(bill_payment.CreatedAt) <=' => financialYear()->endDate,
        ];
        $params2 = [
            'DATE(wma_bill.CreatedAt) >=' => financialYear()->startDate,
            'DATE(wma_bill.CreatedAt) <=' => financialYear()->endDate,
        ];

        $builder = $db->table('bill_payment');
        $builder->join('wma_bill', 'wma_bill.PayCntrNum = bill_payment.PayCtrNum','left');
        $builder->select('PaidAmt as amount')->where($params)->limit(0);
        $result = $builder->get()->getResult();


        $query = $db->table('wma_bill')->select('PaidAmount as amount')
        ->where($params2)
        //->limit(2)
        ->whereIn('PaymentStatus',['Paid','Partial'])->get()->getResult();

       $total = array_sum(array_column($query, 'amount'));

       echo 'Total Amount: '. number_format( $total);



       // FROM BILL_PAYMENT 13,124,465,256.4

        //JOIN WMA_BILL
        //Total Amount: 13,124,151,256 




        //NO JOIN 
        //Total Amount: 13,124,465,256 




        //Printer($query);
    }








    public function user()
    {
        $token = csrf_hash();
        // if ($this->request->getMethod() == 'POST') {




        $uniqueId = md5(str_shuffle('abcdefghijklmnopqrstuvwxyz1234567890' . time()));
        $userData = [
            "id" => $this->request->getVar('id'),
            // "last_name" => $this->getVariable('lastName'),
            // "city" => $this->getVariable('region'),
            // "role" => $this->getVariable('role'),
            // "position" => $this->getVariable('role') == '7'  ? 'superAdmin' : 'normalUser',
            // "email" => $this->getVariable('email'),
            "unique_id" => $uniqueId,



        ];

        return $this->response->setJSON([
            'data' => $userData,
            'token' => $token,

        ]);
    }
}
// }
