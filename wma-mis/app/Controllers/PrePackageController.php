<?php

namespace App\Controllers;

use DateTime;
use Mpdf\Tag\Center;
use App\Models\AppModel;
use App\Libraries\Library;
use App\Models\ProfileModel;
use App\Libraries\PdfLibrary;
use App\Libraries\SmsLibrary;
use App\Models\PrePackageModel;
use App\Models\CertificateModel;
use App\Libraries\PrePackageLibrary;
use App\Libraries\CommonTasksLibrary;
use App\Libraries\ActivityBillProcessing;

class PrePackageController extends BaseController
{
    protected $uniqueId;
    protected $managerId;
    protected $user;
    protected $city;
    protected $PrePackageModel;
    protected $session;
    protected $profileModel;
    protected $CommonTasks;
    protected $appRequest;
    protected $prePackageLibrary;
    protected $billLibrary;
    protected $token;
    protected $collectionCenter;
    protected $GfsCode;
    protected $sms;

    public function __construct()
    {
        helper('setting');
        helper(setting('App.helpers'));
        $this->GfsCode = setting('Gfs.prePackages');
        $this->PrePackageModel = new PrePackageModel();
        $this->profileModel = new ProfileModel();
        $this->prePackageLibrary = new PrePackageLibrary();
        $this->session = session();
        $this->token = csrf_hash();
        $this->uniqueId =  auth()->user()->unique_id;
        $this->collectionCenter = auth()->user()->collection_center;
        $this->user = auth()->user();
        $this->CommonTasks = new CommonTasksLibrary();
        $this->sms = new SmsLibrary();
        helper(['form', 'array', 'regions', 'date', 'prePackage_helper', 'image', 'url']);
    }

    public function getVariable($var)
    {
        return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public function index()
    {
        $data['page'] = [
            "title" => "Pre Package",
            "heading" => "Pre Package",
        ];



        $data['user'] = $this->user;
        // return view('Pages/Prepackage/prepackage', $data);
        return view('Pages/Prepackage/prepackaging', $data);
    }



    public function registeredPrepackages()
    {


        $data['page'] = [
            "title" => "Registered Pre Packages",
            "heading" => "Registered Pre Packages",
        ];


        $data['user'] = $this->user;



        // return view('Pages/Prepackage/prePackageReport', $data);

        return view('Pages/Prepackage/prepackaging', $data);
    }



    public function imported()
    {
        $data['page'] = [
            "title" => "Pre Package(Imported)",
            "heading" => "Pre Package(Imported)",
        ];



        $data['user'] = $this->user;
        $where = [
            'createdAt >=' => financialYear()->startDate,
            'createdAt <=' => financialYear()->endDate,
            'region' => $this->user->inGroup('manager', 'officer') ? $this->collectionCenter : '',
        ];

        $params = array_filter($where, fn($param) => $param !== '' || $param != null);

        $imported = $this->PrePackageModel->getImported($params);
        $data['imported'] = array_map(function ($items) {
            $items->products = json_decode($items->products);
            return $items;
        }, $imported);

        return view('Pages/Prepackage/Imported', $data);
    }

    public function listPrepackage()
    {
        $data['page'] = [
            "title" => "Pre Package",
            "heading" => "Pre Package",
        ];


        $data['user'] = $this->user;
        $data['prePackageData'] = $this->prePackageLibrary->formatDataset($this->PrePackageModel->prePackageData($this->user->unique_id));
        // return view('Pages/Prepackage/prePackageReport', $data);
        return view('Pages/Prepackage/listPrepackage', $data);
    }





    //=================searching existing customer====================
    public function searchCustomer()
    {

        $keyword = $this->getVariable('keyword');
        $request = $this->PrePackageModel->searchCustomer($keyword);
        if (count($request) > 0) {
            return $this->response->setJSON([
                'status' => 1,
                'data' => $request,
                'token' => $this->token
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 0,
                'data' => [],
                'token' => $this->token
            ]);
        }
    }

    public function getPrePackageCustomer()
    {


        $hash = $this->getVariable('hash');



        $products  = $this->PrePackageModel->getPaidProducts($hash);


        //===============================================================================


        $request = $this->PrePackageModel->getCustomerInfo($hash);
        if ($request) {
            return $this->response->setJSON([
                'products' =>  $products,
                'status' => 1,
                'data' => $request,
                'token' => $this->token
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 0,
                'data' => '',
                'token' => $this->token
            ]);
        }
    }
    public function editPrePackageCustomer()
    {


        $hash = $this->getVariable('hash');
        // $ids = [];

        // $billedProducts =   $this->PrePackageModel->getBilledProducts($hash);
        $billedProducts = $this->PrePackageModel->getPaidProducts($hash);


        if (count($billedProducts) == 0) {



            return $this->response->setJSON([
                'status' => count($billedProducts) == 0 ? 0 : 1,
                'products' => $this->PrePackageModel->getProducts($hash),
                'data' => $this->PrePackageModel->getCustomerInfo($hash),
                'token' => $this->token
            ]);
        } else {

            // foreach ($billedProducts as $id) {
            //     array_push($ids, $id->product_id);
            // }
            $billedProductIds = array_map(fn($id) => $id->product_id, $billedProducts);

            $products  = $this->PrePackageModel->getUnpaidProducts($hash, $billedProductIds);
        }



        //===============================================================================


        $request = $this->PrePackageModel->getCustomerInfo($hash);
        if ($request) {
            return $this->response->setJSON([
                'products' =>  $products,
                'status' => 1,
                'data' => $request,
                'token' => $this->token,
                'billedProductIds' =>  $billedProductIds
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 0,
                'data' => '',
                'token' => $this->token
            ]);
        }
    }
    public function checkMeasurements($measurements, $category, $sampleSize)
    {
        $quantity = count($measurements);

        $categories1 = ['Area', 'General', 'Bread', 'Poultry', 'Count', 'Linear', 'Cubic', 'Seeds', 'Medical_Gases', 'Gases', 'Anthracite', 'Fruits', 'Sheets'];

        $categories2 = ['Area_Linear', 'Linear 2', 'Area & Linear'];

        if (in_array($category, $categories1)) {
            if ($quantity != $sampleSize) {
                return false;
            } else {
                return true;
            }
        } else if (in_array($category, $categories2)) {
            if ($quantity <= $sampleSize) {
                return false;
            } else {
                return true;
            }
        }
    }


    public function getAllProducts()
    {
        $hash = $this->getVariable('customerId');


        //get billed items
        $billedProducts =   $this->PrePackageModel->getBilledProducts($hash);
        //get ids of all products which are not verified
        $ids = (new AppModel())->getItemIds([
            'customerId' => $hash,
            'activity' => $this->GfsCode,
            'collectionCenter' => $this->collectionCenter
        ]);

        $unverified = $this->PrePackageModel->getUnverifiedProducts($ids, $hash);

        return  $this->response->setJSON([
            'products' => $unverified,
            'token' => $this->token,
        ]);




        //if no items get items from product details table
        // if (count($billedProducts) == 0) {



        //     return $this->response->setJSON([
        //         'status' =>  1,

        //         'products' => $this->PrePackageModel->getProducts($hash),
        //         'token' => $this->token
        //     ]);
        // } else {


        //     $billedProductIds = array_map(fn ($id) => $id->BillItemRef, $billedProducts);
        //     // get unpaid items by separating unpaid from paid products ids
        //     $products  = $this->PrePackageModel->getUnpaidProducts($hash, $billedProductIds);

        //     $products =   $this->PrePackageModel->getProducts($hash);
        //     return  $this->response->setJSON([
        //         'products' => $products,
        //         'token' => $this->token,
        //     ]);
        // }
    }


    public function selectProduct()
    {
        //px1
        // $sampleSize = 20;
        $productId = $this->getVariable('id');
        //  $quantityId = $this->getVariable('quantityId');
        $sampleSize = (int)$this->PrePackageModel->selectProduct($productId)->sample_size;
        $category = $this->PrePackageModel->selectProduct($productId)->analysis_category;
        $params = [
            'product_id' => $productId,
            // 'quantity_id' => $measurementId
        ];
        $measurements = $this->PrePackageModel->getMeasurementData($params);
        $set1Measurements = array_slice($measurements, 0, $sampleSize);
        $set2Measurements = array_slice($measurements, $sampleSize);
        $measurementIds = [
            $set1Measurements ? $set1Measurements[0]->quantity_id : '',
            $set2Measurements ? $set2Measurements[0]->quantity_id : '',

        ];

        $idz = array_filter($measurementIds, fn($id) => $id != '');

        $request = $this->PrePackageModel->selectProduct($productId);
        if ($request) {
            return $this->response->setJSON([
                'status' => 1,
                'sampleSize' => $sampleSize,
                'quantityIds' =>  $idz,
                'measurements' =>  $this->checkMeasurements($measurements, $category, $sampleSize),
                'set 1' => $set1Measurements,
                'set 2' => $set2Measurements,
                'data' => $request,
                'ms qty' => count($measurements),
                'category' => $category,
                // 'sampleSize' => $sampleSize, 
                'token' => $this->token,
                //'num' => numString(20),
                'num' => time(),
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 0,
                'data' => '',
                'token' => $this->token
            ]);
        }
    }





    // ================Adding customer lorry information to database ==============

    public function addProductDetails()
    {

        //=================Checking the last id its available====================

        try {
            if ($this->request->getMethod() == 'POST') {
                $hash = $this->getVariable('customerId');

                $quantity_2 = $this->getVariable('quantity2');
                $unit_2 = $this->getVariable('unit2');
                $length = $this->getVariable('length');
                $width = $this->getVariable('width');
                $height = $this->getVariable('height');
                $unit_3 = $this->getVariable('unit_3');
                $grossQuantity = $this->getVariable('grosValue');
                $unit = $this->getVariable('unit');
                $type = $this->getVariable('type');
                $category =  $this->getVariable('categoryAnalysis');
                $tansardDocument = $this->request->getFile('tansardDocument');
                $tansardFile = '';


                $dimensions = '';
                if ($category == 'Cubic') {
                    $dimensions .= $length . ' x ' . $width . ' x ' . $height;
                } else if ($category == 'Area') {

                    $dimensions .= $length . ' x ' . $width;
                }



                if ($type == 'Imported') {
                    $tansardFile .= $this->CommonTasks->processFile($tansardDocument);
                } else {
                    $tansardFile = '';
                }


                $task  = $this->getVariable('activityType');

                $productDetails = [
                    'hash' => $hash,
                    'type' => $type,
                    'commodity' => $this->getVariable('commodity'),
                    'task' => $task,
                    'quantity' => $this->getVariable('quantity'),
                    'unit' => $unit,
                    'quantity_2' => $quantity_2,
                    'unit_2' => $unit_2,
                    'unit_3' => $dimensions,

                    // 'quantity1_id' => $category == 'Cubic' || $category == 'Area' ? numString(10) . '-' . $dimensions : numString(10) . '-' . $grossQuantity,
                    'quantity1_id' =>  numString(10) . '-' . $grossQuantity,
                    'quantity2_id' => $quantity_2 != '' ? numString(10) . '-' . $quantity_2 . $unit_2 : '',
                    'ref_number' => $this->getVariable('refNumber'),

                    //  Imported
                    'tansard_number' => $type == 'Imported' ? $this->getVariable('tansardNumber') : '',
                    'tansard_file' => $tansardFile,
                    'fob' => $this->getVariable('fob'),
                    'date' => dateFormatter($this->getVariable('date')),





                    // 'verified' => $task == 'Inspection' ? '1' : '0',
                    'batch_number' => $this->getVariable('batchNumber'),
                    'analysis_category' => $category,
                    'packing_declaration' => $this->getVariable('packingDeclaration'),
                    'lot' => $this->getVariable('batchSize'),
                    'activity' => $this->GfsCode,
                    'method' => $this->getVariable('method'),
                    'measurement_unit' => $this->getVariable('measurementUnit'),
                    'sampling' => $this->getVariable('sampling'),
                    'measurement_nature' => $this->getVariable('measurementNature'),
                    'tare' => $this->getVariable('tareWeight'),
                    'product_nature' => $this->getVariable('productNature'),
                    'density' =>  $this->getVariable('density'),
                    'gross_quantity' => $this->getVariable('grosValue'),
                    'sample_size' => $this->getVariable('sampleSize'),

                    //labeling
                    'packer_identification' => $this->getVariable('packerIdentification'),
                    'product_identification' => $this->getVariable('productIdentification'),
                    'correct_unit' => $this->getVariable('correctUnit'),
                    'correct_symbol' => $this->getVariable('correctSymbol'),
                    'correct_height' => $this->getVariable('correctHeight'),
                    'correct_quantity' => $this->getVariable('correctQuantity'),
                    'general_appearance' => $this->getVariable('generalAppearance'),
                    // 'recommendation' => $this->getVariable('recommendation'),



                    'unique_id' => $this->uniqueId,

                ];

                // return $this->response->setJSON([
                //   'status' => 0,
                //   'data' => $productDetails,
                //   'token' => $this->token
                // ]);
                // exit;
                $ids = [];

                $request = $this->PrePackageModel->addProductDetails($productDetails);

                if ($request) {

                    $billedProducts =   $this->PrePackageModel->getBilledProducts($hash);

                    if (count($billedProducts) == 0) {



                        return $this->response->setJSON([
                            'status' =>  1,
                            'msg' => 'Product Added Successfully!',
                            'products' => $this->PrePackageModel->getProducts($hash),
                            // 'products' =>   $this->PrePackageModel->getUnpaidProducts($hash, $billedProducts),
                            'lastProduct' =>  $this->PrePackageModel->getProducts($hash)[0],
                            'data' => $this->PrePackageModel->getCustomerInfo($hash),
                            'token' => $this->token
                        ]);
                    } else {


                        // foreach ($billedProducts as $id) {

                        //     array_push($ids, $id->BillItemRef);
                        // }
                        $billedProductIds = array_map(fn($id) => $id->BillItemRef, $billedProducts);

                        $products  = $this->PrePackageModel->getUnpaidProducts($hash, $billedProductIds);
                        return $this->response->setJSON([
                            'status' => 1,
                            'msg' => 'Product Added Successfully!',
                            'lastProduct' =>  $this->PrePackageModel->getProducts($hash)[0],
                            //px
                            'products' =>  $products,
                            'token' => $this->token
                        ]);
                    }


                    // echo json_encode($billedProducts);
                    // exit;

                    // $products =  $this->PrePackageModel->getUnpaidProducts($hash, $ids);


                } else {
                    return $this->response->setJSON([
                        'status' => 0,
                        'msg' => 'Something Went Wrong 1111!',
                        'token' => $this->token
                    ]);
                }
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'trace' => $th->getTrace(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response);
    }




    // get products with measurement data but they are not in billing table
    public function getCompleteProducts()
    {
        try {
            $ids = [];
            if ($this->request->getMethod() == 'POST') {
                $hash = $this->getVariable('customerId');





                $idz = $this->PrePackageModel->verifiedProducts([
                    'hash' => $hash,
                    'verified' => 1
                ]);


                //  return $this->response->setJSON([
                //    'status' => 0,
                //    'data' => $idz,
                //    'token' => $this->token
                //  ]);

                //  exit;

                $products = $this->PrePackageModel->fetchProducts($idz,  $hash);

                // $billedProducts =   $this->PrePackageModel->getBilledProducts($hash);
                // foreach ($billedProducts as $id) {
                //     array_push($ids, $id->BillItemRef);
                // }

                // $billedProductIds = array_map(fn ($id) => $id->BillItemRef, $billedProducts);

                // // $p  = $this->PrePackageModel->getProductId($hash, $ids);

                // $products = $this->PrePackageModel->getUnpaidProducts($hash, $billedProductIds);
                // $productIds = array_map(fn ($pro) => $pro->id, $products);
                // $prod = $this->PrePackageModel->getProductsWithMeasurements($productIds, $hash);
                // echo json_encode($billedProducts);

                // exit;



                return $this->response->setJSON([

                    'status' => 1, //P@ssw0rd00
                    'yyy' => 'yyyy',
                    'products2' => $products,
                    // 'products' => [],
                    'billedProduct' =>   $products,
                    'token' => $this->token,

                    'products' =>  $products

                ]);
                //WMA$admin01    Github@255
                // ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password by 'WMA$admin01';
                // if (count($billedProducts) == 0) { WMA$admin01



                //     return $this->response->setJSON([
                //         'status' => count($billedProducts) == 0 ? 0 : 1,
                //         'xxx' => 'xx',
                //         'products' =>   $this->PrePackageModel->getMeasuredProducts($hash),
                //         'token' => $this->token
                //     ]);
                // } else {


                // }
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'trace' => $th->getTrace(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response);
    }
    //check if measurement is already created

    public function checkQuantityId()
    {
        try {
            if ($this->request->getMethod() == 'POST') {
                $id = $this->getVariable('quantityId');
                $productId = $this->getVariable('productId');
                $product = $this->PrePackageModel->selectProduct($productId);
                $quantity1 = $product->quantity;
                $current = substr(preg_replace('/[^0-9]/', '', $id), 10);
                $category = $product->analysis_category;
                $switch = $category == 'Area_Linear' && ($current < $quantity1) ? true : false;

                // return $this->response->setJSON([
                //    'q1' =>$quantity1 ,
                //     'current' => $current ,
                //     'switch' => $switch , 
                // ]);

                // exit;

                $request = $this->PrePackageModel->checkQuantityId($id);
                if ($request) {
                    return $this->response->setJSON([

                        'status' => 1,
                        'qId' => $id,
                        'token' => $this->token,
                        'switch' => $switch,
                        'current' => $current,
                        'quantity1' => $quantity1,
                        'sampleSize' => (int)$product->sample_size,
                        // 'dimensions' => $product->unit_3,


                    ]);
                } else {
                    return $this->response->setJSON([
                        'qId' => $id,
                        'current' => $current,
                        'quantity1' => $quantity1,
                        'status' => 0,
                        'id' => $id,
                        'token' => $this->token,
                        'switch' => $switch,
                        'sampleSize' => (int)$product->sample_size,
                        'dimensions' => $product->analysis_category == 'Area' || $product->analysis_category == 'Cubic' ? 'Measurements: ' . $product->unit_3 : '',

                    ]);
                }
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response);
    }

    // grab all products with complete measurements
    public function getProductsWithMeasurements()
    {

        try {
            if ($this->request->getMethod() == 'POST') {

                // $measurementIds  = $this->PrePackageModel->getMeasurementData();
                $hash = $this->getVariable('customerId');
                $activity = $this->getVariable('activity');
                $inspectionType = $this->getVariable('inspectionType');





                // $idz = (new AppModel())->getItemIds([
                //     'customerId' => $hash,
                //     'activity' => $this->GfsCode,
                //     'collectionCenter' => $this->collectionCenter

                // ]);

                $idz = $this->PrePackageModel->verifiedProducts([
                    'hash' => $hash,
                    'verified' => 0
                ]);

                if (empty($idz)) $idz = [microtime()];



                //    return $this->response->setJSON([
                //             'status' => 1,
                //             'ids' => $idz,
                //             //'products' => $theProducts,
                //             'activity' => $activity,
                //             'token' => $this->token
                //         ]);

                //         exit;





                $products = $this->PrePackageModel->fetchProducts($idz,  $hash);
                $theProducts = array_filter($products, fn($product) => $product->task == $activity || $product->task == 'Reverification');



                // return $this->response->setJSON([
                //     'status' => 1,
                //     'ids' => $idz,
                //     'products' => $theProducts,
                //     'activity' => $activity,
                //     'token' => $this->token
                // ]);

                // exit;
                $productsArr = array_map(function ($item) {
                    // $params2 = [
                    //     'product_id' => $item->product_id,
                    //     //'quantity_id' => $item->quantity_id
                    // ];
                    $product = $this->PrePackageModel->selectProduct($item->id);
                    $category = $product->analysis_category;
                    $measurements = $this->PrePackageModel->getMeasurementData(['product_id' => $item->id]);

                    // return $measurements;/
                    $sampleSize = $item->sample_size;
                    // $sampleSize = 100;

                    $set1Measurements = array_slice($measurements, 0, $sampleSize);
                    $set2Measurements = array_slice($measurements, $sampleSize);







                    $set1Status = $this->prePackageLibrary->processingMeasurements($set1Measurements);


                    $set2Status =
                        !empty($set2Measurements) ? $this->prePackageLibrary->processingMeasurements($set2Measurements) : [];

                    $status = $this->prePackageLibrary->evaluateStatus($set1Status, $set2Status);

                    return [
                        'commodity' => $item->commodity . ' ' . $item->quantity . ' ' . $item->unit,
                        'hash' => $item->hash,
                        'id' => $item->id,
                        'lot' => $item->lot,
                        'type' => $item->type,
                        'fob' => $item->fob,
                        'tansardNumber' => $item->tansard_number,
                        'date' => $item->date,
                        'task' => $item->task,
                        'measurements' => $this->PrePackageModel->getMeasurementData(['product_id' => $item->id]),
                        'measurements' => $set1Measurements,
                        'measurements two' => $set2Measurements,
                        // 'item' => $item,



                        'result' => $status,
                        'status 1' =>  $set1Status,
                        'status 2' =>  $set2Status,
                        // 'PRODUCT STATUS' => $status
                    ];
                }, $theProducts);



                // echo json_encode($productsArr);
                // exit;

                return $this->response->setJSON([

                    'status' => 1,
                    //  'billed' => $billed,
                    'products' => array_map(fn($p) => $p, $productsArr),
                    'type' => $inspectionType,
                    // 'idx' => array_values($idx),
                    // '' => removeBilledProducts($customerProducts, $billed) ,
                    'token' => $this->token,
                    'VAR-TYP' => gettype($productsArr),
                ]);
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'trace' => $th->getTrace(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response);
    }


    public function getProducts()
    {
        try {
            $ids = [];
            if ($this->request->getMethod() == 'POST') {
                $hash = $this->getVariable('customerId');

                $products =   $this->PrePackageModel->getTheProducts($hash);


                return $this->response->setJSON([

                    'status' => 1,
                    'products' =>   $products,
                    'token' => $this->token
                ]);
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response);
    }
    function getProductInfo($id)
    {
        $product = $this->PrePackageModel->selectProduct($id);
        return  $product->commodity . ' ' . $product->quantity . ' ' . $product->unit;
    }



    // ===================================================================
    public function generateCertificateNumber()
    {



        //get region name
        $region = wmaCenter($this->collectionCenter)->centerName;


        //get 3 first letter of region name
        $prefix = strtoupper(substr($region, 0, 3));


        // Fetch the last sticker data for the given activity
        $lastCertificate = (new CertificateModel())->getLastConformityCertificate(['region' => $this->collectionCenter]);

        if (!$lastCertificate) {
            // If no data exists, start with the initial sticker value
            $certificate = $prefix . '000001';
            // Use $certificate here or perform any other operations outside the loop
        } else {
            // If data exists, extract the numeric part and increment it

            //get the letter prefix of sticker number
            $prefix = preg_replace("/[0-9]/", "", $lastCertificate->certificateNumber);
            //get the numeric part
            $numericPart = (int) preg_replace("/[^0-9]/", "", $lastCertificate->certificateNumber);
        }



        if (isset($certificate)) {
            // If $certificate is set (meaning it's the initial value), use it as is
            $currentCertificate = $certificate;
            //  unset($certificate); // Unset $certificate so it won't be used in subsequent iterations
        } else {
            // Increment the numeric part and generate the sticker
            $numericPart++;
            // combine prefix and incremented part
            $currentCertificate = $prefix . sprintf('%06d', $numericPart);
        }

        return $currentCertificate;

        //  echo $prefix;




    }
    // ===================================================================








    public function createCertificateData($data)
    {

        $certificate = new CertificateModel();
        $params = [
            'certificateId' => randomString(),
            'certificateNumber' => $this->generateCertificateNumber(),
            'activity' => $data->activity,
            'region' => $this->collectionCenter,
            'officer' => $this->uniqueId,
            'customer' => $data->customer,
            'mobile' => $data->mobile,
            'address' => $data->address,
            'controlNumber' => $data->controlNumber,
            'products' => json_encode($data->products)

        ];

        $certificate->addConformityCertificate($params);
    }

    public function createBill()
    {


        try {
            $transactionData =  [];
            if ($this->request->getMethod() == 'POST') {
                $hash = $this->getVariable('customerHash');
                $activityType = $this->getVariable('activityType');
                $method = $this->getVariable('method');
                $SwiftCode = $this->getVariable('SwiftCode');
                $billedAmount = (float)$this->getVariable('totalAmount');
                $expiryDate = $this->getVariable('BillExprDt');
                $currentDate = date("Y-m-d\TH:i:s");
                $xpDate = $expiryDate . '23:59:59';
                $BillExprDt = (empty($expiryDate) || strtotime($xpDate) < strtotime($currentDate)) ? date("Y-m-d\TH:i:s", strtotime("+7 days")) : date("Y-m-d\TH:i:s", strtotime($xpDate));




                $billId = 'WMA'.randomString();
                $requestId = $billId; //'WMAREQ' . numString(10);

                //crating ids to match length of the data set
                // $amountArray = [];
                // for ($i = 0; $i < count($hash); $i++) {
                //     array_push($amountArray, (int)$totalAmount);
                // }
                //crating ids to match length of the data set
                //quantity_id
                $amountArray = [];
                $controlNumberArray = [];
                $uniqueIds = [];
                $activityArray = [];
                $activityArray = [];
                $gfsCode = [];
                $nextVerification = [];
                $date =  date('Y-m-d', strtotime(date('Y-m-d') . ' +1 year'));
                for ($i = 0; $i < count($hash); $i++) {
                    // array_push($amountArray, (int)$totalAmount);
                    array_push($gfsCode, $this->GfsCode);
                    array_push($nextVerification, $date);
                    array_push($uniqueIds, $this->uniqueId);
                    array_push($activityArray, $activityType);
                }

                $productIds = $this->getVariable('prodId');

                $data = [
                    'hash' => $hash,
                    'gfsCode' => $gfsCode,
                    'product_id' => $productIds,
                    'unique_id' => $uniqueIds,
                    'amount' =>   $this->getVariable('prodMount'),
                    'nextVerification' =>  $nextVerification,
                ];

                $billingData = multiDimensionArray($data);



                // $bill = array_map(fn () => [
                //     'customer' => $this->PrePackageModel->getCustomerInfo($hash[0]),
                //     'products' => $billingData
                // ], $transactionData);


                $itemsArray = array_map(fn($product) =>
                [


                   // 'RefBillId' =>  $billId,
                   // 'SubSpCode' => setting('Bill.wmaSubSpCode'),
                   // 'CollSp' => setting('Bill.wmaSpCode'),
                    'BillItemRef' => $product['product_id'],
                    'UseItemRefOnPay' => 'N',
                    'BillItemAmt' => (float)$product['amount'],
                    'BillItemEqvAmt' => (float)$product['amount'],
                    'BillItemMiscAmt' => 0.00,
                    'GfsCode' => $this->GfsCode,
                    'ItemName' =>  $this->getProductInfo($product['product_id']),
                    'RequestId' => $requestId,
                    'BillId' => $billId,
                    'PayerId' => $product['hash'],
                    'UserId' => $product['unique_id'],
                    'center' => $this->collectionCenter,



                ], $billingData);

                $billedProducts = array_map(fn($product) => $this->getProductInfo($product['product_id']), $billingData);



                $customer = $this->PrePackageModel->getCustomerInfo($hash[0]);
                //=================data for bill submission====================
                $groupBillId = 'GRP' . numString(10);

                $centerDetails = wmaCenter($this->collectionCenter);
                $collectionCenterCode =  $centerDetails->collectionCenterCode; //'CC1015000199419';
                $groupBillId = 'GRP'.numString(10);
                $billDetailsArray = [
                    'BillTyp' => 1,
                    'isTrBill' => 'No',
                    'RequestId' => $requestId,
                    'CollCentCode' =>  $collectionCenterCode,
                    'CustId' => numString(5),
                    'CustIdTyp' =>  5,
                    'CustTin' => '',
                    'GrpBillId' => $groupBillId,

                    'BillId' => $billId,
                    'Activity' => $this->GfsCode,
                    'BillRef' => numString(10),
                    'BillAmt' => $billedAmount,
                    'BillAmtWords' => toWords($billedAmount),
                    'MiscAmt' =>  0.00,
                    'BillExprDt' =>  $BillExprDt,
                    'extendedExpiryDate' => (new DateTime())->modify('+360 days')->format('Y-m-d\TH:i:s'),
                    'PyrId' =>  $customer->hash,
                    'PyrName' =>  $customer->name,
                    'BillDesc' =>  'Prepackage Inspection',
                    'BillGenDt' => date('Y-m-d\TH:i:s'),
                    'BillGenBy' =>   $this->getUser()->name,
                    'CollectionCenter' =>   $this->collectionCenter,
                    'BillApprBy' =>   'WMAHQ',
                    'PyrCellNum' =>  $customer->phone_number,
                    'PyrEmail' =>   $customer->email,
                    'Ccy' =>  'TZS',
                    'BillEqvAmt' => $billedAmount,
                    'RemFlag' =>  $this->getVariable('RemFlag') == "on" ? 'true' : 'false',
                    'BillPayOpt' =>  (int)$this->getVariable('BillPayOpt'),
                    'method' =>  $method,
                    'Task' =>  'Verification',
                    'UserId' =>  $this->uniqueId,
                    'SwiftCode' =>  $SwiftCode != '' ? $SwiftCode : '',

                ];

                // $this->PrePackageModel->billedProducts($billingData);


                // return $this->response->setJSON([
                //     'bill' => $billDetailsArray,
                //     'item' => $itemsArray,
                //     'billingData' => $billingData,
                //     'token' => $this->token,
                // ]);
                // exit; 


                $activityBill = new ActivityBillProcessing();

                $response = $activityBill->processBill($billDetailsArray, $itemsArray, $this->getUser()->name);


                $params = [
                    'customerId' => $hash[0],
                    'collectionCenter' => $this->collectionCenter,
                    'activity' => $this->GfsCode,
                ];



                if ($response->status == 1) {
                    $this->PrePackageModel->saveBilledProducts($billingData);

                    (new AppModel())->purgeItems($productIds, $params);
                    $this->PrePackageModel->updateProducts($productIds, ['verified' => 1]);
                    $controlNumber = $response->controlNumber;

                    $textParams = (object)[
                        'payer' => $customer->name,
                        'center' => wmaCenter($this->collectionCenter)->centerName,
                        'amount' => $billedAmount,
                        'items' => (string)implode(',', $billedProducts),
                        'expiryDate' => $expiryDate,
                        'controlNumber' => $controlNumber,

                    ];

                    $certificateData = (object)[

                        'customer' => $customer->name,
                        'activity' => $this->GfsCode,
                        'mobile' => $customer->phone_number,
                        'address' => $customer->postal_address,
                        'products' =>  $productIds,
                        'controlNumber' => $controlNumber,

                    ];
                    //adding certificate data to to the database
                    $this->createCertificateData($certificateData);

                    //sending sms notification to customer
                    $this->sms->sendSms(recipient: $customer->phone_number, message: billTextTemplate($textParams));

                    return $this->response->setJSON([
                        'status' => 1,
                        'TrxStsCode' => $response->TrxStsCode,
                        'msg' => 'Bill Created Successfully',
                        'bill' => $response->bill,
                        'qrCodeObject' => $response->qrCodeObject,
                        'heading' => $response->heading,
                        'token' => $this->token
                    ]);
                } else {

                    if ($response->status == 1) {
                        return $this->response->setJSON([
                            'status' => 1,
                            'TrxStsCode' => $response->TrxStsCode,
                            'msg' => 'Bill Created Successfully',
                            'bill' => $response->bill,
                            'qrCodeObject' => $response->qrCodeObject,
                            'heading' => $response->heading,
                            'token' => $this->token
                        ]);
                    } else {
                        return $this->response->setJSON([
                            'status' => 0,
                            'TrxStsCode' => $response->TrxStsCode,
                            'msg' => !empty($response->TrxStsCode)  ? tnxCode($response->TrxStsCode) : $response->msg,
                            'token' => $this->token
                        ]);
                    }
                }
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'trace' => $th->getTrace(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response);
    }

    public function getUser(): object
    {
        $user = $this->profileModel->getLoggedUserData($this->uniqueId);
        return (object)[
            'name' => auth()->user()->username,
            'collectionCenter' => centerName()

        ];
    }


    //=================save measurement sheet data ====================
    public function saveMeasurementData()
    {
        try {
            if ($this->request->getMethod() == 'POST') {

                $measurementData = [];
                $idArray = [];
                $quantityIdArray = [];
                // $gross = $this->getVariable('weightGross');
                $switcher = $this->getVariable('switcher');
                $comment = $this->getVariable('comment');
                $status = $this->getVariable('status');
                $commodityId = $this->getVariable('commodityId');
                $commodityCategory = $this->getVariable('commodityCategory');

                $category = '';

                $commodityCategory =
                    $switcher != '' ?   $category .= 'Linear' : $category .= $commodityCategory;
                $quantityId =  $this->getVariable('currentQuantity');


                //crating ids to match length of the data set
                for ($i = 0; $i < count($comment); $i++) {
                    array_push($idArray, $commodityId);
                    array_push($quantityIdArray, $quantityId);
                }


                // return $this->response->setJSON([
                //     'data' => $quantityIdArray,

                //    // 'token' => $this->token
                // ]);
                // exit;
                $data = null;
                switch ($category) {
                    case 'General':
                    case 'Linear':
                    case 'Linear 2':
                    case 'Count':
                    case 'Fruits':
                    case 'Bread':
                    case 'Poultry':
                    case 'Gases':
                    case 'Seeds':
                    case 'Sheets':
                    case 'Anthracite':
                        $data = [
                            'gross_quantity' => $this->getVariable('weightGross'),
                            'net_quantity' => $this->getVariable('weightNet'),
                            'comment' => $comment,
                            'status' => $status,
                            'product_id' => $idArray,
                            'quantity_id' => $quantityIdArray,
                        ];
                        break;
                    case 'Area':
                    case 'Area_Linear':
                        $data = [
                            'length' => $this->getVariable('length'),
                            'width' =>  $this->getVariable('width'),
                            'net_quantity' =>  $this->getVariable('area'),
                            'comment' => $comment,
                            'status' => $status,
                            'product_id' => $idArray,
                            'quantity_id' => $quantityIdArray,
                        ];
                        break;
                    case 'Cubic':
                        $data = [
                            'length' => $this->getVariable('length'),
                            'width' =>  $this->getVariable('width'),
                            'height' =>  $this->getVariable('height'),
                            'net_quantity' =>  $this->getVariable('volume'),
                            'comment' => $comment,
                            'status' => $status,
                            'product_id' => $idArray,
                            'quantity_id' => $quantityIdArray,
                        ];
                        break;

                    default:
                        # code...
                        break;
                }

                // $data = [
                //     'gross_quantity' => $gross,
                //     'net_quantity' => $net,
                //     'comment' => $comment,
                //     'status' => $status,
                //     'product_id' => $idArray,
                // ];




                // creating multidimensional array for batch insertion
                foreach ($data as $key => $value) {
                    for ($i = 0; $i < count($value); $i++) {
                        $measurementData[$i][$key] = $value[$i];
                    }
                }


                // return $this->response->setJSON([
                //     'data' => $measurementData,
                //     'token' => $this->token
                // ]);

                // exit;



                $request = $this->PrePackageModel->addMeasurementSheetData($measurementData);
                if ($request) {
                    $product = $this->PrePackageModel->selectProduct($commodityId);
                    $hash = $product->hash;
                    if($product->task == 'Inspection'){
                        $this->PrePackageModel->updateProducts([$commodityId], ['verified' => 1]);
                    }
                    (new AppModel)->createTempId([   
                        'itemId' => $commodityId,
                        'customerId' => $hash,
                        'activity' => $this->GfsCode,
                        'collectionCenter' => $this->collectionCenter
                    ]);
                    return $this->response->setJSON([
                        'status' => 1,
                        'msg' => 'Data inserted successfully',
                        'commodityId' => $commodityId,
                        'token' => $this->token
                    ]);
                } else {
                    return $this->response->setJSON([
                        'status' => 0,
                        'commodityId' => $commodityId,
                        'msg' => 'something went wrong ',
                        'token' => $this->token
                    ]);
                }
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response);
    }


    public function getMeasurementData()
    {
        try {
            if ($this->request->getMethod() == 'POST') {

                $productId = $this->getVariable('productId');
                $quantityId = $this->getVariable('quantityId');
                $product = $this->PrePackageModel->selectProduct($productId);
                $category = $product->analysis_category;
                $sampleSize = (int)$product->sample_size;
                $params = [
                    'product_id' => $productId,
                    'quantity_id' => $quantityId
                ];
                // return json_encode($params);
                // exit;
                $data = $this->PrePackageModel->getMeasurementData($params);



                $measurements = $this->PrePackageModel->getMeasurementData(['product_id' => $productId]);
                $set1Measurements = array_slice($measurements, 0, $sampleSize);
                $set2Measurements = array_slice($measurements, $sampleSize);

                // $measurementIds = [
                //     $set1Measurements ? $set1Measurements[0]->quantity_id : '',
                //     $set2Measurements ? $set2Measurements[0]->quantity_id : '',

                // ];

                $set1StatusArray = $this->prePackageLibrary->processingMeasurements($set1Measurements);
                $set2StatusArray  = !empty($set2Measurements) ? $this->prePackageLibrary->processingMeasurements($set2Measurements) : [];



                $set1Status = $this->prePackageLibrary->evaluateStatus($set1StatusArray);
                $set2Status = $this->prePackageLibrary->evaluateStatus($set2StatusArray);

                $product = $this->PrePackageModel->selectProduct($productId);
                $category = $product->analysis_category;
                $quantity1 = $product->quantity . ' ' . $product->unit;
                $quantity2 = $product->quantity_2 . ' ' . $product->unit_2;

                $declaredQuantity = preg_replace('/[^0-9]/', '', substr($quantityId, 11));

                $switch = '';
                $declaredQuantity == $product->quantity_2  ? $switch .= 1 : $switch .= 0;
                $dataSet = [
                    'status' => 1,
                    'switcher' =>  $switch,
                    'data' => $data,

                    'token' => $this->token,
                    'results' => [
                        'quantity1' => $quantity1,
                        'quantity1Status' => $set1Status,
                        'category' => $category,
                    ],

                ];
                $dataSet['results']['quantity2'] = count($measurements) > $sampleSize ? $quantity2 : null;
                $dataSet['results']['quantity2Status'] = $set2Status;
                $dataSet['results']['overallStatus'] = $this->prePackageLibrary->evaluateStatus(array_merge($set1StatusArray, $set2StatusArray));

                // if ($category == 'Area' || $category == 'Linear 2') {


                // }
                return $this->response->setJSON($dataSet);


                // return json_encode($data);
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'msg' => $th->getMessage(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response);
    }


    public function downloadProductData($hash, $productId, $quantityId = '')
    {

        $params = [
            'product_id' => $productId,
            'quantity_id' => $quantityId != '00' ? $quantityId : ''
        ];






        $customerDetails = $this->PrePackageModel->getCustomerInfo($hash);
        $productDetails = $this->PrePackageModel->selectProduct($productId);
        $product = $this->PrePackageModel->selectProduct($productId);
        $category = $product->analysis_category;
        $measurementSheet = $this->PrePackageModel->getMeasurementData($params);

        //Data variables

        // $declaredQuantity = (int)$productDetails->quantity;
        $quantity = $product->quantity_2;

        $declaredQuantity = preg_replace('/[^0-9]/', '', substr($measurementSheet[0]->quantity_id, 11));

        $sampleSize = (int)$productDetails->sample_size;
        $lotSize = (int)$productDetails->lot;
        $appliedMethod = $productDetails->method;

        $switch = '';
        $declaredQuantity == $quantity ? $switch .= 1 : $switch .= 0;

        $data['customerDetails'] = $customerDetails;
        $data['productDetails'] = $productDetails;
        $data['measurementSheet'] = $measurementSheet;
        $data['declaredQuantity'] = $declaredQuantity;
        $data['sampleSize'] = $sampleSize;
        $data['lotSize'] = $lotSize;
        $data['productQuantity'] = $declaredQuantity;
        $data['switcher'] = $switch;

        //create array of net quantities
        $netQuantities = array_map(function ($net) use ($declaredQuantity) {
            return   $net->net_quantity - $declaredQuantity;
        }, $measurementSheet);

        $data['netQuantities'] = $netQuantities;
        //filter t1
        $withT1error = array_filter($measurementSheet, function ($data) {
            return $data->status == 1;
        });

        // filter t2
        $withT2error = array_filter($measurementSheet, function ($data) {
            return $data->status == 2;
        });

        // calculate individual error
        $individualError = array_reduce($netQuantities, function ($prev, $next) {
            return $prev + $next;
        });

        // echo '<pre>';

        // print_r($netQuantities);
        // echo '</pre>';

        // exit;


        /*

      
        const averageError = samplesWithError.reduce((prev, next) => {
            return +prev + +next
        }, 0) / samplesWithError.length v11

         */
        $realT1 = array_map(function ($t) use ($withT1error) {
            return $t->net_quantity;
        }, $withT1error);
        $realT2 = array_map(function ($t) use ($withT2error) {
            return $t->net_quantity;
        }, $withT2error);

        $samplesWithError = array_merge($realT1, $realT2);

        $t1Percentage = count($realT1) * 100 / $sampleSize;
        $t2Percentage = count($realT2) * 100 / $sampleSize;

        $averageError = $individualError / $sampleSize;

        $data['samplesWithError'] = $samplesWithError;
        $data['t1Percentage'] = $t1Percentage;
        $data['t2Percentage'] = $t2Percentage;
        $data['averageError'] = $averageError;
        $data['individualError'] = $individualError;


        $data['t1Items'] = count($realT1);
        $data['t2Items'] = count($realT2);



        // //=====================================
        // $measurementSheet = $this->PrePackageModel->getMeasurementData($params);



        $measurements = $this->PrePackageModel->getMeasurementData(['product_id' => $productId]);

        $set1Measurements = array_slice($measurements, 0, $sampleSize);
        $set2Measurements = array_slice($measurements, $sampleSize);

        // $measurementIds = [
        //     $set1Measurements ? $set1Measurements[0]->quantity_id : '',
        //     $set2Measurements ? $set2Measurements[0]->quantity_id : '',

        // ];

        $set1StatusArray = $this->prePackageLibrary->processingMeasurements($set1Measurements);
        $set2StatusArray  = !empty($set2Measurements) ? $this->prePackageLibrary->processingMeasurements($set2Measurements) : [];



        $set1Status = $this->prePackageLibrary->evaluateStatus($set1StatusArray);
        $set2Status = $this->prePackageLibrary->evaluateStatus($set2StatusArray);

        // $product = $this->PrePackageModel->selectProduct($productId);
        // $category = $product->analysis_category;
        $quantity1 = $product->quantity . ' ' . $product->unit;
        $quantity2 = $product->quantity_2 . ' ' . $product->unit_2;
        $dataSet = [

            'quantity1' => $quantity1,
            'quantity1Status' => $set1Status,
            'category' => $category,
            'count($measurementSheet)' => count($measurements),


        ];
        $dataSet['quantity2'] = count($measurements) > $sampleSize ? $quantity2 : null;
        $dataSet['quantity2Status'] = $set2Status;
        $dataSet['overallStatus'] = $this->prePackageLibrary->evaluateStatus(array_merge($set1StatusArray, $set2StatusArray));
        //=====================================

        $data['overallResults'] = (object)$dataSet;
        //   echo '<pre>';

        //   print_r($measurementSheet);
        //   echo '</pre>';

        //   exit;








        $approved = 0;
        $correctionFactor = 0;

        $decision = '';

        if ($sampleSize == 20 && $appliedMethod == 'Destructive') {
            $approved += 0;

            if ($approved > 0) {
                $decision .= ' Sample Failed the required test reject';
            }

            $correctionFactor += 0.640;
        } else if ($sampleSize == 50 && $appliedMethod == 'Non Destructive') {
            $approved += 3;
            if ($approved > 3) {
                $decision .= ' Sample Failed the required test reject';
            }
            $correctionFactor += 0.379;
        } else if ($sampleSize == 80 && $appliedMethod == 'Non Destructive') {
            $approved += 5;
            if ($approved > 5) {
                $decision = ' Sample Failed the required test reject';
            }
            $correctionFactor += 0.295;
        } else if ($sampleSize == 125 && $appliedMethod == 'Non Destructive') {
            $approved += 7;
            if ($approved > 7) {
                $decision .= ' Sample Failed the required test reject';
            }
            $correctionFactor += 0.234;
        }



        // if ($lotSize >= 100 && $lotSize <= 500 && $appliedMethod == 'Non Destructive') {
        //     $approved += 3;

        //     if (count($realT1) > 3) {
        //         $decision = ' Sample Failed the required test reject';
        //     }

        //     $correctionFactor += 0.379;
        // } else if ($lotSize >= 501 && $lotSize <= 3200 && $appliedMethod == 'Non Destructive') {
        //     $approved += 5;
        //     if (count($realT1) > 5) {
        //         $decision = ' Sample Failed the required test reject';
        //     }
        //     $correctionFactor += 0.295;
        // } else if ($lotSize > 3200) {
        //     $approved += 7;
        //     if (count($realT1) > 7 && $appliedMethod == 'Non Destructive') {
        //         $decision = ' Sample Failed the required test reject';
        //     }
        //     $correctionFactor += 0.234;
        // } else if ($lotSize >= 100 && $appliedMethod == 'Destructive') {
        //     $approved += 1;
        //     if (count($realT1)  > 1) {
        //         $decision = ' Sample Failed the required test reject';
        //     }
        //     $correctionFactor += 0.640;
        // }

        $data['approved'] = $approved;
        $data['correctionFactor'] = $correctionFactor;
        $data['decision'] = $decision;


        $title = $customerDetails->name . ' ' . $productDetails->commodity . ' ' . $productDetails->quantity . '' . $productDetails->unit . ' ' . str_shuffle(time());

        //=================Generating pdf====================


        $orientation =  'L';
        // $title = 'Chart-' . randomString();
        $data['title'] = $title;
        // $data['center'] = $this->CommonTasks->getCenterAddress();


        $pdfLibrary = new PdfLibrary();
        $pdfLibrary->renderPdf(orientation: $orientation, view: 'PrePackageTemplates/productTemplate', data: $data, title: $title);
    }


    public function prePackageReport()
    {
        $data['page'] = [
            "title" => "Pre Package Report",
            "heading" => "Pre Package Report",
        ];


        $data['user'] = $this->user;
        $data['region'] = centerName();
        return view('Pages/Prepackage/prePackageReport', $data);
    }

    public function generatePrepackageReport()
    {
        function getAllowedLimit($lot)
        {
            if ($lot > 100 && $lot <= 500) {
                return 3;
            } else if ($lot > 501 && $lot <= 3200) {
                return 3;
            } else if ($lot > 3200) {
                return 7;
            }
        }
        function evaluateStatus($measurementData, $declaredQuantity, $lotSize)
        {


            $withT1error = array_filter($measurementData, function ($data) {
                return $data->status == 1;
            });

            // filter t2
            $withT2error = array_filter($measurementData, function ($data) {
                return $data->status == 2;
            });

            $netQuantities = array_map(function ($net) use ($declaredQuantity) {
                return   (int)$declaredQuantity - (int)$net->net_quantity;
            }, $measurementData);

            // calculate individual error
            $individualError = array_reduce($netQuantities, function ($prev, $next) {
                return $prev + $next;
            });

            if (count($withT1error) > getAllowedLimit($lotSize) && count($withT2error) > 0) {
                return 'Failed';
            } else {
                return 'Pass';
            }
        }

        if ($this->request->getMethod() == 'POST') {


            $region = $this->getVariable('region');

            if (!$this->user->inGroup('officer')) {
                $params = ['region' => $region];
            } elseif ($this->user->inGroup('officer', 'manager')) {
                $params = ['region' => $this->user->collection_canter];
            }
            $customerDetails = $this->PrePackageModel->getRegionalPrepackedData($params);

            /// check if no data found based on parameters supplied
            if (count($customerDetails)  == 0) {
                return $this->response->setJSON([
                    'status' => 0,
                    'msg' => 'No Data Found',
                    'data' => [],
                    'token' => $this->token
                ]);
            } else {
                $company = [];
                $prepackage = array_map(function ($d) use ($company) {
                    $params = [
                        'product_id' => $d->product_id,
                        'quantity_id' => $d->quantity_id
                    ];
                    $product = $this->PrePackageModel->selectProduct($d->product_id);
                    $category = $product->analysis_category;
                    $measurements = $this->PrePackageModel->getMeasurementData($params);
                    array_push($company, $d->name);
                    return [

                        'name' => $d->name,
                        'date' => $d->created_at,
                        'details' => [
                            'measurements' => $measurements,
                            'commodity' => $d->commodity . ' ' . $d->quantity . '' . $d->unit,
                            'amount' => $d->amount,
                            'controlNumber' => $d->control_number,
                            'status' => evaluateStatus($measurements, $d->gross_quantity, $d->lot),
                            'region' => $d->region,
                            'location' => $d->location,
                            'date' => dateFormatter($d->created_at),
                            'unit_3' => $d->unit_3,
                        ]
                    ];
                }, $customerDetails);


                $unique = array();

                foreach ($prepackage as $arg) {
                    $tmp[$arg['name']][] = $arg['details'];
                }

                $output = array();

                foreach ($tmp as $customer => $data) {
                    $output[] = array(
                        'customer' => $customer,
                        'productData' => $data,
                        'region' => $data[0]['region'],
                        'location' => $data[0]['location'],
                        'date' => $data[0]['date'],
                        'controlNumber' => $data[0]['controlNumber'],
                        // 'controlNumber' => $data[0]['controlNumber'],
                    );
                }



                return $this->response->setJSON([
                    'status' => 1,
                    // 'data' => array_unique($unique),
                    'data' => $output,
                    //'data' => $unique,
                    'token' => $this->token
                ]);
            }
        }
    }




    //=================REPORT DOWNLOAD====================
    public function downloadPrepackageReport($collectionCenter)
    {
        function getAllowedErrorLimit($lot)
        {
            if ($lot > 100 && $lot <= 500) {
                return 3;
            } else if ($lot > 501 && $lot <= 3200) {
                return 3;
            } else if ($lot > 3200) {
                return 7;
            }
        }
        function evaluateProductStatus($measurementData, $declaredQuantity, $lotSize)
        {


            $withT1error = array_filter($measurementData, function ($data) {
                return $data->status == 1;
            });

            // filter t2
            $withT2error = array_filter($measurementData, function ($data) {
                return $data->status == 2;
            });

            $netQuantities = array_map(function ($net) use ($declaredQuantity) {
                return (int)$net->net_quantity - (int)$declaredQuantity;
            }, $measurementData);

            // calculate individual error
            $individualError = array_reduce($netQuantities, function ($prev, $next) {
                return $prev + $next;
            });

            if (count($withT1error) > getAllowedErrorLimit($lotSize) && count($withT2error) > 0) {
                return 'Failed';
            } else {
                return 'Pass';
            }
        }





        if (!$this->user->inGroup('officer', 'manager')) {
            $params = ['region' => $collectionCenter];
        } elseif ($this->user->inGroup('officer', 'manager')) {
            $params = ['region' => $this->user->collection_center];
        }
        $customerDetails = $this->PrePackageModel->getRegionalPrepackedData($params);


        $names = [];
        $prepackage = array_map(function ($d) use ($names) {

            $params = [
                'product_id' => $d->product_id,
                'quantity_id' => $d->quantity_id
            ];

            $product = $this->PrePackageModel->selectProduct($d->product_id);
            $category = $product->analysis_category;
            $measurements = $this->PrePackageModel->getMeasurementData($params);
            array_push($names, $d->name);
            return [

                'name' => $d->name,
                'date' => $d->created_at,
                'details' => [
                    'measurements' => $measurements,
                    // 'grossQuantity' => $d->gross_quantity,
                    // 'lot' => $d->lot,
                    'commodity' => $d->commodity . ' ' . $d->quantity . '' . $d->unit,
                    'amount' => $d->amount,
                    'controlNumber' => $d->control_number,
                    'status' => evaluateProductStatus($measurements, $d->gross_quantity, $d->lot),
                    'region' => $d->region,
                    'location' => $d->location,
                    'date' => dateFormatter($d->created_at),
                ]
            ];
        }, $customerDetails);;


        $unique = array();


        foreach ($prepackage as $arg) {
            $tmp[$arg['name']][] = $arg['details'];
        }

        (object)$output = array();

        foreach ($tmp as $customer => $data) {
            $output[] = array(
                'customer' => $customer,
                'productData' => $data,
                'region' => $data[0]['region'],
                'location' => $data[0]['location'],
                'date' => $data[0]['date'],
                'controlNumber' => $data[0]['controlNumber'],
            );
        }


        $title = 'Pre Package Report' . str_shuffle(time());

        $data['prePackageData'] = (object)$output;

        //=================Generating pdf====================


        $orientation =  'L';
        $title = 'Chart-' . randomString();
        $data['title'] = $title;
        // $data['center'] = $this->CommonTasks->getCenterAddress();


        $pdfLibrary = new PdfLibrary();
        $pdfLibrary->renderPdf(orientation: $orientation, view: 'PrePackageTemplates/prePackageRegionalReport', data: $data, title: $title);
    }
}
