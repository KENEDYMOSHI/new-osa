<?php

namespace App\Controllers;

use App\Libraries\EsbLibrary;
use App\Controllers\BaseController;
use App\Models\PetroleumImportModel;
use CodeIgniter\HTTP\ResponseInterface;

class SailingReportController extends BaseController
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
            'title' => 'Vessel Sailing Report',
            'heading' => 'Vessel Sailing Report',
        ];

        $data['user'] = $this->user;

        $data['vessels'] = $this->model->getVessels();
        return view('Pages/PetroleumImport/VesselSailingReport', $data);
    }


    // Helper method to clean number input (remove thousands separators and validate)
    private function cleanNumberInput($input)
    {
        // Remove commas and validate numeric input
        $cleanedInput = str_replace(',', '', $input);
        return is_numeric($cleanedInput) ? floatval($cleanedInput) : 0;
    }




    //add vessel method
    public function addSailingReport()
    {
        try {
            $id = $this->getVariable('sailingId');
            $sailingId = 'VSR' . numString(10);
            $report = [
                'sailingId' => $sailingId,
                'vesselId' => $this->getVariable('vesselId'),
                'quantityMt' => $this->cleanNumberInput($this->getVariable('quantityMt')),
                'quantityLitre' => $this->cleanNumberInput($this->getVariable('quantityLitre')),
                'anchorageTime' => $this->getVariable('anchorageTime'),
                'noticeOfReadiness' => $this->getVariable('noticeOfReadiness'),
                'berthingTime' => $this->getVariable('berthingTime'),
                'commencedDischarging' => $this->getVariable('commencedDischarging'),
                'completedTimeDischarging' => $this->getVariable('completedTimeDischarging'),
                'vesselDepartureTime' => $this->getVariable('vesselDepartureTime'),






            ];


            // return $this->response->setJSON([
            //     'status' => 0,
            //     'data' =>  $report,
            //     'token' => $this->token
            // ]);

            // exit;


            $query = $this->model->addVesselSailing($report);
            if ($query) {
                $report = $this->model->selectVesselSailing(['sailingId' => $sailingId]);
                $response = [
                    'status' => 1,
                    'sailingReport' => $this->renderSailingReport($report),
                    'msg' => 'Vessel Sailing Report added successfully',
                    'token' => $this->token
                ];
            } else {
                $response = [
                    'status' => 0,
                    'msg' => 'Failed To Add Vessel Sailing Report',
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




    public function getSailingReport($vesselId)
    {
        try {
            $reports = $this->model->selectVesselSailingReports(['vessel_sailing.vesselId' => $vesselId]);


            if ($reports) {
                $response = [
                    'status' => 1,
                    'sailingReport' => $this->renderSailingReport($reports),
                    'token' => $this->token
                ];
            } else {
                $response = [
                    'status' => 0,
                    'sailingReport' => '<h5><i class="far fa-exclamation-triangle"></i> No Vessel Sailing Report Found</h5>',
                    'token' => $this->token
                ];
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'sailingReport' => '',
                'msg' => $th->getMessage(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response);
    }












    public function renderSailingReport($report)
    {

        $quantityMt = formatNumber($report->quantityMt);
        $quantityLitre = formatNumber($report->quantityLitre);

        $anchorageTime = dateTimeFormatter($report->anchorageTime);
        $noticeOfReadiness = dateTimeFormatter($report->noticeOfReadiness);
        $berthingTime = dateTimeFormatter($report->berthingTime);
        $commencedDischarging = dateTimeFormatter($report->commencedDischarging);
        $completedTimeDischarging = dateTimeFormatter($report->completedTimeDischarging);
        $vesselDepartureTime = dateTimeFormatter($report->vesselDepartureTime);





        $html = <<<HTML
         
            <h6><b>VESSEL</b> : $report->vesselName</h6>
            <h6><b>PORT</b> : $report->port</h6>
            <h6><b>PRODUCT</b> : $report->productType</h6>
            <h6><b>ARRIVAL QUANTITY</b> : $quantityMt MT / $quantityLitre LITRE </h6>
                <table class="table table-bordered ">
                    <thead>
                        <tr>
                            <th>AT</th>
                            <th>NOR</th>
                            <th>BT</th>
                            <th>CD</th>
                            <th>CTD</th>
                            <th>VTD</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>$anchorageTime hrs</td>
                            <td>$noticeOfReadiness hrs</td>
                            <td>$berthingTime hrs</td>
                            <td>$commencedDischarging hrs</td>
                            <td>$completedTimeDischarging hrs</td>
                            <td>$vesselDepartureTime hrs</td>


                        </tr>
                        <tr>
                            <td colspan="6"><button class="btn btn-primary" style="float: right;" ><i class="far fa-pen-square    "></i> Edit Report</button></td>
                        </tr>

                    </tbody>
                </table>

                <h4>NOTE :</h4>
                <h5><b>AT</b> : Anchorage Time</h5>
                <h5><b>NOR</b> : Notice Of Readiness </h5>
                <h5><b>BT</b> : Berthing Time</h5>
                <h5><b>CD</b> : Commenced Discharging</h5>
                <h5><b>CDT</b> : Completed Time Discharging</h5>
                <h5><b>VDT</b> : Vessel Departure Time </h5>   
    HTML;

        return $html;
    }


    // public function sailingReports()
    // {
    //     try {
    //         $reports = $this->model->selectVesselSailingReports();
    //         $response = [
    //             'status' => 1,
    //             'data' =>  $reports,
             
    //         ];
    //     } catch (\Throwable $th) {
    //         $response = [
    //             'status' => 0,
    //             'msg' => $th->getMessage(),
             
    //         ];
    //     }
    //     return $this->response->setJSON($response);
    // }

    public function sailingReports()
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

            $reports = $this->model->selectVesselSailingReports();

            return $esb->successResponse([
                'status' => 1,
                'data' => $reports,
            ]);
        } catch (\Throwable $th) {

            return $esb->failureResponse([
                'status' => 0,
                'msg' => $th->getMessage(),


            ]);
        }
    }
}
