<?php namespace App\Controllers;

use App\Libraries\CommonTasksLibrary;
use App\Models\PortModel;
use App\Models\ProfileModel;

class PortUnit extends BaseController
{
    public $uniqueId;
    public $managerId;
    public $role;
    public $city;
    public $portUnitModel;
    public $session;
    public $profileModel;
    public $CommonTasks;

    public $sessionExpiration;

    public $variable;
    public $appRequest;

    public function __construct()
    {
        $this->appRequest = service('request');
        $this->portUnitModel = new PortModel();
        $this->profileModel = new ProfileModel();
        $this->session = session();

        $this->uniqueId = $this->session->get('loggedUser');
        $this->managerId = $this->session->get('manager');
        $this->role = $this->profileModel->getRole($this->uniqueId)->role;
        $this->city = $this->session->get('city');
        $this->CommonTasks = new CommonTasksLibrary();
        helper(['form', 'array', 'regions', 'date', 'documents','image']);

    }

    public function getVariable($var)
    {
        return $this->appRequest->getVar($var, FILTER_SANITIZE_STRING);
    }

    public function searchExistingShips()
    {
        $ships = $this->portUnitModel->findMatch();
        return json_encode($ships);
    }

    public function selectedShip()
    {
        if ($this->request->getMethod() == 'POST') {
            $id = $this->getVariable('id');
            $request = $this->portUnitModel->getSelectedShip($id);

            if ($request) {
                echo json_encode($request);
            }
        }
    }

    public function timeLog()
    {
       

        $uniqueId = $this->uniqueId;
        $role = $this->role;
        $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
        $data['role'] = $role;
        $data['page'] = [
            "title" => "Time Log",
            "heading" => "Time Log",
        ];

        $data['genderValues'] = ['Male', 'Female'];
        return view('Pages/Port/timeLog', $data);
    }

    public function addShipParticulars()
    {
        if ($this->request->getMethod() == 'POST') {

            /*
            draft: draft.value,
            aft: aft.value,
            trim: trim.value,
            list: list.value,
            density15Centigrade: density15Centigrade.value,
            density20Centigrade: density20Centigrade.value,
             */

            $shipDetails = [
                //'hash'=>$this->getVariable('customerHash'),
                'ship_name' => $this->getVariable('shipName'),
                'captain' => $this->getVariable('captain'),
                'arrival_date' => $this->getVariable('ArrivalDate'),
                'cargo' => $this->getVariable('cargo'),
                'quantity' => $this->getVariable('quantity'),
                'imo' => $this->getVariable('imo'),
                'port' => $this->getVariable('port'),
                'terminal' => $this->getVariable('terminal'),
                'draft' => $this->getVariable('draft'),
                'aftr' => $this->getVariable('aft'),
                'trim' => $this->getVariable('trim'),
                'list' => $this->getVariable('list'),
                'density_15C' => $this->getVariable('density15Centigrade'),
                'density_20C' => $this->getVariable('density20Centigrade'),
                'unique_id' => $this->uniqueId,
            ];

            // echo json_encode($shipDetails);
            // exit;
            $request = $this->portUnitModel->saveShipParticulars($shipDetails);
            if ($request) {
                echo json_encode('Added');
            } else {
                echo json_encode('Something Went Wrong');
            }

        }

    }

    //=================request for documents====================
    public function documents()
    {
       
        $data['genderValues'] = ['Male', 'Female'];
        $uniqueId = $this->uniqueId;
        $role = $this->role;
        $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
        $data['role'] = $role;
        $data['page'] = [
            "title" => "Request Of Documents",
            "heading" => "Request Of Documents",
        ];
        //$data['documents'] = $this->portUnitModel->downloadDocument('5');

        $data['shipList'] = $this->portUnitModel->getShipList($uniqueId);

        return view('Pages/Port/documents', $data);
    }

    public function saveShipDocumentsInfo()
    {
        if ($this->request->getMethod() == 'POST') {

            $shipDocuments = [
                // 'hash'=>$this->getVariable('customerHash'),
                'ship_id ' => $this->getVariable('shipId'),
                'StowagePlan' => $this->getVariable('StowagePlan'),
                'ShipParticulars' => $this->getVariable('ShipParticulars'),
                'TankCalibrationCertificate' => $this->getVariable('TankCalibrationCertificate'),
                'BillOfLading' => $this->getVariable('BillOfLading'),
                'CargoManifest' => $this->getVariable('CargoManifest'),
                'UllageReportOfLoadingPorts' => $this->getVariable('UllageReportOfLoadingPorts'),
                'UllageTemperatureInterfaceCalibrationCertificate' => $this->getVariable('UllageTemperatureInterfaceCalibrationCertificate'),
                'CertificateOfQuantity' => $this->getVariable('CertificateOfQuantity'),
                'CertificateOfQuality' => $this->getVariable('CertificateOfQuality'),
                'LastArrivalPortBunker' => $this->getVariable('LastArrivalPortBunker'),
                'NoticeOfReadinessSignedByCargoReceiver' => $this->getVariable('NoticeOfReadinessSignedByCargoReceiver'),
                'VesselExperienceFactor' => $this->getVariable('VesselExperienceFactor'),
                'CargoDischargingOrder' => $this->getVariable('CargoDischargingOrder'),
                'CertificateOfOrigin' => $this->getVariable('CertificateOfOrigin'),

                'unique_id' => $this->uniqueId,
            ];

            // echo json_encode($shipDocuments);
            // exit;
            $request = $this->portUnitModel->saveShipDocuments($shipDocuments);
            if ($request) {
                echo json_encode('Added');
            } else {
                echo json_encode('Something Went Wrong');
            }

        }
    }

    public function selectShipDocuments()
    {
        $id = $this->getVariable('id');

        $documents = $this->portUnitModel->getShipDocuments($id);
        if ($documents) {
            echo json_encode($documents);
        } else {
            echo json_encode('empty');
        }

    }

    public function renderImg($arg)
    {
        $cancel = cancelIcon();
        $check = checkIcon();
        if ($arg == '1') {
            return "<img src='$cancel width='25px'>";
        } else if ($arg == '0') {
            return "<img src='$check' width='25px'>";
        }
    }

//$data['documents'] = $this->portUnitModel->downloadDocument($id);
    public function downloadPortDocsPDF($id)
    {

        $dompdf = new \Dompdf\Dompdf();
        $shipInfo = $this->portUnitModel->getSelectedShip($id);
        $shipDocuments = $this->portUnitModel->downloadDocument($id, $this->uniqueId);
        $page =
            documentHeader($shipDocuments) . '
    <div class="wrapper">
    <table class="main-table" border="1" >
    <thead>
        <tr>
            <th>DOCUMENT </th>
            <th>REMARK</th>
            <th>APPENDIX</th>

        </tr>
    </thead>
    <tbody>
                ';
        // $shipDocuments = $this->portUnitModel->downloadDocument($id, $this->uniqueId);
        foreach ($shipDocuments as $document) {
            $page .=
            '
                <tr>
                    <td>STOWAGE PLAN</td>
                    <td>' . $this->renderImg($document->StowagePlan) . '</td>
                    <td></td>
                </tr>

                <tr>
                   <td>SHIP PARTICULARS</td>
                   <td>' . $this->renderImg($document->ShipParticulars) . '</td>
                   <td></td>
                </tr>
                <tr>
                <td>TANKS CALIBRATION CERTIFICATE</td>
                   <td>' . $this->renderImg($document->TankCalibrationCertificate) . '</td>
                   <td></td>
                </tr>
                <tr>
                <td>BILL OF LADING</td>
                   <td>' . $this->renderImg($document->BillOfLading) . '</td>
                   <td></td>
                </tr>
                <tr>
                <td>CARGO MANIFEST</td>
                   <td>' . $this->renderImg($document->CargoManifest) . '</td>
                   <td></td>
                </tr>
                <tr>
                <td>ULLAGE REPORT OF THE LAST LOADING POSTS</td>
                   <td>' . $this->renderImg($document->UllageReportOfLoadingPorts) . '</td>
                   <td></td>
                </tr>
                <tr>
                <td>ULLAGE TEMPERATURE INTERFACE CALIBRATION CERTIFICATE</td>
                   <td>' . $this->renderImg($document->UllageTemperatureInterfaceCalibrationCertificate) . '</td>
                   <td></td>
                </tr>
                <tr>
                <td>CERTIFICATES OF QUANTITY</td>
                   <td>' . $this->renderImg($document->CertificateOfQuantity) . '</td>
                   <td></td>
                </tr>
                <tr>
                <td>CERTIFICATES OF QUALITY</td>
                   <td>' . $this->renderImg($document->CertificateOfQuality) . '</td>
                   <td></td>
                </tr>
                <tr>
                <td>NOTICES OF READINESS SIGNED BY CARGO RECEIVER</td>
                   <td>' . $this->renderImg($document->NoticeOfReadinessSignedByCargoReceiver) . '</td>
                   <td></td>
                </tr>
                <tr>
                <td>VESSEL EXPERIENCE FACTOR(V.E.F)</td>
                   <td>' . $this->renderImg($document->VesselExperienceFactor) . '</td>
                   <td></td>
                </tr>
                <tr>
                <td>LAST/ ARRIVAL PORT BUNKER</td>
                   <td>' . $this->renderImg($document->LastArrivalPortBunker) . '</td>
                   <td></td>
                </tr>
                <tr>
                <td>DISCHARGING ORDER/INSTRUCTION</td>
                   <td>' . $this->renderImg($document->CargoDischargingOrder) . '</td>
                   <td></td>
                </tr>
                <tr>
                <td>CERTIFICATE OF ORIGIN</td>
                   <td>' . $this->renderImg($document->CertificateOfOrigin) . '</td>
                   <td></td>
                </tr>

                ';
        }

        $page .= '
                </tbody>

            </table>
        </div>' . documentFooter($document->first_name, $document->last_name, $shipInfo->captain) . '

';

        $dompdf->loadHtml($page);
// (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->set_option('isRemoteEnabled', true);

// Render the HTML as PDF
        $dompdf->render();

// Output the generated PDF to Browser
        $dompdf->stream('Onboard Documents' . ".pdf", array("Attachment" => 0));
    }
}