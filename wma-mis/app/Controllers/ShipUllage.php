<?php namespace App\Controllers;

use App\Libraries\CommonTasksLibrary;
use App\Models\PortModel;
use App\Models\ProfileModel;

class ShipUllage extends BaseController
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
        helper(['form', 'array', 'regions', 'date', 'documents','image']);

    }

    public function getVariable($var)
    {
        return $this->appRequest->getVar($var, FILTER_SANITIZE_STRING);
    }

    public function index()
    {
        

        $uniqueId = $this->uniqueId;
        $role = $this->role;
        $data['profile'] = $this->profileModel->getLoggedUserData($uniqueId);
        $data['role'] = $role;
        $data['page'] = [
            "title" => "Ship Ullage Before Discharging",
            "heading" => "Ship Ullage Before Discharging",
        ];

        $data['ports'] = $this->portUnitModel->portDetails();

        $data['genderValues'] = ['Male', 'Female'];
        return view('Pages/Port/ullageBeforeDischarging', $data);
    }

    public function addShipOilTank()
    {

        if ($this->request->getMethod() == 'POST') {

            $oilTank = [
                'ship_id' => $this->getVariable('shipId'),
                'tankNumber' => $this->getVariable('tankNumber'),
                'correctedUllage' => round($this->getVariable('correctedUllage'), $this->decimal),
                'observedTemperature' => round($this->getVariable('observedTemperature'), $this->decimal),
                'totalObservedVolume' => round($this->getVariable('totalObservedVolume'), $this->decimal),
                'freeWater' => round($this->getVariable('freeWater'), $this->decimal),
                'freeWaterVolume' => round($this->getVariable('freeWaterVolume'), $this->decimal),
                'grossObservedVolume' => round($this->getVariable('grossObservedVolume'), $this->decimal),
                'VCF54B15Centigrade' => round($this->getVariable('VCF54B15Centigrade'), $this->decimal),
                'GSV15Centigrade' => round($this->getVariable('GSV15Centigrade'), $this->decimal),
                'VCF60B20Centigrade' => round($this->getVariable('VCF60B20Centigrade'), $this->decimal),
                'GSV20Centigrade' => round($this->getVariable('GSV20Centigrade'), $this->decimal),
                'unique_id' => $this->uniqueId,
            ];

            // echo json_encode($oilTank);
            // exit;
            $request = $this->portUnitModel->saveShipUllageB4Discharge($oilTank);
            if ($request) {
                echo json_encode('Added');
            } else {
                echo json_encode('Something Went Wrong');
            }

        }

    }

    public function getAvailableShipUllageB4Discharge()
    {
        if ($this->request->getMethod() == 'POST') {

            $shipId = $this->getVariable('shipId');
            $request = $this->portUnitModel->getAllShipUllageB4Discharge($shipId, $this->uniqueId);
            if ($request) {
                echo json_encode($request);
            } else {
                echo json_encode('');
            }

        }
    }

    public function computeColumn($obsvdTemp, $obsvdVol, $freeWtr, $freeWtrVol, $grossObsvdVol, $VCF54B, $GSV15C, $VCF60B, $GSV20C)
    {

        return
        '
        <td></td>
        <td><b>TOTAL G.S.V</b></td>
        <td><b>' . round($obsvdTemp, $this->decimal) . '</b></td>
        <td><b>' . round($obsvdVol, $this->decimal) . '</b></td>
        <td><b>' . round($freeWtr, $this->decimal) . '</b></td>
        <td><b>' . round($freeWtrVol, $this->decimal) . '</b></td>
        <td><b>' . round($grossObsvdVol, $this->decimal) . '</b></td>
        <td><b>' . round($VCF54B, $this->decimal) . '</b></td>
        <td><b>' . round($GSV15C, $this->decimal) . '</b></td>
        <td><b>' . round($VCF60B, $this->decimal) . '</b></td>
        <td><b>' . round($GSV20C, $this->decimal) . '</b></td>

        ';

    }

    public function standardTemperature($GCF15C, $GCF20C, $density15C, $density20C, $WCFT_56_15C, $WCFT_56_20C, $totalQtyB4_Metric_Tons_15, $totalQtyB4_Metric_Tons_20)
    {
        return '
  <table border="1" class="ullageTable">

  <tr>
      <th colspan="2" class="text-center">PARTICULAR</th>
      <th colspan="2" class="text-center">STANDARD TEMPERATURE</th>

  </tr>
  <tr>
      <th colspan="2"></th>
      <th class="text-center">15 &deg;C</th>
      <th class="text-center">20 &deg;C</th>

  </tr>
  <tr>

      <td colspan="2"><b>TOTAL G.S.V (m<sup>3</sup>)</b></td>
      <td>' . round($GCF15C, $this->decimal) . '</td>
      <td>' . round($GCF20C, $this->decimal) . '</td>


  </tr>
  <tr>

      <td colspan="2"><b>W.C.F.T-56/C</b></td>
      <td>' . $WCFT_56_15C . '</td>
      <td>' . $WCFT_56_20C . '</td>

  </tr>
  <tr>

      <td colspan="2"><b>Reference Density</b></td>
      <td>' . $density15C . '</td>
      <td>' . $density20C . '</td>

  </tr>
  <tr>

      <td colspan="2"><b>TOTAL QTY DISCHARGING (MT)</b></td>
      <td>' . $totalQtyB4_Metric_Tons_15 . '</td>
      <td>' . $totalQtyB4_Metric_Tons_20 . '</td>


  </tr>
  <tr>

      <td colspan="2"><b>VOLUME (L)</b></td>
      <td>' . number_format($GCF15C * 1000) . '</td>
      <td>' . number_format($GCF20C * 1000) . '</td>

  </tr>
</table>
  ';
    }

//$data['documents'] = $this->portUnitModel->downloadDocument($id);
    public function downloadUllageB4Discharging($id)
    {

        $obsvdTemp = 0;
        $obsvdVol = 0;
        $freeWtr = 0;
        $freeWtrVol = 0;
        $grossObsvdVol = 0;
        $VCF54B = 0;
        $GSV15C = 0;
        $VCF60B = 0;
        $GSV20C = 0;

        $dompdf = new \Dompdf\Dompdf();
        $shipInfo = $this->portUnitModel->getSelectedShip($id);
        $title = 'ULLAGE REPORT BEFORE DISCHARGING';

        $page =
            ullageB4Header($shipInfo->ship_name, $shipInfo->arrival_date, $shipInfo->terminal, $shipInfo->port, $shipInfo->fax, $shipInfo->email, $shipInfo->postal_address, $shipInfo->phone_number, $title, $shipInfo->draft, $shipInfo->aftr, $shipInfo->trim, $shipInfo->list, $shipInfo->cargo) . '
    <div class="wrapper">
    <table class="main-table" border="1" >
    <thead>
        <tr>
        <th>TANK NO.</th>
        <th scope="col">CORRECTED ULLAGE(m)</th>
        <th scope="col">OBSVD TEMP &deg;C</th>
        <th scope="col">TOTAL OBSVD VOL(m<sup>3</sup>)</th>
        <th>FREE WATER(m)</th>
        <th>FREE WATER VOL(m<sup>3</sup>)</th>
        <th scope="col">GROSS OBSVD VOL(m<sup>3</sup>)</th>
        <th>V.C.F TABLE 54B @ 15&deg;C</th>
        <th>G.S.V @15&deg;C (m<sup>3</sup>)</th>
        <th>V.C.F TABLE 60B @ 20&deg;C</th>
        <th>G.S.V @20&deg;C (m<sup>3</sup>)</th>

        </tr>
    </thead>
    <tbody>
                ';
        $ullageB4Discharging = $this->portUnitModel->downloadUllageB4Discharge($id, $this->uniqueId);
        foreach ($ullageB4Discharging as $ullageB4) {
            $page .=
            '
                <tr>

                <td>' . $ullageB4->tankNumber . '</td>
                <td>' . $ullageB4->correctedUllage . '</td>
                <td>' . $ullageB4->observedTemperature . '</td>
                <td>' . $ullageB4->totalObservedVolume . '</td>
                <td>' . $ullageB4->freeWater . '</td>
                <td>' . $ullageB4->freeWaterVolume . '</td>
                <td>' . $ullageB4->grossObservedVolume . '</td>
                <td>' . $ullageB4->VCF54B15Centigrade . '</td>
                <td>' . $ullageB4->GSV15Centigrade . '</td>
                <td>' . $ullageB4->VCF60B20Centigrade . '</td>
                <td>' . $ullageB4->GSV20Centigrade . '</td>
                </tr>

                ';
            $obsvdTemp += ($ullageB4->observedTemperature / count($ullageB4Discharging));
            $obsvdVol += $ullageB4->totalObservedVolume;
            $freeWtr += $ullageB4->freeWater;
            $freeWtrVol += $ullageB4->freeWaterVolume;
            $grossObsvdVol += $ullageB4->grossObservedVolume;
            $VCF54B += $ullageB4->VCF54B15Centigrade;
            $GSV15C += $ullageB4->GSV15Centigrade;
            $VCF60B += $ullageB4->VCF60B20Centigrade;
            $GSV20C += $ullageB4->GSV20Centigrade;

        }

        $density15C = $ullageB4Discharging[0]->density_15C;
        $density20C = $ullageB4Discharging[0]->density_20C;

        $WCFT_56_15C = $density15C - 0.0011;
        $WCFT_56_20C = $density20C - 0.0011;

        $totalQtyB4_Metric_Tons_15 = round($GSV15C * $WCFT_56_15C, $this->decimal);
        $totalQtyB4_Metric_Tons_20 = round($GSV20C * $WCFT_56_20C, $this->decimal);

        $page .= $this->computeColumn($obsvdTemp, $obsvdVol, $freeWtr, $freeWtrVol, $grossObsvdVol, $VCF54B, $GSV15C, $VCF60B, $GSV20C) . '

                </tbody>

            </table>
        </div>

        ' . $this->standardTemperature(
            $GSV15C,
            $GSV20C,
            $density15C, $density20C,
            $WCFT_56_15C,
            $WCFT_56_20C,
            $totalQtyB4_Metric_Tons_15,
            $totalQtyB4_Metric_Tons_20

        ) . '
        .' . documentFooter($ullageB4->first_name, $ullageB4->last_name, $shipInfo->captain) . '

';

        $dompdf->loadHtml($page);
// (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->set_option('isRemoteEnabled', true);

// Render the HTML as PDF
        $dompdf->render();

// Output the generated PDF to Browser
        $dompdf->stream($shipInfo->ship_name . ' Ship Ullage Before Discharging' . ".pdf", array("Attachment" => 1));
    }
}