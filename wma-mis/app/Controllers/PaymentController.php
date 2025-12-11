<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BillModel;
use App\Models\ProfileModel;


class PaymentController extends BaseController
{
    protected $BillModel;
    protected $uniqueId;
    protected $managerId;
    protected $role;
    protected $city;

    protected $session;
    protected $profileModel;
    protected $CommonTasks;

    protected $PaymentLibrary;
    protected $token;


    public function __construct()
    {

        $this->session = session();
        $this->token = csrf_hash();
        $this->BillModel = new BillModel();
        $this->profileModel = new profileModel();
        $this->uniqueId = $this->session->get('loggedUser');
        $this->managerId = $this->session->get('manager');
        $this->role = $this->profileModel->getRole($this->uniqueId)->role;
        $this->city = $this->session->get('city');
        helper(['form', 'array', 'regions', 'date', 'prePackage_helper', 'image', 'url']);
    }

    public function getVariable($var)
    {
        return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public function index()
    {
        $currentPage =  url_is('PaymentManagement') ? "Payment Management" : "Payments";
        $data['page'] = [
            "title" => $currentPage,
            "heading" =>  $currentPage,
        ];



        $data['role'] = $this->role;
        $data['user'] = auth()->user();


        return view('Pages/Transactions/searchReceipt', $data);
    }
    public function payments()
    {

        $data['page'] = [
            "title" => 'Payments',
            "heading" =>  'Payments',
        ];



        $data['role'] = $this->role;
        $data['user'] = auth()->user();


        return view('Pages/Transactions/searchReceipt', $data);
    }


    public function searchPayment()
    {
        $activity = $this->getVariable('activity');
        $payment = $this->getVariable('payment');
        $name = $this->getVariable('name');
        $phone = $this->getVariable('phone');
        $date = $this->getVariable('date');
        $controlNumber = $this->getVariable('controlNumber');

        $PaymentParams = [
            // 'name' => $name,
            'payment' => $payment,
            'transactions.control_number' => $controlNumber,
            'customers.phone_number' => $phone,
            'DATE(transactions.created_on)' => $date,
        ];

        foreach ($PaymentParams as $key => $value) {
            if ($value == '' || $value == 'All') {
                unset($PaymentParams[$key]);
            }
        }

        // if($name == '' && count() ){

        // }
        $request =  $this->BillModel->searchPayment($PaymentParams, $name, $activity);

        if ($request) {
            $Payment = array_map(fn ($data) => [
                'id' => $data->id,
                'hash' => $data->hash,
                'name' => $data->name,
                'phoneNumber' => $data->phone_number,
                'controlNumber' => $data->control_number,
                'payment' => $data->payment,
                'amount' => $data->amount,
                'date' => dateFormatter($data->created_on),
                'item' => $data->item,
                'total' => 'Tsh ' . number_format($data->total),
                'totalInWords' => toWords($data->total),
            ], $request);
            return $this->response->setJSON([

                'status' => 1,
                'PaymentData' => $Payment,
                'activity' => $activity,
                'token' => $this->token
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 0,
                'PaymentData' => [],
                'activity' => $activity,
                'token' => $this->token
            ]);
        }


        // return $this->response->setJSON([$Payment]);
    }

    public function selectPayment()
    {
        $activity = $this->getVariable('activity');
        $controlNumber = $this->getVariable('controlNumber');
        $document = $this->getVariable('document');

        // return $this->response->setJSON([$controlNumber,$activity]);
        // exit;

        $user = $this->profileModel->getLoggedUserData($this->uniqueId);


        $Payment =  $this->BillModel->selectPayment($controlNumber, $activity);

        if ($Payment) {
            $products = array_map(fn ($product) => [
                'product' => $product->item,
                'amount' => number_format(isset($product->amount) ? $product->amount : $product->total_amount),
            ], $Payment);

            $data = [
                'status' => 1,
                'document' => $document,
                'products' =>  $products,
                'payer' => $Payment[0]->name,
                'phoneNumber' => $Payment[0]->phone_number,
                'controlNumber' => $Payment[0]->control_number,
                'paymentDate' => dateFormatter($Payment[0]->paymentDate),
                'PaymentTotal' =>  number_format($Payment[0]->total),
                'PaymentTotalInWords' =>  toWords($Payment[0]->total),
                'paymentRef' => time(),
                'createdBy' => $Payment[0]->creator,
                'printedBy' => $user->first_name . ' ' . $user->last_name,
                'printedOn' => date('d M Y'),
                'token' => $this->token,

            ];



            $data['balance'] = number_format($Payment[0]->total - $Payment[0]->paid);
            $data['receiptNumber'] = numString(13);
            $data['billReference'] = numString(16);





            return $this->response->setJSON($data);
        } else {
            return $this->response->setJSON([
                'status' => 0,
                'token' => $this->token,

            ]);
        }
    }
}
