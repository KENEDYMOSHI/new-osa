<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\SearchModel;

class GeolocationController extends BaseController
{
    public function __construct(
        protected $searchModel = null,
        protected $session = null,
        protected $token = null,
        protected $collectionCenters = null,
        protected $uniqueId = null,
        protected $collectionCenter = null,
        protected $role = null,
        protected $customerModel = null
    ) {
        helper('setting');
        helper(setting('App.helpers'));
        $this->searchModel =  new SearchModel();
        $this->customerModel =  new CustomerModel();
        $this->session = session();
        $this->token =  csrf_hash();
        $this->uniqueId =  auth()->user()->unique_id;
        $this->collectionCenter =  auth()->user()->collection_center;
        $this->role =  auth()->user()->role;
    }

    public function getVariable($var)
    {
        return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }






    public function index()
    {
        $data['page'] = [
            'title' => 'Geo Location Report',
            'heading' => 'Geo Location Report',
        ];


        $data['role'] = $this->role;
        $data['user'] = auth()->user();




        $params = [
            'CreatedAt >=' => financialYear()->startDate,
            'CreatedAt<=' => financialYear()->endDate,
            'customers.latitude!=' =>  '',
            'customers.longitude!=' =>  ''
        ];


        $prePackage = $this->customerModel->getPrepackageCustomers($params);
        $vtv = $this->customerModel->getVtvCustomers($params);
        $sbl = $this->customerModel->getSblCustomers($params);
        $waterMeter = $this->customerModel->getWaterMeterCustomers($params);
        $customerData = array_merge($prePackage, $vtv, $sbl, $waterMeter);
        $data['vtv'] = $this->customerModel->getVtvCustomers($params);
        // $data['customers'] = $this->dataTable($this->customerModel->getPrepackageCustomers($params));
        $data['params'] = $params;
        $data['customers'] = $this->dataTable($customerData);
        $data['sbl'] = $waterMeter;



        return view('Pages/Geolocation/LocationReport', $data);
    }

    public function filterLocationData()
    {
        $name = $this->getVariable('name');
        $activity = $this->getVariable('activity');
        $collectionCenter = $this->getVariable('collectionCenter');
        $year = $this->getVariable('year');


        $queryParams = [
            'YEAR(customers.created_at)' => $year,
            'CollectionCenter' => $collectionCenter,


        ];

      

        $params = array_filter($queryParams, fn ($param) => $param !== '' || $param != null);
        $params['customers.latitude!='] = '';
        $params['customers.longitude!='] = '';
        
        switch ($activity) {
            case '142101210027':
                $customers = $this->customerModel->getPrepackageCustomers($params, $name);
                break;
            case '142101210024':
                $customers = $this->customerModel->getVtvCustomers($params, $name);
                break;
            case '142101210025':
                $customers = $this->customerModel->getSblCustomers($params, $name);
                break;
            case '142101210026':
                $customers = $this->customerModel->getWaterMeterCustomers($params, $name);
                break;

            default:
                $customers = [];
                break;
        }




        return  $this->response->setJSON([
            'tableData' => $this->dataTable($customers),
            'token' => $this->token,
            'params' => $params,
            'customer' => $customers,
            'activity' => $activity,

        ]);
    }

    public function dataFilter()
    {
    }

    public function dataTable($customers)
    {
        $rows = '';
        foreach ($customers as $customer) {
            $lat = round((float)$customer->latitude, 7);
            $long = round((float)$customer->longitude, 7);
            $mobile = str_replace('255', '0', $customer->phone_number);
            $verificationDate = dateFormatter($customer->verificationDate);
            $nextVerification = dateFormatter($customer->nextVerification);
            $rows .=  <<<HTML
                 <tr>
                    <td>$customer->Activity </td>
                    <td>$customer->centerName </td>
                    <td>$customer->name </td>
                    <td>$mobile </td>
                    <td>$customer->district </td>
                    <td>$customer->postal_code </td>
                    <td>$customer->physical_address </td>
                    <td>$verificationDate </td>
                    <td>$nextVerification </td>
                    <td>$customer->verificationStatus </td>
                    <td>$lat </td>
                    <td>$long </td>

                    <td><button type="button" class="btn btn-primary btn-xs" onclick="openMap('$customer->latitude ','$customer->longitude ','$customer->name ')"><i class="fal fa-map-marker-alt"></i> Open Map</button></td>

                            </tr>
            HTML;
            # code...
        }

        return <<<HTML
         <thead>
              <tr>
                <th>Activity</th>
                <th>Collection Center</th>
                <th>Customer Name</th>
                <th>Phone Number</th>
                <th>District</th>
                <th>Post Code</th>
                <th>Physical Address</th>
                <th>Verification Date</th>
                <th>Next Verification</th>
                <th>Status </th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Map</th>
            </tr>
        </thead>
        <tbody>
            $rows
        </tbody>

HTML;
    }
}
