<?php namespace App\Controllers;

use App\Libraries\CommonTasksLibrary;
use App\Models\PortModel;
use App\Models\ProfileModel;

class DischargeOrder extends BaseController
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
        $this-> appRequest = service('request');
        $this->portUnitModel = new PortModel();
        $this->profileModel = new ProfileModel();
        $this->session = session();

        $this->uniqueId = $this->session->get('loggedUser');
        $this->managerId = $this->session->get('manager');
        $this->role = $this->profileModel->getRole($this->uniqueId)->role;
        $this->city = $this->session->get('city');
        $this->CommonTasks = new CommonTasksLibrary();
        helper(['form', 'array', 'regions', 'date','image']);

    }

    public function getVariable($var)
    {
        return $this->appRequest->getVar($var, FILTER_SANITIZE_STRING);
    }

    public function index()
    {
        if (!$this->session->has('loggedUser')) {
            return redirect()->to('login');
        }

        $uniqueId = $this->uniqueId;
        $role = $this->role;
        $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
        $data['role'] = $role;
        $data['page'] = [
            "title" => "Discharge Order Analysis",
            "heading" => "Discharge Order Analysis",
        ];

        return view('Pages/Port/dischargeOrderAnalysis', $data);
    }

    public function addDischargeOrder()
    {
        if ($this->request->getMethod() == 'POST') {

            $DischargeOrder = [
                'ship_id' => $this->getVariable('shipId'),
                'receiving_terminal' => $this->getVariable('receivingTerminal'),
                'receiver' => $this->getVariable('receiver'),
                'quantity' => $this->getVariable('quantity'),
                'destination' => $this->getVariable('destination'),
                'unique_id' => $this->uniqueId,
            ];

            // echo json_encode($DischargeOrder);
            // exit;
            $request = $this->portUnitModel->saveDischargeOrder($DischargeOrder);
            if ($request) {
                echo json_encode('Added');
            } else {
                echo json_encode('Something Went Wrong');
            }

        }

    }

    public function getDischargeOrder()
    {

        if ($this->request->getMethod() == 'POST') {

            $shipId = $this->getVariable('shipId');

            $quantity = 0;

            $arrivalQuantity_billOfLading = $this->portUnitModel->getArrivalQuantity_billOfLading($shipId);
            $billOfLading = $arrivalQuantity_billOfLading[0]->billOfLading1;
            $WCFT_20 = $arrivalQuantity_billOfLading[0]->density_20C - 0.0011;

            foreach ($arrivalQuantity_billOfLading as $qty) {
                $quantity += $qty->GSV20Centigrade;
            }
            $arrivalQuantity = $quantity * $WCFT_20;

            $request = $this->portUnitModel->getAllDischargeOrders($shipId);
            if ($request) {
                echo json_encode([
                    'data' => $request,
                    'billOfLading' => (float) $billOfLading,
                    'arrivalQuantity' => round($arrivalQuantity, 3),
                ]);
            } else {
                echo json_encode('nothing');
            }

        }
    }

//$data['documents'] = $this->portUnitModel->downloadDocument($id);
    public function downloadDischargeOrder($shipId)
    {

        {
            if (!$this->session->has('loggedUser')) {
                return redirect()->to('/login');
            }

            $date = date('d-M,Y h:i:s ');
            $dompdf = new \Dompdf\Dompdf();
            $options = new \Dompdf\Options();

            $title = 'Discharge Order Analysis';

            $data['dischargeOrders'] = $this->portUnitModel->getAllDischargeOrders($shipId);

            $quantity = 0;

            $arrivalQuantity_billOfLading = $this->portUnitModel->getArrivalQuantity_billOfLading($shipId);
            $billOfLading = $arrivalQuantity_billOfLading[0]->billOfLading1;
            $WCFT_20 = $arrivalQuantity_billOfLading[0]->density_20C - 0.0011;

            foreach ($arrivalQuantity_billOfLading as $qty) {
                $quantity += $qty->GSV20Centigrade;
            }
            $arrivalQuantity = $quantity * $WCFT_20;

            $data['arrivalQuantity'] = round($arrivalQuantity, 3);
            $data['billOfLading'] = round($billOfLading, 3);

            $dompdf->loadHtml(view('PortUnitTemplates/DischargeOrderPdf', $data));
            $dompdf->setPaper('A4', 'portrait');
            $options->set('isRemoteEnabled', true);

            $dompdf->render();

            $dompdf->stream($title . ':' . $date . '.pdf', array('Attachment' => 0));

        }
    }
}