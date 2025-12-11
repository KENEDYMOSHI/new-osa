<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>Discharge Order Analysis</title>
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
        padding: 12px 12px;
    }

    #logOfFact table th {
        padding: 12px 2px;
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
    <p class="contacts"> Tel: <?=$dischargeOrders[0]->phone_number?> Fax: <?=$dischargeOrders[0]->fax?>
        ,<?=$dischargeOrders[0]->postal_address?>, e-mail:
        <?=$dischargeOrders[0]->email?></p>
    <div class="wrapper">
        <table style="width:100%">
            <tr>
                <td>Vessel Name: <b><?=$dischargeOrders[0]->port_name?></b></td>
                <td>Cargo Name: <b><?=$dischargeOrders[0]->cargo?></b></td>
                <td>Port Name: <b><?=$dischargeOrders[0]->port_name?></b></td>
                <td>Terminal Name: <b><?=$dischargeOrders[0]->terminal?></b></td>
                <td>Arrival Date: <b><?=dateFormatter($dischargeOrders[0]->arrival_date)?></b></td>
            </tr>
        </table>

        <h3>DISCHARGE ORDER ANALYSIS</h3>
    </div>
    <div class="wrapper" id="logOfFact">
        <?php $index = 1;?>
        <table border="1">
            <thead>
                <tr>
                    <th colspan="3"></th>
                    <th>B/L QUANTITY : <?=$billOfLading?> </th>
                    <th>ARRIVAL QTY @20&deg;C : <?=$arrivalQuantity?></th>
                </tr>
                <tr>

                    <th scope="col">#</th>
                    <th scope="col">RECEIVING TERMINAL</th>
                    <th scope="col">RECEIVER / OWNER</th>
                    <th scope="col">QUANTITY</th>
                    <th scope="col">DESTINATION</th>

                </tr>
            </thead>
            <tbody>

                <?php

$quantityTotal = 0;

?>

                <?php foreach ($dischargeOrders as $dischargeOrder): ?>

                <?php
$quantityTotal += $dischargeOrder->quantity;
?>
                <tr>
                    <td><?=$index++?></td>
                    <td><?=dateFormatter($dischargeOrder->receiving_terminal)?></td>
                    <td><?=$dischargeOrder->receiver?></td>
                    <td><?=$dischargeOrder->quantity?></td>
                    <td><?=$dischargeOrder->destination?></td>
                </tr>
                <?php endforeach;?>

                <tr>
                    <th colspan="3">
                        SUBTOTAL
                    </th>
                    <th> <?=round($quantityTotal, 3)?></th>
                    <th></th>


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
                <td><b><?=$dischargeOrders[0]->captain?></b></td>
                <td><b><?=$dischargeOrders[0]->first_name . ' ' . $dischargeOrders[0]->last_name?></b></td>
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