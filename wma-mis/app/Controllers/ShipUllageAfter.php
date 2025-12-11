<?php namespace App\Controllers;

use App\Libraries\CommonTasksLibrary;
use App\Models\PortModel;
use App\Models\ProfileModel;

class ShipUllageAfter extends BaseController
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
            "title" => "Ship Ullage After Discharging",
            "heading" => "Ship Ullage After Discharging",
        ];

        $data['ports'] = $this->portUnitModel->portDetails();

        $data['genderValues'] = ['Male', 'Female'];
        return view('Pages/Port/ullageAfterDischarging', $data);
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
            $request = $this->portUnitModel->saveShipUllageAfterDischarge($oilTank);
            if ($request) {
                echo json_encode('Added');
            } else {
                echo json_encode('Something Went Wrong');
            }

        }

    }

    //calculating metic tons in ullage before
    public function calculateUllageB4($ullageB4)
    {
        $gsv15c = 0;
        $gsv20c = 0;

        foreach ($ullageB4 as $ullage) {
            $gsv15c += $ullage->gsv15;
            $gsv20c += $ullage->gsv20;
        }
        $WCFT_15 = $ullage->DN15 - 0.0011;
        $WCFT_20 = $ullage->DN20 - 0.0011;

        $tonesAt15 = round(($gsv15c * $WCFT_15), $this->decimal);
        $tonesAt20 = round(($gsv20c * $WCFT_20), $this->decimal);

        return [
            'ullageB4Tons_15' => $tonesAt15,
            'ullageB4Tons_20' => $tonesAt20,
        ];

    }

    public function getAvailableShipUllageAfterDischarge()
    {
        if ($this->request->getMethod() == 'POST') {

            $shipId = $this->getVariable('shipId');
            $request = $this->portUnitModel->getAllShipUllageAfterDischarge($shipId, $this->uniqueId);
            $ullgb4 = $this->portUnitModel->getAllUllageB4DischargeValue($shipId, $this->uniqueId);

            if ($request) {
                array_push($request, $this->calculateUllageB4($ullgb4));
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

    public function standardTemperature($totalAfterDischarging15, $totalAfterDischarging20, $densityAt15, $densityAt20, $WCFT_15, $WCFT_20, $totalB4Discharging15, $totalB4Discharging20, $totalQuantityDischarged15, $totalQuantityDischarged20, $volume15, $volume20, $GSV15C, $GSV20C)
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
      <td>' . round($GSV15C, 3) . '</td>
      <td>' . round($GSV20C, 3) . '</td>



  </tr>
  <tr>

      <td colspan="2"><b>W.C.F.T-56/C</b></td>
      <td>' . $WCFT_15 . '</td>
      <td>' . $WCFT_20 . '</td>

  </tr>
  <tr>

      <td colspan="2"><b>Reference Density</b></td>
      <td>' . $densityAt15 . '</td>
      <td>' . $densityAt20 . '</td>

  </tr>
  <tr>

      <td colspan="2"><b>TOTAL QTY BEFORE DISCHARGING (MT)</b></td>
      <td>' . round($totalB4Discharging15, 3) . '</td>
      <td>' . round($totalB4Discharging20, 3) . '</td>


  </tr>
  <tr>

      <td colspan="2"><b>TOTAL QTY AFTER DISCHARGING (MT)</b></td>
      <td>' . round($totalAfterDischarging15, 3) . '</td>
      <td>' . round($totalAfterDischarging20, 3) . '</td>

  </tr>
  <tr>

      <td colspan="2"><b>TOTAL QTY DISCHARGED (MT)</b></td>
      <td>' . round($totalQuantityDischarged15, 3) . '</td>
      <td>' . round($totalQuantityDischarged20, 3) . '</td>

  </tr>
  <tr>

      <td colspan="2"><b>VOLUME (L)</b></td>
      <td>' . $volume15 . '</td>
      <td>' . round($volume20, 3) . '</td>


  </tr>
</table>
  ';
    }

//$data['documents'] = $this->portUnitModel->downloadDocument($id);
    public function downloadUllageAfterDischarging($id)
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

        $totalVolumeUllage_15 = 0;
        $totalVolumeUllage_20 = 0;

        // $WCFT_15 = 0;
        // $WCFT_20 = 0;

        $ullageB4TotalVolume = $this->portUnitModel->getAllUllageB4DischargeValue($id, $this->uniqueId);
        $densityAt15 = $ullageB4TotalVolume[0]->DN15;
        $densityAt20 = $ullageB4TotalVolume[0]->DN20;

        $WCFT_15 = $ullageB4TotalVolume[0]->DN15 - 0.0011;
        $WCFT_20 = $ullageB4TotalVolume[0]->DN20 - 0.0011;
        foreach ($ullageB4TotalVolume as $ullageB4) {

            $totalVolumeUllage_15 += $ullageB4->gsv15;
            $totalVolumeUllage_20 += $ullageB4->gsv20;

        }
        //$totalVolumeUllage_15*1000;

        // ($GSV15C,$GSV20C,$densityAt15,$densityAt20,$WCFT_15,$WCFT_20,$totalB4Discharging15,$totalB4Discharging20,$totalQuantityDischarged15,$totalQuantityDischarged20)

        $dompdf = new \Dompdf\Dompdf();
        $shipInfo = $this->portUnitModel->getSelectedShip($id);
        $title = 'SUBSEQUENT  ULLAGE REPORT AFTER DISCHARGING';

        $page = ullageB4Header($shipInfo->ship_name, $shipInfo->arrival_date, $shipInfo->terminal, $shipInfo->port, $shipInfo->fax, $shipInfo->email, $shipInfo->postal_address, $shipInfo->phone_number, $title, $shipInfo->draft, $shipInfo->aftr, $shipInfo->trim, $shipInfo->list, $shipInfo->cargo) . '
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
        $ullageAfterDischarging = $this->portUnitModel->downloadUllageAfterDischarge($id, $this->uniqueId);
        foreach ($ullageAfterDischarging as $ullageAfter) {
            $page .=
            '
                <tr>

                <td>' . $ullageAfter->tankNumber . '</td>
                <td>' . $ullageAfter->correctedUllage . '</td>
                <td>' . $ullageAfter->observedTemperature . '</td>
                <td>' . $ullageAfter->totalObservedVolume . '</td>
                <td>' . $ullageAfter->freeWater . '</td>
                <td>' . $ullageAfter->freeWaterVolume . '</td>
                <td>' . $ullageAfter->grossObservedVolume . '</td>
                <td>' . $ullageAfter->VCF54B15Centigrade . '</td>
                <td>' . $ullageAfter->GSV15Centigrade . '</td>
                <td>' . $ullageAfter->VCF60B20Centigrade . '</td>
                <td>' . $ullageAfter->GSV20Centigrade . '</td>
                </tr>

                ';
            $obsvdTemp += ($ullageAfter->observedTemperature / count($ullageAfterDischarging));
            $obsvdVol += $ullageAfter->totalObservedVolume;
            $freeWtr += $ullageAfter->freeWater;
            $freeWtrVol += $ullageAfter->freeWaterVolume;
            $grossObsvdVol += $ullageAfter->grossObservedVolume;
            $VCF54B += $ullageAfter->VCF54B15Centigrade;
            $GSV15C += $ullageAfter->GSV15Centigrade;
            $VCF60B += $ullageAfter->VCF60B20Centigrade;
            $GSV20C += $ullageAfter->GSV20Centigrade;

        }
        $totalB4Discharging15 = $totalVolumeUllage_15 * $WCFT_15;
        $totalB4Discharging20 = $totalVolumeUllage_20 * $WCFT_20;

        $totalAfterDischarging15 = $GSV15C * $WCFT_15;
        $totalAfterDischarging20 = $GSV20C * $WCFT_20;

        $totalQuantityDischarged15 = $totalB4Discharging15 - $totalAfterDischarging15;
        $totalQuantityDischarged20 = $totalB4Discharging20 - $totalAfterDischarging20;
        $volume15 = $totalQuantityDischarged15 / $WCFT_15;
        $volume20 = $totalQuantityDischarged20 / $WCFT_20;

        $page .= $this->computeColumn($obsvdTemp, $obsvdVol, $freeWtr, $freeWtrVol, $grossObsvdVol, $VCF54B, $GSV15C, $VCF60B, $GSV20C) . '

                </tbody>

            </table>
        </div>



        ' . $this->standardTemperature($totalAfterDischarging15, $totalAfterDischarging20, $densityAt15, $densityAt20, $WCFT_15, $WCFT_20, $totalB4Discharging15, $totalB4Discharging20, $totalQuantityDischarged15, $totalQuantityDischarged20, $volume15, $volume20, $GSV15C, $GSV20C) . '
        .' . documentFooter($ullageAfter->first_name, $ullageAfter->last_name, $shipInfo->captain) . '

';

        $dompdf->loadHtml($page);
// (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->set_option('isRemoteEnabled', true);

// Render the HTML as PDF
        $dompdf->render();

// Output the generated PDF to Browser
        $dompdf->stream($shipInfo->ship_name . 'Ullage After Discharging' . ".pdf", array("Attachment" => 1));
    }
}