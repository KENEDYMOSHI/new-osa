<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>Pressure Log</title>
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

    #logOfFact table {
        margin: 0 auto;
        width: 100%;
        border-collapse: collapse;

    }

    #logOfFact table td {
        padding: 13px 12px;
    }

    #logOfFact table th {
        padding: 13px 2px;
    }

    .details {
        width: 100%;
        margin-bottom: 15px;
    }

    .form-check {
        transform: scale(1.7);
        color: #333;
    }

    .check-label {
        font-size: 16px;
    }

    .top {
        margin-bottom: 20px;
    }

    .top .checkWrapper {
        /* float: left; */
        margin-top: 5px
    }

    .text-center {
        text-align: center;
        margin-bottom: 5px;
    }
    </style>
</head>



<body>
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
    <p class="contacts"> Tel: <?=$log[0]->phone_number?> Fax: <?=$log[0]->fax?> ,<?=$log[0]->postal_address?>, e-mail:
        <?=$log[0]->email?></p>
    <div class="wrapper">
        <table>
            <tr>
                <td style="padding-right: 15px;"> Vessel Name: <b><?=$log[0]->ship_name?></b></td>
                <td style="padding-right: 15px;"> Cargo Name: <b><?=$log[0]->cargo?></b></td>
                <td style="padding-right: 15px;"> Port Name: <b><?=$log[0]->terminal?></b></td>
            </tr>
        </table>

        <h3>PRESSURE LOG</h3>
    </div>
    <div class="wrapper" id="logOfFact">
        <?php $index = 1;?>
        <table border="1">
            <thead>
                <tr>

                    <th scope="col">#</th>
                    <th scope="col">DATE</th>
                    <th scope="col">TIME</th>
                    <th scope="col">PRESSURE</th>
                    <th scope="col">RATE</th>

                </tr>
            </thead>
            <tbody>

                <?php

$pressureTotal = 0;
$rateTotal = 0;

?>

                <?php foreach ($log as $pressureLog): ?>

                <?php
$pressureTotal += $pressureLog->pressure;
$rateTotal += $pressureLog->rate;
$quantity = count($log);
?>
                <tr>
                    <td><?=$index++?></td>
                    <td><?=dateFormatter($pressureLog->date)?></td>
                    <td><?=$pressureLog->time?></td>
                    <td><?=$pressureLog->pressure?></td>
                    <td><?=$pressureLog->rate?></td>
                </tr>
                <?php endforeach;?>

                <tr>
                    <th colspan="3">
                        AVERAGE
                    </th>
                    <th> <?=round(($pressureTotal / $quantity), 2)?></th>
                    <th> <?=round(($rateTotal / $quantity), 2)?></th>


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
                <td><b><?=$log[0]->captain?></b></td>
                <td><b><?=$log[0]->first_name . ' ' . $log[0]->last_name?></b></td>
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