<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title></title>
    <style>
        * {
            /* padding: 0;
        margin: 0; */
            box-sizing: border-box;
            font-family: sans-serif;
        }

        header,
        section,
        aside {
            margin: 0 1.5% 10px 1.5%;
        }

        section {
            float: left;
            width: 30%;
        }


        .contacts {
            clear: both;
            margin-bottom: 0;
        }

        .left,
        .right {
            width: 20%;
        }

        .right img {
            float: right;
        }

        .middle {
            width: 50%;
        }

        .headings {
            /* margin-top: 10px; */
            text-align: center;
            line-height: 1;
        }

        .headings h5 {
            margin: 5px;
        }

        .center {
            text-align: center;
        }

        .contacts {
            width: 100%;



        }

        .contacts p {
            margin: 0;
            padding: 0;
            margin-top: -30px;
        }

        .contacts h5 {
            margin: 0;
            padding: 0;
            /* margin-top: -30px; */
        }

        .report {
            width: 100%;
            /* margin-top: 60px; */
        }

        .mainTable {
            width: 100%;
            border-collapse: collapse;
            color: #222;
            font-size: 12px;

        }

        .mainTable td {
            padding: 3px;
            text-align: left;
            border-bottom: 1px solid #333;
        }

        .mainTable th {
            padding: 5px;
            text-align: left;
            background: #e6e6e6;
        }


        .summary table {

            width: 40%;
            position: absolute;
            right: 0;
            border-collapse: collapse;
            color: #222;
            font-size: 14px;
        }


        .summary table td {
            padding: 3px;
            text-align: left;
            border-bottom: 1px solid #333;

        }
    </style>
</head>

<body>
    <!-- <header>
        <code>&#60;header&#62;</code>
        <?= base_url() ?>/
    </header> -->

    <header>
        <section class="left">
            <img src='<?= getImage('assets/images/emblem.png') ?>' alt="">
        </section>

        <section class="middle">
            <div class="headings">
                <h5><b>THE UNITED REPUBLIC OF TANZANIA</b></h5>
                <h5><b>MINISTRY OF INDUSTRY AND TRADE </b></h5>
                <h5>WEIGHTS AND MEASURES AGENCY </h5>
            </div>
        </section>

        <section class="right">
            <img src='<?= getImage('assets/images/wma1.png') ?>' alt="">
        </section>
    </header>
    <div class="contacts">
        <p class="center"><?= $contacts ?></p>

    </div>

    <section class="report">
        <h3 class="center"><?= $reportTitle . ' In ' . ($collectionRegion) ?></h3>
        <table class="mainTable" border="0">
            <thead>
                <tr>
                    <th>Activity</th>
                    <th>Total Amount</th>
                    <th>Paid</th>
                    <th>Pending</th>
                    <th>Total Items</th>
                    <th>Items Paid</th>
                    <th>Items Pending</th>

                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Vehicle Tank Verification</td>
                    <td>Tsh <?= $allActivities['vtc']['totalVtc'] ?></td>
                    <td>Tsh <?= $allActivities['vtc']['paidVtc'] ?></td>
                    <td>Tsh <?= $allActivities['vtc']['pendingVtc'] ?></td>
                    <td><?= $allActivities['vtc']['vtcQuantity'] ?> Vehicle(s)</td>
                    <td><?= $allActivities['vtc']['vtcPaidQuantity'] ?> Vehicle(s)</td>
                    <td><?= $allActivities['vtc']['vtcPendingQuantity'] ?> Vehicle(s)</td>
                </tr>

                <tr>

                    <td>Sandy & Ballast Lorries</td>
                    <td>Tsh <?= $allActivities['sbl']['totalSbl'] ?></td>
                    <td>Tsh <?= $allActivities['sbl']['paidSbl'] ?></td>
                    <td>Tsh <?= $allActivities['sbl']['pendingSbl'] ?></td>
                    <td><?= $allActivities['sbl']['sblQuantity'] ?> vehicle(s)</td>
                    <td><?= $allActivities['sbl']['sblPaidQuantity'] ?> vehicle(s)</td>
                    <td><?= $allActivities['sbl']['sblPendingQuantity'] ?> vehicle(s)</td>
                </tr>
                <tr>

                    <td>Meters</td>
                    <td>Tsh <?= $allActivities['waterMeter']['totalWaterMeter'] ?></td>
                    <td>Tsh <?= $allActivities['waterMeter']['paidWaterMeter'] ?></td>
                    <td>Tsh <?= $allActivities['waterMeter']['pendingWaterMeter'] ?></td>
                    <td><?= $allActivities['waterMeter']['waterMeterQuantity'] ?> Meters(s)</td>
                    <td><?= $allActivities['waterMeter']['waterMeterPaidQuantity'] ?> Meter(s)</td>
                    <td><?= $allActivities['waterMeter']['waterMeterPendingQuantity'] ?> Meter(s)</td>
                </tr>
                <tr>

                    <td>Pre Package</td>
                    <td>Tsh <?= number_format($allActivities['prePackage']['totalPrePackage']) ?></td>
                    <td>Tsh <?= number_format($allActivities['prePackage']['paidPrePackage']) ?></td>
                    <td>Tsh <?= number_format($allActivities['prePackage']['pendingPrePackage'])?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>


            </tbody>
            <tfoot></tfoot>
        </table>

        <?php
        $totalCollection = stringToInteger($allActivities['vtc']['totalVtc']) + stringToInteger($allActivities['sbl']['totalSbl']) + stringToInteger($allActivities['waterMeter']['totalWaterMeter']) + $allActivities['prePackage']['totalPrePackage'];

        $totalPaid = stringToInteger($allActivities['vtc']['paidVtc']) + stringToInteger($allActivities['sbl']['paidSbl']) + stringToInteger($allActivities['waterMeter']['paidWaterMeter']) + $allActivities['prePackage']['paidPrePackage'];

        $totalPending = stringToInteger($allActivities['vtc']['pendingVtc']) + stringToInteger($allActivities['sbl']['pendingSbl']) + stringToInteger($allActivities['waterMeter']['pendingWaterMeter']) + $allActivities['prePackage']['pendingPrePackage'];
        ?>

        <div class="summary">

            <table border="0">
                <tr>
                    <td>
                        <h3>Collection Summary</h3>
                    </td>
                    <td></td>

                </tr>
                <tr>
                    <td><b>Paid Amount</b></td>
                    <td> Tsh <?= number_format($totalPaid) ?></td>
                </tr>
                <tr>
                    <td><b>Pending Amount</b></td>
                    <td> Tsh <?= number_format($totalPending) ?></td>
                </tr>
                <tr>
                    <td><b>Total Amount</b></td>
                    <td> Tsh <?= number_format($totalCollection) ?></td>
                </tr>
            </table>
        </div>


    </section>