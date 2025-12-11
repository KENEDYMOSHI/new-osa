<?php namespace App\Controllers;

use App\Libraries\CommonTasksLibrary;
use App\Models\PortModel;
use App\Models\ProfileModel;

class ProvisionalReport extends BaseController
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
    public $decimal;
    public $appRequest;

    public function __construct()
    {
        $this->appRequest = service('request');
        $this->portUnitModel = new PortModel();
        $this->profileModel = new ProfileModel();
        $this->session = session();
        $this->decimal = 3;
        $this->uniqueId = $this->session->get('loggedUser');
        $this->managerId = $this->session->get('manager');
        $this->role = $this->profileModel->getRole($this->uniqueId)->role;
        $this->city = $this->session->get('city');
        $this->CommonTasks = new CommonTasksLibrary();
        helper(['form', 'array', 'regions', 'date', 'documents', 'url','image']);

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
            "title" => "Provisional Outurn Report",
            "heading" => "Provisional Outurn Report",
        ];

        $data['ports'] = $this->portUnitModel->portDetails();

        $data['genderValues'] = ['Male', 'Female'];
        return view('Pages/Port/provisionalReport', $data);
    }

    public function addProvisionalReport()
    {

        if ($this->request->getMethod() == 'POST') {
            $token = csrf_hash();

            $provisionalReport = [
                'ship_id' => $this->getVariable('shipId'),

                'ship_metric_tons' => $this->getVariable('shipMetricTons'),
                'ship_volume' => $this->getVariable('shipVolume'),
                'shore_metric_tons' => $this->getVariable('shoreMetricTons'),
                'shore_volume' => $this->getVariable('shoreVolume'),
                'metric_tons_difference' => $this->getVariable('metricTonsDifference'),
                'metric_tons_percentage' => $this->getVariable('metricTonsPercentage'),
                'volume_difference' => $this->getVariable('volumeDifference'),
                'volume_percentage' => $this->getVariable('volumePercentage'),
                'bl_metric_tons' => $this->getVariable('blMetricTons'),
                'bl_metric_tons_difference' => $this->getVariable('blMetricTonsDifference'),
                'bl_metric_tons_percentage' => $this->getVariable('blMetricTonsPercentage'),

                'unique_id' => $this->uniqueId,
            ];

            // echo json_encode($provisionalReport);
            // exit;
            $request = $this->portUnitModel->saveProvisionalReport($provisionalReport);
            if ($request) {

                echo json_encode([
                    'message' => 'Added',
                    'token' => $token,
                    // 'success' => 1,
                ]);
            } else {
                echo json_encode([
                    'message' => 'Failed',
                    'success' => 0,
                ]);

            }

        }

    }

    public function getProvisionalReport()
    {
        if ($this->request->getMethod() == 'POST') {

            // $token = csrf_hash();

            $shipId = $this->getVariable('shipId');
            $request = $this->portUnitModel->getProvisionalReport($shipId);

            if ($request) {

                $tableRow = '';
                $shipMetricTonsTotal = 0;
                $shipVolumeTotal = 0;
                $shoreMetricTonsTotal = 0;
                $shoreVolumeTotal = 0;

                $metricTonsDifferenceTotal = 0;
                $volumeDifferenceTotal = 0;

                $blMetricTons = 0;
                $blMetricTonsDifference = 0;
                $blMetricTonsPercentage = 0;

                foreach ($request as $line) {

                    $shipMetricTonsTotal += $line->ship_metric_tons;
                    $shipVolumeTotal += $line->ship_volume;
                    $shoreMetricTonsTotal += $line->shore_metric_tons;
                    $shoreVolumeTotal += $line->shore_volume;

                    $metricTonsDifferenceTotal += $line->metric_tons_difference;
                    $volumeDifferenceTotal += $line->volume_difference;

                    $blMetricTons += $line->bl_metric_tons;
                    $blMetricTonsDifference += $line->bl_metric_tons_difference;
                    $blMetricTonsPercentage += $line->bl_metric_tons_percentage;
                    $tableRow .= '
                <tr>
                    <td>' . $line->terminal . '</td>
                    <td>' . $line->ship_metric_tons . '</td>
                    <td>' . $line->ship_volume . '</td>
                    <td>' . $line->shore_metric_tons . '</td>
                    <td>' . $line->shore_volume . '</td>
                    <td>' . $line->metric_tons_difference . '</td>
                    <td>' . $line->metric_tons_percentage . ' %' . '</td>
                    <td>' . $line->volume_difference . '</td>
                    <td>' . $line->volume_percentage . ' %' . '</td>
                    <td>' . $line->bl_metric_tons . '</td>
                    <td>' . $line->bl_metric_tons_difference . '</td>
                    <td>' . $line->bl_metric_tons_percentage . ' %' . '</td>
                </tr>

                ';

                }

                $tableRow .= '
                 <tr>
                    <th>TOTAL</th>
                    <th>' . number_format($shipMetricTonsTotal) . '</th>
                    <th>' . number_format($shipVolumeTotal) . '</th>
                    <th>' . number_format($shoreMetricTonsTotal) . '</th>
                    <th>' . number_format($shoreVolumeTotal) . '</th>
                    <th>' . number_format($shoreMetricTonsTotal - $shipMetricTonsTotal) . '</th>
                    <th>' . round(($metricTonsDifferenceTotal / $shipMetricTonsTotal) * 100, 2) . ' %' . '</th>
                    <th>' . number_format($volumeDifferenceTotal) . '</th>
                    <th>' . round(($volumeDifferenceTotal / $shipVolumeTotal) * 100, 2) . ' %' . '</th>
                    <th>' . number_format($blMetricTons) . '</th>
                    <th>' . number_format($shoreMetricTonsTotal - $blMetricTons) . '</th>
                    <th>' . round(($blMetricTonsDifference / $blMetricTons) * 100, 2) . ' %' . '</th>
                </tr>

                ';

                return $tableRow;

            } else {
                return 'No Data Found';
            }
        }
    }

//$data['documents'] = $this->portUnitModel->downloadDocument($id);
    public function downloadProvisionalReport($shipId)
    {
      

        $date = date('d-M,Y h:i:s ');
        $dompdf = new \Dompdf\Dompdf();
        $options = new \Dompdf\Options();

        $title = 'Provisional Outurn Report';

// $data['details'] = $this->portUnitModel->downloadDocument($shipId);
        $data['line'] = $this->portUnitModel->getProvisionalReport($shipId);
        $dompdf->loadHtml(view('PortUnitTemplates/provisionalReportPdf', $data));
        $dompdf->setPaper('A4', 'landscape');
        $options->set('isRemoteEnabled', true);

// Render the HTML as PDF
        $dompdf->render();

        $dompdf->stream($title . ':' . $shipId . '.pdf', array('Attachment' => 0));

    }
}