<?php

namespace App\Controllers;

use App\Libraries\CommonTasksLibrary;
use App\Models\PortModel;
use App\Models\ProfileModel;

class PressureLog extends BaseController
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
        helper(['form', 'array', 'regions', 'date', 'documents', 'image']);
    }

    public function getVariable($var)
    {
        return $this->appRequest->getVar($var, FILTER_SANITIZE_STRING);
    }

    // public function searchExistingShips()
    // {
    //     $ships = $this->portUnitModel->findMatch();
    //     return json_encode($ships);
    // }

    // public function selectedShip()
    // {
    //     if ($this->request->getMethod() == 'POST') {
    //         $id = $this->getVariable('id');
    //         $request = $this->portUnitModel->getSelectedShip($id);

    //         if ($request) {
    //           echo json_encode($request);
    //         }
    //     }
    // }

    public function index()
    {
       

        $uniqueId = $this->uniqueId;
        $role = $this->role;
        $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
        $data['role'] = $role;
        $data['page'] = [
            "title" => "Pressure Log",
            "heading" => "Pressure Log",
        ];

        $data['genderValues'] = ['Male', 'Female'];
        return view('Pages/Port/pressureLog', $data);
    }

    public function addPressureLog()
    {
        if ($this->request->getMethod() == 'POST') {

            $pressureLog = [
                'ship_id' => $this->getVariable('shipId'),
                'date' => $this->getVariable('date'),
                'time' => $this->getVariable('time'),
                'pressure' => $this->getVariable('pressure'),
                'rate' => $this->getVariable('rate'),
                'unique_id' => $this->uniqueId,
            ];

            // echo json_encode($pressureLog);
            // exit;
            $request = $this->portUnitModel->savePressureLog($pressureLog);
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
            $request = $this->portUnitModel->getLastPressureLog($shipId);
            if ($request) {
                echo json_encode($request);
            } else {
                echo json_encode('Something Went Wrong');
            }
        }
    }
    public function getAllPressureLogs()
    {
        if ($this->request->getMethod() == 'POST') {

            $shipId = $this->getVariable('shipId');
            $request = $this->portUnitModel->getAllPressureLogs($shipId, $this->uniqueId);
            if ($request) {
                echo json_encode($request);
            } else {
                echo json_encode('nothing');
            }
        }
    }

    //$data['documents'] = $this->portUnitModel->downloadDocument($id);
    public function downloadPressureLog($shipId)
    { {
            if (!$this->session->has('loggedUser')) {
                return redirect()->to('/login');
            }

            $date = date('d-M,Y h:i:s ');
            $dompdf = new \Dompdf\Dompdf();
            $options = new \Dompdf\Options();

            $title = 'Pressure Log';

            $data['log'] = $this->portUnitModel->getAllPressureLogs($shipId);
            $dompdf->loadHtml(view('PortUnitTemplates/pressureLogPdf', $data));
            $dompdf->setPaper('A4', 'portrait');
            $options->set('isRemoteEnabled', true);

            $dompdf->render();

            $dompdf->stream($title . ':' . $date . '.pdf', array('Attachment' => 0));
        }
    }
}
