<?php

namespace App\Libraries;

use DateTime;
use stdClass;
use DateInterval;
use App\Models\VtcModel;
use App\Models\BillModel;
use App\Models\CustomerModel;
use App\Libraries\ArrayLibrary;

class VtvLibrary
{
    public function groupCompartments4($array, $key): array
    {

        $result = new stdClass();
        // $result = [];

        foreach ($array as $val) {
            if (isset($key, $val)) {
                $data = $val->$key;
                $result->$data[] = $val;
            } else {
                $data = [''];
                $result->$data[] = $val;
            }
        }


        return (array)$result;
    }
    public function groupCompartments($array, $key): array
    {


        // $arrayLength = count($array);

        // // Check if the array has an odd or even number of elements
        // $isOdd = ($arrayLength % 2 !== 0);

        // // Calculate the index where the modification should start
        // $modifyIndex = ceil($arrayLength / 2);

        // // Iterate through the array and modify 'compartment_number'
        // foreach ($array as $index => $item) {
        //     if ($isOdd && $index === $modifyIndex) {
        //         $item->compartment_number = 'Compartment__1';
        //     } elseif ($index >= $modifyIndex) {
        //         $item->compartment_number = 'Compartment__1';
        //     }
        // }

        $result = new stdClass();
        // $result = [];

        foreach ($array as $val) {
            if (isset($key, $val)) {
                $data = $val->$key;
                $result->$data[] = $val;
            } else {
                $data = [''];
                $result->$data[] = $val;
            }
        }


        return (array)$result;
    }


    


    public function formatCompartmentData($compartments, $oilCompartments)
    {



        $vtcModal = new VtcModel();
        $customerModal = new CustomerModel();
        $vehicleId = $compartments[0]->vehicle_id;
        $vehicle = $vtcModal->getVehicleDetails(['id' => $vehicleId]);

        $customer = $customerModal->selectCustomer($vehicle->hash)->name;
        $verificationDate = $vehicle->registration_date;
        $nextVerification = $vehicle->next_calibration;
        $plateNumber = $vehicle->trailer_plate_number;
        $requiredCompartments = $vehicle->compartments;

        $chartNumber = $vehicle->number;
        $controlNumber = $vehicle->controlNumber;
        $waitingPayment = $vehicle->waiting;

        $downloadLimit = $vehicle->downloadLimit;
        // $downloadLimit = '2023-10-03';

        $today = new DateTime();
        $deadline = new DateTime($downloadLimit);

        $options = [
            'GfsCode' => setting('Gfs.vtv'),
            'SUBSTRING(BillItemRef, 1, LENGTH(BillItemRef) - 33)' => $vehicleId
        ];

        $billItem = (new BillModel())->verifyInstrument($options);
        $payment = (new BillModel())->verifyPayment($controlNumber);
        $requesting =  false;
        $download = false;

        if ($today < $deadline && $payment) {
            $requesting =  false;
            $download = true;
        } else if ($today > $deadline && $controlNumber != '' && !empty($billItem)) {

            if ($payment) {
                $today->add(new DateInterval('PT30M'));
                $limit = $today->format('Y-m-d H:i:s');
                $vtcModal->updateChartIfo($chartNumber, [
                    'waiting' => 0,
                    'controlNumber' => '',
                    'downloadLimit' => $limit
                ]);

                $requesting = false;
                $download = true;
            } else {
                $requesting = true;
                $download = false;
            }
        } else {
            $requesting = false;
        }



        $data = new ArrayLibrary((array)$compartments);
        $compNo =  array_unique($data->map(fn ($c) => $c->compartment_number)->get());
        $filledCompartments = $this->groupCompartments((array)$compartments, 'compartment_number');

        // return $filledCompartments;
        // exit;
        $tankTop = [];

        $order = range(1, $oilCompartments);
        $emptying = $order;
        rsort($emptying);

        $fillOrder = '';
        $emptyOrder = '';
        $labels = [];
        $stamps = [];
        $compTags = [];



        for ($i = 0; $i < $oilCompartments; $i++) {
            $fillOrder .= $order[$i] . ',';
            $emptyOrder .= $emptying[$i] . ',';
            array_push($labels, 'COMPT NO. ' . $order[$i]);
            array_push($tankTop, array_values($filledCompartments)[$i][0]->tank_top);
            array_push($stamps, array_values($filledCompartments)[$i][0]->stamp_number);
            array_push($compTags, array_values($filledCompartments)[$i][0]->compartment_number);
        }
        $htmlData = array_map(
            fn ($d) =>
            array_map(
                function ($x) {

                    $litres = (float)$x->litres;
                    $millimeters = (float)$x->millimeters;
                    return <<<HTML
                    <tr>
                        <td class="text-1">$litres</td>
                        <td class="text-1">$millimeters</td>
                       
                    </tr>
                    
             HTML;
                },
                $vtcModal->getCompartmentData($d, $vehicleId)
            ),
            $compNo
        );


        $measurementData = array_map(
            fn ($data) =>
            array_map(
                function ($x) {
                    return [
                        // 'compartmentNumber' =>$x->compartment_number,
                        'litres' => (float)$x->litres,
                        'millimeters' => (float)$x->millimeters,
                    ];
                },
                $vtcModal->getCompartmentData($data,  $vehicleId)
            ),
            $compNo
        );

        $measurements = array_values($measurementData);


        [$tableData0, $tableData1, $tableData2, $tableData3, $tableData4, $tableData5, $tableData6, $tableData7, $tableData8,$tableData9,$tableData10] = '';
        [$maxLitres0, $maxLitres1, $maxLitres2, $maxLitres3, $maxLitres4, $maxLitres5, $maxLitres6, $maxLitres7, $maxLitres8,$maxLitres9,$maxLitres10] = [[], [], [], [], [], [], [], [], [],[],[]];
        [$maxMm0, $maxMm1, $maxMm2, $maxMm3, $maxMm4, $maxMm5, $maxMm6, $maxMm7, $maxMm8,$maxMm9,$maxMm10] = [[], [], [], [], [], [], [], [], [],[],[]];
        $htmlArr = array_values($htmlData);
        $column = $oilCompartments == 3 ? 4 : ($oilCompartments == 4 ? 3 : ($oilCompartments == 2 ? 6 : ($oilCompartments >= 6 ? 2 : 2)));

        //=================get total of each column====================
        for ($i = 0; $i < count($measurements); $i++) {
            foreach ($measurements[$i] as $measurement) {

                for ($c = 0; $c < $oilCompartments; $c++) {
                    if ($c == $i) {

                        array_push(${'maxLitres' . $c}, $measurement['litres']);
                        array_push(${'maxMm' . $c}, $measurement['millimeters']);
                    }
                }
            }
        }


        for ($i = 0; $i < count($htmlArr); $i++) {
            foreach ($htmlArr[$i] as $table) {

                for ($c = 0; $c < $oilCompartments; $c++) {
                    if ($c == $i) ${'tableData' . $c} .= $table;
                }
            }
        }

        $htmlUpperChart = '';
        $htmlLowerChart = '';


        $capacity = 0;
        for ($j = 0; $j < $oilCompartments; $j++) {
            $tank = $tankTop[$j];
            $litres = max(${'maxLitres' . $j});
            $capacity += max(${'maxLitres' . $j});
            $millimeters = max(${'maxMm' . $j});
            $label = $labels[$j];
            $stamp = 'STAMP: ' . $stamps[$j];

            if (${'tableData' . $j} != '')

                $htmlLowerChart .= <<<"HTML"
                    <td class="col-md-$column">
                         $label<br>
                         $litres LITRE <br> 
                         $stamp <br>
                    </td>
                      
             HTML;

            $editable = true;

            if ($editable) {
                $editButton = <<<"HTML"
                 <tr>
                     <th colspan="2"><button type="button" onclick="editCompartment('$vehicleId','$compTags[$j]')" class="btn btn-primary btn-xs " style="float:right;"> <i class="fal fa-edit"></i></button></th>
                </tr>
              HTML;
            } else {
                $editButton = '';
            }

            $td = ${'tableData' . $j};
            $htmlUpperChart .=  <<<HTML
             <td class="col-md-$column text-sm text-center">
                 <h5 class="text-sm col-md-10"><b>T.T $tank mm</b></h5>
               
                <table class="table table-bordered table-sm" >
                   <thead>
                   $editButton
                     <tr>
                        <th>Volume(l)</th>
                        <th>Height(mm)</th>
                    </tr>
                   </thead>
                   <tbody>
                     $td
                   </tbody>

                   
                </table>
             </td>
             
             HTML;
        }
        $link = base_url('downloadCalibrationChart/' . $vehicleId);
        $requestLink = base_url("renewChart/$vehicle->hash/$chartNumber/$plateNumber");



        //  $button = '<button class="btn btn-sm btn-default"><i class="far fa-clock"></i> Chart Pending For Payment</button>'; 
        $chartIsComplete = $requiredCompartments == $oilCompartments ? true : false;

        if ($requesting && $chartIsComplete) {
            $button = <<<HTML
             <a href="$requestLink" class="btn btn-primary btn-sm" target="_blank"> <i class="far fa-undo"></i> Request Chart</a>
            HTML;
        } else if ($download && $chartIsComplete) {
            $button = <<<HTML
             <a href="$link" class="btn btn-primary btn-sm" target="_blank"> <i class="far fa-download"></i> Download Chart</a>
            HTML;
        } else if (!$chartIsComplete) {
            $button = '';
        }
        $button = <<<HTML
        <a href="$link" class="btn btn-primary btn-sm" target="_blank"> <i class="far fa-download"></i> Download Chart</a>
       HTML;

        return (object)[
            'link' => '',
            'button' =>  $button,
            'complete' => $requiredCompartments == $oilCompartments ? true : false,
            'chartNumber' => $chartNumber,
            'customer' => $customer,
            'verificationDate' => dateFormatter($verificationDate),
            'nextVerification' => dateFormatter($nextVerification),
            'plateNumber' => 'TANK/TR NO ' . $plateNumber,
            'capacity' => $capacity,
            'upperChart' => $htmlUpperChart,
            'lowerChart' => $htmlLowerChart,
            'fillOrder' => rtrim($fillOrder, ','),
            'emptyOrder' => rtrim($emptyOrder, ','),
        ];
        // return $htmlLowerChart;
    }
}
