<?php namespace App\Controllers;

use App\Libraries\CommonTasksLibrary;
use App\Models\PortModel;
use App\Models\ProfileModel;

class TimeLog extends BaseController
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

    public function addTimeLog()
    {
        if ($this->request->getMethod() == 'POST') {

            $timeLog = [
                'ship_id' => $this->getVariable('shipId'),
                'date' => $this->getVariable('date'),
                'time' => $this->getVariable('time'),
                'event' => $this->getVariable('event'),
                'unique_id' => $this->uniqueId,
            ];

            // echo json_encode($timeLog);
            // exit;
            $request = $this->portUnitModel->saveTimeLog($timeLog);
            if ($request) {
                echo json_encode('Added');
            } else {
                echo json_encode('Something Went Wrong');
            }

        }

    }

    public function getLastLog()
    {
        if ($this->request->getMethod() == 'POST') {

            $shipId = $this->getVariable('shipId');
            $request = $this->portUnitModel->getLastTimeLog($shipId, $this->uniqueId);
            if ($request) {
                echo json_encode($request);
            } else {
                echo json_encode('Something Went Wrong');
            }

        }
    }
    public function getAllTimeLogs()
    {
        if ($this->request->getMethod() == 'POST') {

            $shipId = $this->getVariable('shipId');
            $request = $this->portUnitModel->getAllTimeLogs($shipId, $this->uniqueId);
            if ($request) {
                echo json_encode($request);
            } else {
                echo json_encode('nothing');
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

//$data['documents'] = $this->portUnitModel->downloadDocument($id);
    public function downloadTimeLog($id)
    {

        $dompdf = new \Dompdf\Dompdf();
        $shipInfo = $this->portUnitModel->getSelectedShip($id);
        $timeLogs = $this->portUnitModel->downloadTimeLog($id, $this->uniqueId);

        $page =
            timeLogHeader($timeLogs) . '
    <div class="wrapper">
    <table class="main-table" border="1" >
    <thead>
        <tr>
            <th>DATE</th>
            <th>TIME</th>
            <th>EVENT/OPERATION</th>

        </tr>
    </thead>
    <tbody>
                ';

        foreach ($timeLogs as $timeLog) {
            $page .=
            '
                <tr>
                    <td>' . dateFormatter($timeLog->date) . '</td>
                    <td>' . $timeLog->time . '</td>
                    <td>' . $timeLog->event . '</td>
                </tr>

                ';
        }

        $page .= '
                </tbody>
                <tfoot>
                    <tr>
                    <th>DATE</th>
                    <th>TIME</th>
                    <th>EVENT/OPERATION</th>
                    </tr>
                </tfoot>
            </table>
        </div>' . documentFooter($timeLog->first_name, $timeLog->last_name, $shipInfo->captain) . '

';

        $dompdf->loadHtml($page);
// (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->set_option('isRemoteEnabled', true);

// Render the HTML as PDF
        $dompdf->render();

// Output the generated PDF to Browser
        $dompdf->stream($shipInfo->ship_name . ' TimeLog' . ".pdf", array("Attachment" => 1));
    }
}