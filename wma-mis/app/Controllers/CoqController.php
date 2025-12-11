<?php

namespace App\Controllers;

use App\Libraries\EsbLibrary;
use App\Controllers\BaseController;
use App\Models\PetroleumImportModel;
use CodeIgniter\HTTP\ResponseInterface;

class CoqController extends BaseController
{
    protected $model;
    protected $user;
    protected $token;
    protected $apiKey;
    //add a constructor method
    public function __construct()
    {
        $this->model = new PetroleumImportModel();
        $this->user = auth()->user();
        $this->token = csrf_hash();
        $this->apiKey = 'DjJnA1SsJNBtVA1pp1dHAJEeD4v3sdZG';
    }

    public function getVariable($variableName)
    {
        return  $this->request->getVar($variableName, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public function index()
    {
        $data['page'] = [
            'title' => 'Certificate Of Quantity',
            'heading' => 'Certificate Of Quantity',
        ];

        $data['user'] = $this->user;

        $data['vessels'] = $this->model->getVessels();
        return view('Pages/PetroleumImport/CertificateOfQuantity', $data);
    }


    // Helper method to clean number input (remove thousands separators and validate)
    private function cleanNumberInput($input)
    {
        // Remove commas and validate numeric input
        $cleanedInput = str_replace(',', '', $input);
        return is_numeric($cleanedInput) ? floatval($cleanedInput) : 0;
    }





    //add vessel method
    public function addCertificateOfQuantity()
    {
        try {
            $id = $this->getVariable('certificateId');
            $certificateId = 'COQ' . numString(10);
            $certificate = [
                'certificateId' => $certificateId,
                'vesselId' => $this->getVariable('vesselId'),
                'metricTonesInAir' => $this->cleanNumberInput($this->request->getVar('metricTonesInAir')),
                'metricTonesInVac' => $this->cleanNumberInput($this->request->getVar('metricTonesInVac')),
                'longTons' => $this->cleanNumberInput($this->request->getVar('longTons')),
                'litresAtTwentyCentigrade' => $this->cleanNumberInput($this->request->getVar('litresAtTwentyCentigrade')),
                'litresAtFifteenCentigrade' => $this->cleanNumberInput($this->request->getVar('litresAtFifteenCentigrade')),
                'usbblsAtSixtyFahrenheit' => $this->cleanNumberInput($this->request->getVar('usbblsAtSixtyFahrenheit')),
                'usgallonsAtSixtyFahrenheit' => $this->cleanNumberInput($this->request->getVar('usgallonsAtSixtyFahrenheit')),
                'stdDensityAtTwentyCentigrade' => $this->cleanNumberInput($this->request->getVar('stdDensityAtTwentyCentigrade')),
                'densityAtFifteenCentigrade' => $this->cleanNumberInput($this->request->getVar('densityAtFifteenCentigrade')),


            ];


            // return $this->response->setJSON([
            //     'status' => 0,
            //     'data' =>  $certificate,
            //     'token' => $this->token
            // ]);

            // exit;


            $query = $this->model->addCertificateOfQuantity($certificate);
            if ($query) {
                $certificate = $this->model->selectCertificateOfQuantity(['certificateId' => $certificateId]);
                $response = [
                    'status' => 1,
                    'certificate' => $this->renderCertificate($certificate),
                    'msg' => 'Certificate Of Quantity added successfully',
                    'token' => $this->token
                ];
            } else {
                $response = [
                    'status' => 0,
                    'msg' => 'Failed To Add Certificate Of Quantity',
                    'token' => $this->token
                ];
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




    public function getVesselCertificate($vesselId)
    {
        try {
            $certificates = $this->model->selectCertificateOfQuantity(['certificate_of_quantity.vesselId' => $vesselId]);


            if ($certificates) {
                $response = [
                    'status' => 1,
                    'certificate' => $this->renderCertificate($certificates),
                    'token' => $this->token
                ];
            } else {
                $response = [
                    'status' => 0,
                    'certificate' => '<h5><i class="far fa-exclamation-triangle"></i> No Certificate Of Quantity Found</h5>',
                    'token' => $this->token
                ];
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'certificate' => '',
                'msg' => $th->getMessage(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response);
    }












    public function renderCertificate($certificate)
    {
        $date = dateFormatter($certificate->berthingDate);
        $mt = formatNumber($certificate->metricTonesInAir);
        $mtv = formatNumber($certificate->metricTonesInVac);
        $lt = formatNumber($certificate->longTons);
        $lta20 = formatNumber($certificate->litresAtTwentyCentigrade);
        $lta15 = formatNumber($certificate->litresAtFifteenCentigrade);
        $usbbls = formatNumber($certificate->usbblsAtSixtyFahrenheit);
        $usgallons = formatNumber($certificate->usgallonsAtSixtyFahrenheit);
        $stdDensity = formatNumber($certificate->stdDensityAtTwentyCentigrade);
        $density = formatNumber($certificate->densityAtFifteenCentigrade);

        $html = <<<HTML
            <h6><b>SUPPLIER</b> :$certificate->supplier</h6>
            <h6><b>VESSEL</b> : $certificate->vesselName</h6>
            <h6><b>PORT</b> : $certificate->port</h6>
            <h6><b>PRODUCT</b> : $certificate->productType</h6>
            <h6><b>TABLE USED</b> : $certificate->tableUsed</h6>
            <h6><b>DATE</b> : $date</h6>


      

            <table id="vesselsTable" class="table table-bordered " id="vesselsTable">

                <tbody>
                    <tr>
                        <td style="width:50%">Metric Tones in Air</td>
                        <td style="width:50%">$mt</td>
                    </tr>
                    <tr>
                        <td style="width:50%">Metric Tones in Vacuum</td>
                        <td style="width:50%"> $mtv</td>
                    </tr>
                    <tr>
                        <td style="width:50%">Long Tons</td>
                        <td style="width:50%"> $lt</td>
                    </tr>
                  
                    <tr>
                        <td style="width:50%">Litres @ 20°C </td>
                        <td style="width:50%"> $lta20</td>
                    </tr>
                    <tr>
                        <td style="width:50%">Litres @ 15°C </td>
                        <td style="width:50%"> $lta15</td>
                    </tr>
                    <tr>
                        <td style="width:50%">USBBLS @ 60°F </td>
                        <td style="width:50%"> $usbbls</td>
                    </tr>
                    <tr>
                        <td style="width:50%">US GALLONS @ 60°F </td>
                        <td style="width:50%"> $usgallons</td>
                    </tr>
                    <tr>
                        <td style="width:50%">Std Density @ 20°C </td>
                        <td style="width:50%"> $stdDensity</td>
                    </tr>
                    <tr>
                        <td style="width:50%"> Density @ 15°C </td>
                        <td style="width:50%"> $density</td>
                    </tr>
                    <tr>
                        <td colspan="2" >
                            <button style="float: right" type="button" name="" id="" class="btn btn-primary"><i class="far fa-pen-square"></i> Edit Certificate</button>
                        </td>
                    </tr>
                </tbody>
            </table>    
    HTML;

        return $html;
    }



    public function requestCertificateOfQuantity()
    {
        try {

            $content = $this->request->getBody();
            $esb = new EsbLibrary();
            $reqData = $esb->verifyAndGetData($content);


            if (!$reqData) {
                return $esb->failureResponse([
                    'status' => 0,
                    'msg' => 'Invalid payload signature',
                ]);
            }

            $apiKey = $reqData['esbBody']['apiKey'];


            if ($apiKey != $this->apiKey || empty($apiKey)) {

                return $esb->failureResponse([
                    'status' => 0,
                    'msg' => 'Invalid API Key',

                ]);
            }

            $certificates = $this->model->getCertificatesOfQuantity(['certificate_of_quantity.vesselId !=' => '']);

            return $esb->successResponse([
                'status' => 1,
                'requestDate' => date('Y-m-d H:i:s'),
                'data' =>  $certificates,
            ]);
        } catch (\Throwable $th) {

            return $esb->failureResponse([
                'status' => 0,
                'msg' => $th->getMessage(),


            ]);
        }
    }
}
