<?php

namespace App\Controllers;

use App\Models\AppModel;



use App\Models\BillModel;
use App\Models\CertificateModel;
use App\Controllers\BaseController;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;




class CorrectnessCertificateController extends BaseController
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
        $items = array_map(function ($item) {
            return $item->ItemName;
        }, $billItems);
        $gfs = explode(',', $bill->Activity);
        // ================certificates=================================

        $cert = $this->certificateModel->getLastCorrectnessCertificate(['region' => $bill->CollectionCenter]);
        $certificateNumber = $cert->certificateNumber;

        $newCertNumber = preg_replace_callback('/\d+/', function ($matches) {
            return str_pad($matches[0] + 1, strlen($matches[0]), '0', STR_PAD_LEFT);
        }, $certificateNumber);
        $params = [
            'certificateId' => randomString(),
            'certificateNumber' => $newCertNumber,
            'activities' => json_encode($gfs),
            'region' => $bill->CollectionCenter,
            'officer' => $bill->UserId,
            'customer' => $bill->PyrName,
            'mobile' => $bill->PyrCellNum,
            'address' => '',
            'controlNumber' => $controlNumber,
            'items' =>  json_encode($items),

        ];


 
        $this->certificateModel->addCorrectnessCertificate($params);
    }




    public function searchCorrectnessCertificate()
    {
        try {
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
            $checkCertificate = $this->certificateModel->fetchCorrectnessCertificate(['controlNumber' => $controlNumber]);

            $bill = $this->billModel->getBill($controlNumber);
            if($bill->Activity == '142101210007') {
               return $this->response->setJSON([
                'status' => 0,
                'message' => '<p>No Data Found</p>',
                'token' => $this->token
               ]);
            }

            if (isset($data['controlNumber']) && empty($checkCertificate)) {
                $this->generateCertificateData($controlNumber);
            }




            $offset = ($page - 1) * $limit;


            $certData = $this->certificateModel->findCorrectnessCertificates($params, $name, $limit, $offset);
            $total =  $this->certificateModel->countCorrectnessCertificates();
            $pager = \Config\Services::pager();
            $pager->makeLinks($page, $limit, $total);
            $links = $pager->links('default', 'customTemplate');



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


            $certificates = array_map(function ($data) {

                $data->items  = json_decode($data->items);
                return $data;
            }, $certData);


            // return $this->response->setJSON([
            //     'status' => 0,
            //     'data' => $certificates,
            //     'token' => $this->token
            //   ]);

            //   exit;

            $tr = '';
            $sn = 1;
            foreach ($certificates as $certificate) {
                $date = dateFormatter($certificate->createdAt);

                $isPaid = $this->certificateModel->isPaid(['PayCtrNum' => $certificate->controlNumber]);
                // $isPaid = true;
                $ol = '';
                foreach ($certificate->items as $item) {
                    $ol .= <<<HTML
                         <li>$item</li>
                    HTML;
                }

                if ($isPaid) {
                    $button = <<<HTML
                        
                            <button data-toggle="tooltip" data-placement="top" title="View Certificate"  type="button" class="btn btn-primary btn-sm" onclick="viewCertificate('$certificate->certificateId')"><i class="far fa-eye"></i></button>
                               
                    HTML;
                } else {
                    $button = '<button data-toggle="tooltip" data-placement="top" title="Not Paid"  type="button" class="btn btn-default btn-sm"><i class="far fa-ban"></i></button>';
                }
                $no = $sn++;
                $tr .= <<<HTML
                     <tr>
               
                        <td>$date</td>
                        <td>$certificate->customer</td>
                        <td>$certificate->certificateNumber</td>
                        <td>$certificate->controlNumber</td>
                        <td style="padding-left: 10px;">
                           <ol style='padding:0;margin-left: 10px;'>
                            $ol
                           </ol>
                        </td>
                        <td>$button</td>
                    </tr>     
                HTML;
            }
            if (!empty($certData)) {
                return $this->response->setJSON([
                    'status' => 1,
                    'params' => $params,
                    'certificates' => $tr,
                    'links' => $links,
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




    public function prepareCorrectnessCertificate($certificateId)
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
            $certificateData = $this->certificateModel->fetchCorrectnessCertificate(['certificateId' => $certificateId]);
            if (!empty($certificateData)) {
                $defaultSign = '';
                $officer = $this->certificateModel->findUser(['unique_id' => $certificateData->officer]);
                $officerSignature = 'sign/' . $officer->username . '.png';
                $manager = $this->certificateModel->findUser(['collection_center' => $certificateData->region, 'group' => 'manager']);
                $managerSignature = 'sign/' . $manager->username . '.png';
                $date = dateFormatter($certificateData->createdAt);

                $activities = json_decode($certificateData->activities, true);

                if (!is_array($activities)) {
                    // Convert the single value to an array
                    $activities = [$activities];
                }


                $items = array_map(fn($item) => activityName($item), array_unique($activities));



                $data = (object)[
                    'certificateId' => $certificateId,
                    'trade' => implode('| ', $items),
                    'certificateNumber' => $certificateData->certificateNumber,
                    'controlNumber' => $certificateData->controlNumber,
                    'officer' => (object)['name' => $officer->username, 'signature' => (file_exists($officerSignature) ? $officerSignature : $defaultSign)],
                    'manager' => (object)['name' => $manager->username, 'signature' => (file_exists($managerSignature) ? $managerSignature : $defaultSign)],


                    'date' => $date,
                    'client' => (object)[
                        'name' => ucwords(strtolower($certificateData->customer)),
                        'mobile' => '+' . $certificateData->mobile,
                        'region' => str_replace('Wakala Wa Vipimo', '', wmaCenter($certificateData->region)->centerName),

                    ]
                ];

                // Printer($data);
                // exit;

                $fileUrl =  $this->generateCorrectnessCertificate($data);
                return $fileUrl;
            }
        }
    }


    public function generateCorrectnessCertificate($data)
    {
        // $imageManager = new ImageManager(new Driver());

        $imageManager = new ImageManager(new Driver());



        $qrCodeData = base_url('verifyCertificate/Correctness/' . $data->certificateId);

        $fontRegular = 'assets/fonts/Roboto.ttf';
        $fontLight = 'assets/fonts/Roboto-Light.ttf';

        $fontSize = 25;

        // Load the background image
        $background = $imageManager->read('assets/correct.jpg');
        // Create a new image instance
        $canvas = $imageManager->create(1241, 1755, '#ffffff'); // Width, Height, Background Color
        //    $img = $imageManager->create(1000, 600, '#ffffff'); // Width, Height, Background Color

        // Insert the background image
        $canvas->place($background, 'top-left');

        $region = $data->client->region;


        //set certificate number
        // $canvas->text($data->certificateNumber, 950, 400, function ($font) use ($fontLight, $fontRegular, $fontSize) {
        //     $font->size($fontSize);
        //     $font->fileName($fontRegular);
        //     $font->color('#333333');
        // });
        //set client name
        $canvas->text($data->client->name, 170, 707, function ($font) use ($fontLight, $fontRegular, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontRegular);
            $font->color('#333333');
        });

        //set client name
        $canvas->text($region, 180, 770, function ($font) use ($fontLight, $fontRegular, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontRegular);
            $font->color('#333333');
        });
        //set client trade
        $canvas->text($data->trade, 168, 836, function ($font) use ($fontLight, $fontRegular, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontRegular);
            $font->color('#333333');
        });
        $canvas->text($data->date, 159, 900, function ($font) use ($fontLight, $fontRegular, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontRegular);
            $font->color('#333333');
        });
        //set officer name
        $canvas->text(ucfirst($data->officer->name), 187, 1434, function ($font) use ($fontLight, $fontRegular, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontRegular);
            $font->color('#333333');
        });
        //region left
        $canvas->text('Wma ' . $region, 219, 1470, function ($font) use ($fontLight, $fontRegular, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontRegular);
            $font->color('#333333');
        });

        if (file_exists($data->officer->signature)) {
            //officer signature
            $officerSign = $imageManager->read($data->officer->signature);
            $officerSign->resize(130, 50);
            $canvas->place($officerSign, 'top-left', 265, 1475);
        }



        //date left side
        $canvas->text($data->date, 177, 1547, function ($font) use ($fontLight, $fontRegular, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontRegular);
            $font->color('#333333');
        });
        //manager name
        $canvas->text($data->manager->name, 880, 1434, function ($font) use ($fontLight, $fontRegular, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontRegular);
            $font->color('#333333');
        });
        //region right side
        $canvas->text('Wma ' . $region, 902, 1470, function ($font) use ($fontLight, $fontRegular, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontRegular);
            $font->color('#333333');
        });

        if (file_exists($data->manager->signature)) {
            //manager signature
            $managerSign = $imageManager->read($data->manager->signature);
            $managerSign->resize(130, 50);
            $canvas->place($managerSign, 'top-left', 945, 1478);
        }




        //date right side
        $canvas->text($data->date, 863, 1547, function ($font) use ($fontLight, $fontRegular, $fontSize) {
            $font->size($fontSize);
            $font->fileName($fontRegular);
            $font->color('#333333');
        });




        $qrCode = QRCode($qrCodeData);

        // Load the QR code image and resize it
        $qrCodeImage = $imageManager->read($qrCode);
        $qrCodeImage->resize(200, 200);

        // Insert the QR code at the bottom right corner
        $canvas->place($qrCodeImage, 'center', 0, 630);

        $title =   $data->certificateId . '.jpg';
        $savePath = 'public/certificates/' . $title;
        // $savePath = 'stickers/' . $title;
        // $savePath = WRITEPATH  . $title;
        $canvas->toJpeg()->save($savePath);

        $imgPath = base_url($savePath);

        return $imgPath;
        // echo <<<HTML
        //     <img src="$imgPath" width='700'>         
        // HTML;
    }

    public function viewCorrectnessCertificate()
    {
        $certificateId = $this->request->getVar('certificateId');
        $certificateData = $this->certificateModel->fetchCorrectnessCertificate(['certificateId' => $certificateId]);
        $fileUrl = $this->prepareCorrectnessCertificate($certificateId);
        $file = basename($fileUrl);
        $fileName = $certificateData->customer . '-' . $file;

        return $this->response->setJSON([
            'status' => 1,
            'button' => "<a href='$fileUrl' download='$fileName' class='btn btn-sm btn-primary'><i class='far fa-download'></i> Download</a>",
            'certificate' => "<img src='$fileUrl' width='100%'>",
            'token' => $this->token
        ]);
    }

    public function printCorrectnessCertificate($certificateId)
    {

        $fileUrl = $this->prepareCorrectnessCertificate($certificateId);



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
        $lastCertificate = $this->certificateModel->getLastCertificate(['region' => $this->collectionCenter]);

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
