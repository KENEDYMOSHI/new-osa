<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\EsbLibrary;
use App\Models\PetroleumImportModel;
use CodeIgniter\HTTP\ResponseInterface;

class OutturnReportController extends BaseController
{
    protected $model;
    protected $user;
    protected $token;
    protected $userId;
    //add a constructor method
    public function __construct()
    {
        $this->model = new PetroleumImportModel();
        $this->user = auth()->user();
        $this->token = csrf_hash();
        $this->userId = auth()->user()->unique_id;
    }

    public function getVariable($variableName)
    {
        return  $this->request->getVar($variableName, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public function index()
    {
        $data['page'] = [
            'title' => 'Vessel Outturn Report',
            'heading' => 'Vessel Outturn Report',
        ];

        $data['user'] = $this->user;

        $data['vessels'] = $this->model->getVessels();
        return view('Pages/PetroleumImport/VesselOutturnReport', $data);
    }


    // Helper method to clean number input (remove thousands separators and validate)
    private function cleanNumberInput($input)
    {
        // Remove commas and validate numeric input
        $cleanedInput = str_replace(',', '', $input);
        return is_numeric($cleanedInput) ? floatval($cleanedInput) : 0;
    }




    //add vessel method
    public function addOutturnReport()
    {
        try {
            $id = $this->getVariable('outturnId');
            $outturnId = 'VOT' . numString(10);
            $vesselId = $this->getVariable('vesselId');
            $report = [
                'outturnId' => $outturnId,
                'vesselId' => $vesselId,
                'terminal' =>  $this->getVariable('terminal'),
                'shipMt' => $this->cleanNumberInput($this->getVariable('shipMt')),
                'shipVol' => $this->cleanNumberInput($this->getVariable('shipVol')),
                'shoreMt' => $this->cleanNumberInput($this->getVariable('shoreMt')),
                'shoreVol' => $this->cleanNumberInput($this->getVariable('shoreVol')),
                'billOfLadingMt' => $this->cleanNumberInput($this->getVariable('billOfLadingMt')),
                'billOfLadingVol' => $this->cleanNumberInput($this->getVariable('billOfLadingVol')),
                'dischargeQuantityMtDifference' => $this->cleanNumberInput($this->getVariable('dischargeQuantityMtDifference')),
                'dischargeQuantityVolDifference' => $this->cleanNumberInput($this->getVariable('dischargeQuantityVolDifference')),
                'dischargeQuantityMtDifferencePercent' => $this->cleanNumberInput($this->getVariable('dischargeQuantityMtDifferencePercent')),
                'dischargeQuantityVolDifferencePercent' => $this->cleanNumberInput($this->getVariable('dischargeQuantityVolDifferencePercent')),
                'billOfLadingMtDifference' => $this->cleanNumberInput($this->getVariable('billOfLadingMtDifference')),
                'billOfLadingVolDifference' => $this->cleanNumberInput($this->getVariable('billOfLadingVolDifference')),
                'billOfLadingMtDifferencePercent' => $this->cleanNumberInput($this->getVariable('billOfLadingMtDifferencePercent')),
                'billOfLadingVolDifferencePercent' => $this->cleanNumberInput($this->getVariable('billOfLadingVolDifferencePercent')),
                'createdBy' => $this->userId,









            ];


            // return $this->response->setJSON([
            //     'status' => 0,
            //     'data' =>  $report,
            //     'token' => $this->token
            // ]);

            // exit;


            $query = $this->model->addVesselOutturn($report);
            if ($query) {
                $report = $this->model->getVesselOutturnReports(['vessel_outturn.vesselId' => $vesselId]);
                $response = [
                    'status' => 1,
                    'outturnReport' => $this->renderOutturnReport($report),
                    'msg' => 'Vessel Outturn Report added successfully',
                    'token' => $this->token
                ];
            } else {
                $response = [
                    'status' => 0,
                    'msg' => 'Failed To Add Vessel Outturn Report',
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




    public function getOutturnReport($vesselId)
    {
        try {
            $report = $this->model->getVesselOutturnReports(['vesselId' => $vesselId]);


            if ($report) {
                $response = [
                    'status' => 1,
                    'outturnReport' => $this->renderOutturnReport($report),
                    'token' => $this->token,
                    'report' =>  $report,
                    'vesselId' => $vesselId,
                ];
            } else {
                $response = [
                    'status' => 0,
                    'outturnReport' => '<h5><i class="far fa-exclamation-triangle"></i> No Vessel Outturn Report Found</h5>',
                    'token' => $this->token
                ];
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => 0,
                'outturnReport' => '',
                'msg' => $th->getMessage(),
                'trace' => $th->getTrace(),
                'token' => $this->token
            ];
        }
        return $this->response->setJSON($response);
    }












    public function renderOutturnReport($outturnReport)
    {






        $tr = '';
        foreach ($outturnReport as $report) {
            $shipMt = formatNumber($report->shipMt);
            $shipVol = formatNumber($report->shipVol);
            $shoreMt = formatNumber($report->shoreMt);
            $shoreVol = formatNumber($report->shoreVol);
            $billOfLadingMt = formatNumber($report->billOfLadingMt);
            $billOfLadingVol = formatNumber($report->billOfLadingVol);
            $dischargeQuantityMtDifference = formatNumber($report->dischargeQuantityMtDifference);
            $dischargeQuantityVolDifference = formatNumber($report->dischargeQuantityVolDifference);
            $dischargeQuantityMtDifferencePercent = formatNumber($report->dischargeQuantityMtDifferencePercent);
            $dischargeQuantityVolDifferencePercent = formatNumber($report->dischargeQuantityVolDifferencePercent);
            $billOfLadingMtDifference = formatNumber($report->billOfLadingMtDifference);
            $billOfLadingVolDifference = formatNumber($report->billOfLadingVolDifference);
            $billOfLadingMtDifferencePercent = formatNumber($report->billOfLadingMtDifferencePercent);
            $billOfLadingVolDifferencePercent = formatNumber($report->billOfLadingVolDifferencePercent);


            $tr .= <<<HTML
              <tr>
                <td>$report->terminal</td>
                <td>$shipMt</td>
                <td>$shipVol</td>
                <td>$shoreMt</td>
                <td>$shoreVol </td>
                <td>$billOfLadingMt</td>
                <td>$billOfLadingVol</td>
                <td>$dischargeQuantityMtDifference</td>
                <td>$dischargeQuantityVolDifference</td>
                <td>$dischargeQuantityMtDifferencePercent</td>
                <td>$dischargeQuantityVolDifferencePercent</td>
                <td>$billOfLadingMtDifference</td>
                <td>$billOfLadingVolDifference </td>
                <td>$billOfLadingMtDifferencePercent</td>
                <td>$billOfLadingVolDifferencePercent</td>
            </tr>        
        HTML;
        }



        $table = <<<HTML
            <table  style="width: 100%; border-collapse: collapse;">
        <!-- First Row: B/L Quantity and Ship Quantity -->
                <tr>
                    <th colspan="12" style="border: 0;"></th>
                    <th colspan="2" class="header-right">B/L QUANTITY (MT)</th>
                    <th colspan="4" class="header-right">37,587.443</th>
                </tr>
                <tr>
                    <th colspan="12" style="border: 0;"></th>
                    <th colspan="2" class="header-right">SHIP QUANTITY @20 MT</th>
                    <th colspan="4" class="header-right">37,558.650</th>
                </tr>

                <tr>
                    <th colspan="18">FINAL RECEIPT</th>
                </tr>

                <!-- New DISC. Header Row -->
                <tr>
                    <th rowspan="4">TERMINAL</th>
                    <th colspan="6"></th>
                    <th colspan="11">DIFFERENCE</th>
                </tr>
    
        <!-- Second Row: SHIP FIGURE and Other Sections -->
                <tr>
                    <th colspan="2">SHIP FIGURE</th>
                    <th colspan="2" rowspan="2">SHORE FIGURE</th>
                    <th colspan="2" rowspan="2">BILL OF LADING</th>
                    <th colspan="4" rowspan="2">DISCHARGED QUANTITY Vs FINAL</th>
                    <th colspan="4" rowspan="2">BILL OF LADING VS FINAL</th>
                </tr>
    
 
              <tr>
            <th colspan="2">Discharged Quantity</th>
           
        </tr>
    
        <!-- Third Row: Sub Headers -->
        <tr>
            <th>M/TONS</th>
            <th>VOL @ 20°C</th>
            <th>M/TONS</th>
            <th>VOL @ 20°C</th>
            <th>MT</th>
            <th>VOL</th>
       
            <th>M/TONS</th>
            <th>% DIFF</th>
            <th>VOL @ 20°C</th>
            <th>% DIFF</th>
            <th>MT</th>
            <th>% DIFF</th>
            <th>VOL</th>
            <th colspan="">% DIFF</th>
        </tr>
        <!-- Data Rows -->
             $tr
      
     
       
    </table> 
HTML;





        return $table;
    }



    public function getOutturnReportApi()
    {
        $esbLib = new EsbLibrary();
    }
}
