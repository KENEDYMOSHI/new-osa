<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>Line Displacement</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: sans-serif;
        font-size: 12px;
    }

    header {
        position: relative;
    }

    .wrapper {
        width: 90%;
        margin: 20px 40px;
        top: 0;

    }

    .logo-left {
        position: absolute;
        left: 50px;
    }

    .logo-right {
        position: absolute;
        right: 50px;
        top: 0;
    }

    .headings {
        text-align: center;
        line-height: 2;
        color: #3a3a3a;
    }



    .contacts {
        text-align: center;
    }

    .time {
        width: 100%;
        text-align: center;
    }

    .doc-footer {
        width: 100%;
        text-align: center;
    }

    h3 {
        text-align: center;
        text-decoration: underline;
    }

    #lineOfFact table {
        margin: 0 auto;
        width: 100%;
        border-collapse: collapse;

    }

    #lineOfFact table td {
        padding: 13px;
    }

    th {
        padding: 13px;
    }

    .details {
        width: 100%;
        margin-bottom: 15px;
    }
    </style>
</head>


<body>
    <pre>
<?php
// print_r($line);
// exit;
?>
</pre>
    <header>
        <div class="wrapper">
            <div class="logo-left">
                <img src='data:image/jpeg;base64,<?=coatOfArm()?>' alt="">
            </div>
            <div class="headings">
                <h5><b>THE UNITED REPUBLIC OF TANZANIA</b></h5>
                <h5><b>MINISTRY OF INDUSTRY AND TRADE </b></h5>
                <h5>WEIGHTS AND MEASURES AGENCY </h5>

            </div>
            <div class="logo-right">
                <img src='data:image/jpeg;base64,<?=wmaLogo()?>' alt="">
            </div>
        </div>

    </header>
    <p class="contacts"> Tel: <?=$line[0]->phone_number?> Fax: <?=$line[0]->fax?> ,<?=$line[0]->postal_address?>,
        e-mail:
        <?=$line[0]->email?></p>
    <div class="wrapper">
        <table class="details">

            <tr>
                <td>Vessel Name: <b><?=$line[0]->ship_name?></b> </td>

                <td>Port Name: <b><?=$line[0]->port_name?></b> </td>
                <td>Terminal Name: <b><?=$line[0]->terminal?></b> </td>

                <td>Cargo Name: <b><?=$line[0]->cargo?></b> </td>

                <td>Arrival Date: <b><?=dateFormatter($line[0]->arrival_date)?></b> </td>
                <!-- <td>Time: <b>12:30 Hours</b> </td> -->
            </tr>


        </table>

        <h3>LINE DISPLACEMENT</h3>
    </div>
    <!--  -->
    <div class="wrapper" id="lineOfFact">
        <table class="table" border="1">
            <thead class="">
                <tr>
                    <th colspan="9" class="text-center">LINE DISPLACEMENT DIFFERENCES</th>
                </tr>
                <tr>
                    <th rowspan="2">Terminal</th>
                    <th colspan="2">SHIP FIGURE</th>
                    <th colspan="2">SHORE FIGURE</th>
                    <th colspan="2">DIFFERENCE</th>
                    <th colspan="2">DIFFERENCE</th>
                </tr>
                <tr>
                    <th colspan="2">Discharged Quantity</th>
                    <th colspan="2">Received Quantity</th>
                    <th colspan="2">Received Qty Vs Discharged Qty</th>
                    <th colspan="2">Received Qty Vs Discharged Qty</th>
                </tr>
                <tr>
                    <th></th>
                    <th>M/TONS</th>
                    <th>VOL @ 20&deg; C</th>
                    <th>M/TONS</th>
                    <th>VOL @ 20&deg; C</th>

                    <th>M/TONS</th>
                    <th>% DIFF</th>

                    <th>VOL @ 20&deg; C</th>
                    <th>% DIFF</th>

                </tr>
            </thead>

            <tbody id="lineDetails">
                <?php
$shipMetricTonsTotal = 0;
$shipVolumeTotal = 0;
$shoreMetricTonsTotal = 0;
$shoreVolumeTotal = 0;

$metricTonsDifferenceTotal = 0;
$volumeDifferenceTotal = 0;

?>

                <?php foreach ($line as $data): ?>

                <?php
$shipMetricTonsTotal += $data->ship_metric_tons;
$shipVolumeTotal += $data->ship_volume;
$shoreMetricTonsTotal += $data->shore_metric_tons;
$shoreVolumeTotal += $data->shore_volume;

$metricTonsDifferenceTotal += $data->metric_tons_difference;
$volumeDifferenceTotal += $data->volume_difference;

?>
                <tr>
                    <td><?=$data->terminal?></td>
                    <td><?=$data->ship_metric_tons?></td>
                    <td><?=$data->ship_volume?></td>
                    <td><?=$data->shore_metric_tons?></td>
                    <td><?=$data->shore_volume?></td>
                    <td><?=$data->metric_tons_difference?></td>
                    <td><?=$data->metric_tons_percentage . ' %'?></td>
                    <td><?=$data->volume_difference?></td>
                    <td><?=$data->volume_percentage . ' %'?></td>

                </tr>
                <?php endforeach;?>
                <tr>
                    <td></td>
                    <td><?=number_format($shipMetricTonsTotal)?></td>
                    <td><?=number_format($shipVolumeTotal)?>'</td>
                    <td><?=number_format($shoreMetricTonsTotal)?>'</td>
                    <td><?=number_format($shoreVolumeTotal)?></td>
                    <td><?=number_format($shoreMetricTonsTotal - $shipMetricTonsTotal)?>'</td>
                    <td><?=round(($metricTonsDifferenceTotal / $shipMetricTonsTotal) * 100, 2) . ' %'?></td>
                    <td><?=number_format($volumeDifferenceTotal)?></td>
                    <td><?=round(($volumeDifferenceTotal / $shipVolumeTotal) * 100, 2) . ' %'?></td>
                </tr>

            </tbody>



        </table>

    </div>


    <div class="wrapper">
        <table class="doc-footer">
            <tr>
                <th>CAPTAIN/CHIEF OFFICER </th>
                <th>WEIGHTS AND MEASURES OFFICER </th>
            <tr>
                <td><b><?=$line[0]->captain?></b></td>
                <td><b><?=$line[0]->first_name . ' ' . $line[0]->last_name?></b></td>
            </tr>
            <tr>
                <td>
                    <p>Signature & Stamp</p>
                    <br>
                    ......................
                </td>
                <td>
                    <p>Signature & Stamp</p>
                    <br>
                    ......................
                </td>
            </tr>
            </tr>

        </table>
    </div>

</body>

</html>