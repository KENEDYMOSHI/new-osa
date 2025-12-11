<?php

namespace App\Controllers;

use App\Models\AppModel;



use App\Models\BillModel;
use PHPUnit\Util\Printer;
use App\Models\PrePackageModel;
use App\Models\CertificateModel;
use App\Controllers\BaseController;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ConformityCertificateController extends BaseController
{

    protected $user;
    protected $collectionCenter;
    protected $appModel;
    protected $billModel;
    protected $certificateModel;
    protected $token;
    protected $uniqueId;


    public function __construct()
    {
        $this->appModel = new AppModel();
        $this->billModel = new BillModel();
        $this->certificateModel = new CertificateModel();
        helper('date');
        $this->token = csrf_hash();
        $this->user = auth()->user();
        $this->collectionCenter = $this->user->collection_center;
        $this->uniqueId = $this->user->unique_id;



        //     $dumpSettings = array(),
        // $pdoSettings = array()
    }

    public function getVariable($var)
    {
        return $this->request->getVar($var, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public function index()
    {



        $data['page'] = ['title' => 'Certificates', 'heading' => 'Certificates',];


        return view('Pages/certificates', $data);
    }


    public function generateCertificateData($controlNumber)
    {

        $billModel = new BillModel();

        $bill = $billModel->getBill($controlNumber);
        $billItems = $billModel->fetchBillItems($bill->RequestId);
        $products = array_map(function ($item) {
            return $item->BillItemRef;
        }, $billItems);
        // ================certificates=================================

        $cert = $this->certificateModel->getLastConformityCertificate(['region' => $bill->CollectionCenter]);
        $certificateNumber = $cert->certificateNumber;

        $newCertNumber = preg_replace_callback('/\d+/', function ($matches) {
            return str_pad($matches[0] + 1, strlen($matches[0]), '0', STR_PAD_LEFT);
        }, $certificateNumber);
        $params = [
            'certificateId' => randomString(),
            'certificateNumber' => $newCertNumber,
            'activity' => '142101210007',
            'region' => $bill->CollectionCenter,
            'officer' => $bill->UserId,
            'customer' => $bill->PyrName,
            'mobile' => $bill->PyrCellNum,
            'address' => '',
            'controlNumber' => $controlNumber,
            'products' =>  json_encode($products),

        ];




        $this->certificateModel->addConformityCertificate($params);
    }


    public function searchConformityCertificate()
    {
        try {
            session()->remove('surround');
            $controlNumber = trim($this->request->getGet('controlNumber'));
            $activity = $this->request->getGet('activity');
            $name = $this->request->getGet('name');
            $month = $this->request->getGet('month');
            $year = $this->request->getGet('year');
            $page = $this->request->getGet('page');
            $limit = $this->request->getGet('perPage');

            $data = [
                'controlNumber' => $controlNumber,
                'MONTH(createdAt)' => $month,
                'YEAR(createdAt)' => $year,
                'region' => $this->user->inGroup('officer', 'manager') ? $this->collectionCenter : '',
            ];

            $params = array_filter($data, fn($param) => $param  != '');

            $checkCertificate = $this->certificateModel->fetchConformityCertificate(['controlNumber' => $controlNumber]);


            $bill = $this->billModel->getBill($controlNumber);

            $allowedActivities = ['142101210007']; // Allowed activities
            
            // Ensure $bill->Activity is not null or empty before processing
            if (empty($bill->Activity)) {
                return $this->response->setJSON([
                    'status' => 0,
                    'message' => '<p>No Data Found</p>',
                    'token' => $this->token
                ]);
            }
            
            // Convert $bill->Activity into an array (handles both single and multiple values)
            $activityList = explode(',', trim($bill->Activity));
            
            // Check if at least one value exists in the allowed list
            if (!array_intersect($activityList, $allowedActivities)) {
                return $this->response->setJSON([
                    'status' => 0,
                    'message' => '<p>The Control number is not for Pre Package</p>',
                    'token' => $this->token
                ]);
            }

            if (isset($data['controlNumber']) && empty($checkCertificate)) {
                $this->generateCertificateData($controlNumber);
            }

            


            $offset = ($page - 1) * $limit;


            $certData = $this->certificateModel->findConformityCertificates($params, $name, $limit, $offset);
            $total =  $this->certificateModel->countConformityCertificates();
            $pager = \Config\Services::pager();
            $pager->makeLinks($page, $limit, $total);
            // $pager->setSurroundCount(2);

            $links = $pager->links('default', 'customTemplate');
            $params = array_filter($data, fn($param) => $param  != '');






            // return $this->response->setJSON([
            //     'status' => 0,
            //     'data' =>$certData ,
            //     'links' =>$links ,
            //     'token' => $this->token,
            //     'params' => $params,
            //     'page' =>$page ,
            //     'offset' =>$offset ,
            //     'limit' => $limit,
            //     'total' => $total,
            // ]);
            // exit;


            $prePackageModel = new PrePackageModel();
            $certificates = array_map(function ($data) use ($prePackageModel) {
                $ids = json_decode($data->products);
                $products = $prePackageModel->products($ids);
                $data->products = array_map(fn($product) => $product->product, $products);
                return $data;
            }, $certData);

            $tr = '';

            foreach ($certificates as $certificate) {
                $date = dateFormatter($certificate->createdAt);
                $isPaid = $this->certificateModel->isPaid(['PayCtrNum' => $certificate->controlNumber]);
                $ol = '';
                foreach ($certificate->products as $product) {
                    $ol .= <<<HTML
                         <li>$product</li>
                    HTML;
                }

                if ($isPaid) {
                    $button = <<<HTML
                      
                            <button data-toggle="tooltip" data-placement="top" title="View Certificate"  type="button" class="btn btn-primary btn-sm" onclick="viewCertificate('$certificate->certificateId')"><i class="far fa-eye"></i></button>
                          
                    HTML;
                } else {
                    $button = '<button data-toggle="tooltip" data-placement="top" title="Not Paid"  type="button" class="btn btn-default btn-sm"><i class="far fa-ban"></i></button>';
                }
                $tr .= <<<HTML
                     <tr>
                        <td>$date</td>
                        <td>$certificate->customer</td>
                        <td>$certificate->certificateNumber</td>
                        <td>$certificate->controlNumber</td>
                        <td>
                           <ol style='padding:0;margin-left: 10px;'>
                            $ol
                           </ol>
                        </td>
                        <td>
                           $button
                        </td>
                    </tr>     
                HTML;
            }
            if (!empty($certData)) {
                return $this->response->setJSON([
                    'status' => 1,
                    'params' => $params,
                    'links' => $links,
                    'certificates' => $tr,
                    'token' => $this->token
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 0,
                    'message' => '<p>No Data Found</p>',
                    'token' => $this->token
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




    public function prepareConformityCertificate($certificateId)
    {
        // Set the folder path
        $folderPath = 'public/certificates/'; // Adjust the folder path accordingly



        // Combine controlNumber and activity to form the expected name
        $name = "$certificateId";

        // Build the search pattern
        $searchPattern = $folderPath . "*{$name}*";

        // Use glob to search for matching files
        $file = glob($searchPattern);

        $relativeFilePath = str_replace(ROOTPATH, '', $file);
        $fileUrl = base_url($relativeFilePath);

        if (!empty($file)) {
            return $fileUrl;
        } else {
            $certificateData = $this->certificateModel->fetchConformityCertificate(['certificateId' => $certificateId]);
            if (!empty($certificateData)) {
                $defaultSign = '';
                $officer = $this->certificateModel->findUser(['unique_id' => $certificateData->officer]);
                $officerSignature = 'sign/' . $officer->username . '.png';
                $manager = $this->certificateModel->findUser(['collection_center' => $certificateData->region, 'group' => 'manager']);
                $managerSignature = 'sign/' . $manager->username . '.png';
                $date = dateFormatter($certificateData->createdAt);
                $data = (object)[
                    'certificateId' => $certificateId,
                    'certificateNumber' => $certificateData->certificateNumber,
                    'controlNumber' => $certificateData->controlNumber,
                    'officer' => (object)['name' => $officer->username, 'signature' => (file_exists($officerSignature) ? $officerSignature : $defaultSign)],
                    'manager' => (object)['name' => $manager->username, 'signature' => (file_exists($managerSignature) ? $managerSignature : $defaultSign)],


                    'date' => $date,
                    'client' => (object)[
                        'name' => ucwords(strtolower($certificateData->customer)),
                        'mobile' => '+' . $certificateData->mobile,
                        'address' => $certificateData->address,
                        'region' => str_replace('Wakala Wa Vipimo', '', wmaCenter($certificateData->region)->centerName),

                    ]
                ];

                // Printer($data);
                // exit;

                $fileUrl =  $this->generateConformityCertificate($data);
                return $fileUrl;
            }
        }
    }


    public function generateConformityCertificate($data)
    {
        $imageManager = new ImageManager(new Driver());

        //     $img = base_url('sign/kenedy.png');
        //     echo <<<HTML
        //     <img src="$img" width='700'>         
        // HTML;

        //     exit;


        $qrCodeData = base_url('verifyCertificate/Conformity/' . $data->certificateId);

        $fontRegular = 'assets/fonts/Roboto.ttf';
        $fontLight = 'assets/fonts/Roboto-Light.ttf';

        $fontSize = 25;

        // Load the background image
        $background = $imageManager->read('assets/confo.jpg');
        // Create a new image instance
        $canvas = $imageManager->create(1241, 1755, '#ffffff'); // Width, Height, Background Color
        //    $img = $imageManager->create(1000, 600, '#ffffff'); // Width, Height, Background Color

        // Insert the background image
        $canvas->place($background, 'top-left');
        $region = $data->client->region;

        //set certificate number
        // $canvas->text($data->certificateNumber, 900, 480, function ($font) use ($fontLight, $fontRegular, $fontSize) {
        //     $font->size(25);
        //     $font->fileName($fontRegular);
        //     $font->color('#333333');
        // });
        //set officer name
        $canvas->text($data->officer->name, 565, 687, function ($font) use ($fontLight, $fontRegular, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontRegular);
            $font->color('#333333');
        });

        if (file_exists($data->officer->signature)) {
            //officer signature
            $officerSign = $imageManager->read($data->officer->signature);
            $officerSign->resize(140, 50);
            $canvas->place($officerSign, 'top-left', 215, 990);
        }


        //set client name
        $canvas->text($data->client->name, 233, 800, function ($font) use ($fontLight, $fontRegular, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontRegular);
            $font->color('#333333');
        });
        //set client address
        $canvas->text($data->client->address, 143, 855, function ($font) use ($fontLight, $fontRegular, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontRegular);
            $font->color('#333333');
        });
        //set client phone number
        $canvas->text($data->client->mobile, 142, 916, function ($font) use ($fontLight, $fontRegular, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontRegular);
            $font->color('#333333');
        });
        //set verification date
        $canvas->text($data->date, 153, 974, function ($font) use ($fontLight, $fontRegular, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontRegular);
            $font->color('#333333');
        });




        //set manager name
        $canvas->text($data->manager->name, 400, 1094, function ($font) use ($fontLight, $fontRegular, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontRegular);
            $font->color('#333333');
        });
        //set region
        $canvas->text($region, 179, 1153, function ($font) use ($fontLight, $fontRegular, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontRegular);
            $font->color('#333333');
        });

        //set manager sign
        if (file_exists($data->manager->signature)) {
            $managerSign = $imageManager->read($data->manager->signature);
            $managerSign->resize(140, 50);
            $canvas->place($managerSign, 'top-left', 215, 1167);
        }




        //set verification date
        $canvas->text($data->date, 159, 1272, function ($font) use ($fontLight, $fontRegular, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontRegular);
            $font->color('#333333');
        });






        $qrCode =  QRCode($qrCodeData);

        // // Load the QR code image and resize it
        $qrCodeImage = $imageManager->read($qrCode);
        $qrCodeImage->resize(200, 200);

        // // Insert the QR code at the bottom right corner
        $canvas->place($qrCodeImage, 'center', 0, 670);

        $title =   $data->certificateId . '.jpg';
        $savePath = 'public/certificates/' . $title;
        // $savePath = WRITEPATH . $title;
        // $savePath = 'stickers/' . $title;
        $canvas->toJpeg()->save($savePath);

        $imgPath = base_url($savePath);

        return $imgPath;
        // echo <<<HTML
        //     <img src="$imgPath" width='700'>         
        // HTML;
    }

    public function viewConformityCertificate()
    {
        $certificateId = $this->request->getVar('certificateId');
        $certificateData = $this->certificateModel->fetchConformityCertificate(['certificateId' => $certificateId]);
        $fileUrl = $this->prepareConformityCertificate($certificateId);
        $file = basename($fileUrl);
        $fileName = $certificateData->customer . '-' . $file;

        return $this->response->setJSON([
            'status' => 1,
            'button' => "<a href='$fileUrl' download='$fileName' class='btn btn-sm btn-primary'><i class='far fa-download'></i> Download</a>",
            'certificate' => "<img src='$fileUrl' width='100%'>",
            'token' => $this->token
        ]);
    }

    public function printConformityCertificate($certificateId)
    {

        $fileUrl = $this->prepareConformityCertificate($certificateId);



        // // URL of the file you want to download
        // // $fileUrl = 'http://localhost:8080/certificates/shgf6734gfshjbfw7etyhw4ighw7e8tyh4gn.jpg';

        // Fetch the file content using cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fileUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $fileContent = curl_exec($ch);
        curl_close($ch);

        // Set appropriate headers for file download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $certificateId . '.jpg"');

        // Output the file content
        echo $fileContent;
    }




    public function importSignature()
    {
        $csvFilePath = 'sign/sign.csv';

        // Check if the file exists
        if (!file_exists($csvFilePath)) {
            echo 'CSV file not found.';
        }

        // Load the CSV file
        $file = fopen($csvFilePath, 'r');

        // Skip the header row
        $header = fgetcsv($file);

        // You need to replace 'ModelName' with your actual model name
        // $model = new \App\Models\ModelName();

        // Loop through each row in the CSV file
        while (($row = fgetcsv($file)) !== false) {
            // Insert data into the database
            $data = array_combine($header, $row);

            $fileName =  preg_replace('/\.+/', '.', $data['file']);
            $data['file'] = preg_replace('/\s+/', '', $fileName);
            // Printer($data);
            $this->certificateModel->addSignatureData($data);
        }

        fclose($file);
    }


    public function generateCertificateNumber()
    {



        //get region name
        $region = wmaCenter($this->collectionCenter)->centerName;


        //get 3 first letter of region name
        $prefix = strtoupper(substr($region, 0, 3));


        // Fetch the last sticker data for the given activity
        $lastCertificate = $this->certificateModel->getLastConformityCertificate(['region' => $this->collectionCenter]);

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

        Printer($currentCertificate);

        //  echo $prefix;




    }
}
