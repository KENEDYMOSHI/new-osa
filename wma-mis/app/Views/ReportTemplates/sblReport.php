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

    table {
        font-size: 12px;
    }

    table td {
        padding: 3px;
    }

    .mainTable {
        width: 100%;
        border-collapse: collapse;
        color: #222;
        /* font-size: 14px; */

    }

    .mainTable td {
        /* padding: 7px; */
        text-align: left;
        border-bottom: 1px solid #333;
    }

    .mainTable th {
        padding: 10px;
        text-align: left;
        background: #e6e6e6;
    }


    .summary table {

        width: 40%;
        position: absolute;
        right: 0;
        border-collapse: collapse;
        color: #222;

    }


    .summary table td {
        /* padding: 5px; */
        text-align: left;
        border-bottom: 1px solid #333;

    }
    </style>

</head>

<body>
   
<header>
        <section class="left">
            <img src='data:image/jpeg;base64,<?= getImage('assets/images/emblem.png') ?>' alt="">
        </section>

        <section class="middle">
            <div class="headings">
                <h5><b>THE UNITED REPUBLIC OF TANZANIA</b></h5>
                <h5><b>MINISTRY OF INDUSTRY AND TRADE </b></h5>
                <h5>WEIGHTS AND MEASURES AGENCY </h5>
            </div>
        </section>

        <section class="right">
            <img src='data:image/jpeg;base64,<?= getImage('assets/images/wma1.png') ?>' alt="">
        </section>
    </header>
    <div class="contacts">
        <p class="center"><?=$contacts?></p>

    </div>
    <div class="report">
        <h3 class="center"><?=$reportTitle . ' In ' . ($collectionRegion)?></h3>
        <table class="mainTable">
            <thead class="thead-light">
                <tr>
                    <th>Owner Name</th>
                    <th>Phone Number</th>
                    <th>Vehicle Brand</th>
                    <th>Plate Number</th>
                    <th>Capacity</th>
                    <th>Amount</th>
                    <th>Control Number</th>
                    <th>Payment Status</th>

                </tr>
            </thead>
            <tbody>
                <?php foreach ($sblClients as $client): ?>

                <tr>
                    <td><?=ucwords($client->name) ?></td>
                    <td><?=$client->phone_number?></td>
                    <td><?=$client->vehicle_brand?></td>
                    <td><?=$client->plate_number?></td>
                    <td><?=$client->capacity?> m<sup>3</sup></td>
                    <td><?=number_format($client->vehicle_amount)?></td>
                    <td><?=$client->control_number?></td>
                    <td><?=$client->payment?></td>


                </tr>
                <?php endforeach;?>



            </tbody>
            <tfoot>
                <tr>

                </tr>
            </tfoot>
        </table>
        <br>
        <div class="summary">

            <table>
                <tr>
                    <td style="padding: 0;"><b>
                            <h4>Collection Summary</h4>
                        </b></td>
                    <td style="padding: 0;"></td>
                    <td style="padding: 0;"></td>

                </tr>

                <?php if ($sblSummary['paidSbl'] > 0 && $sblSummary['pendingSbl'] > 0): ?>
                <tr>
                    <td><b>Paid Amount</b></td>
                    <td>Tsh <?=$sblSummary['paidSbl']?></td>
                    <td><?=$sblSummary['sblPaidQuantity']?> Vehicle(s)</td>

                </tr>
                <tr>
                    <td><b>Pending Amount</b></td>
                    <td>Tsh <?=$sblSummary['pendingSbl']?></td>
                    <td><?=$sblSummary['sblPendingQuantity']?> Vehicle(s)</td>
                </tr>
                <tr>
                    <td><b>Total Amount</b></td>
                    <td>Tsh <?=$sblSummary['totalSbl']?></td>
                    <td><?=$sblSummary['sblQuantity']?> Vehicle(s)</td>
                </tr>

                <?php elseif ($sblSummary['paidSbl'] > 0): ?>
                <tr>
                    <td><b>Paid Amount</b></td>
                    <td>Tsh <?=$sblSummary['paidSbl']?></td>
                    <td><?=$sblSummary['sblPaidQuantity']?> Vehicle(s)</td>

                </tr>
                <?php elseif ($sblSummary['pendingSbl'] > 0): ?>
                <tr>
                    <td><b>Pending Amount</b></td>
                    <td>Tsh <?=$sblSummary['pendingSbl']?></td>
                    <td><?=$sblSummary['sblPendingQuantity']?> Vehicle(s)</td>
                </tr>

                <?php endif;?>




            </table>
        </div>
    </div>






</body>

</html>